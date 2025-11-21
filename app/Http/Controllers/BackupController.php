<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ElectronicDocument;
use App\Models\ElectronicInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{
    public function run(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->company_id) {
            return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
        }

        $validated = $request->validate([
            'retention_years' => 'nullable|integer|min:1',
            'frequency' => 'nullable|in:Diaria,Semanal,Mensual',
            'location' => 'required|in:local,drive',
            'types' => 'required|array',
            'types.*' => 'in:xml,pdf,db'
        ]);

        $companyId = $user->company_id;
        $base = storage_path("app/backups/{$companyId}/" . date('Ymd_His'));
        if (!is_dir($base)) {
            mkdir($base, 0775, true);
        }

        $fromDate = null;
        if (!empty($validated['retention_years'])) {
            $fromDate = now()->subYears((int)$validated['retention_years']);
        }

        if (in_array('xml', $validated['types'])) {
            $xmlDir = "{$base}/xml";
            if (!is_dir($xmlDir)) {
                mkdir($xmlDir, 0775, true);
            }
            $q = ElectronicDocument::whereHas('electronicInvoice.user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
            if ($fromDate) {
                $q->where('created_at', '>=', $fromDate);
            }
            $docs = $q->get();
            foreach ($docs as $doc) {
                $name = $doc->cufe ?: ("doc-" . $doc->id);
                $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $name) . '.xml';
                file_put_contents("{$xmlDir}/{$safe}", $doc->xml_document ?? '');
            }
        }

        if (in_array('pdf', $validated['types'])) {
            $pdfDir = "{$base}/pdf";
            if (!is_dir($pdfDir)) {
                mkdir($pdfDir, 0775, true);
            }
            $qi = ElectronicInvoice::with([
                'user',
                'buyer',
                'invoiceDetails',
                'invoiceDetails.item.taxes',
                'invoiceDetails.item.measurementUnit',
                'electronicDocuments'
            ])->whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
            if ($fromDate) {
                $qi->where('created_at', '>=', $fromDate);
            }
            $invoices = $qi->get();
            foreach ($invoices as $inv) {
                $name = 'invoice-' . ($inv->invoice_number ?? $inv->id);
                $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $name) . '.pdf';
                $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $inv])->setPaper('letter')->output();
                file_put_contents("{$pdfDir}/{$safe}", $pdf);
            }
        }

        $dbIncluded = false;
        if (in_array('db', $validated['types'])) {
            $dbDir = "{$base}/db";
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0775, true);
            }
            $dumpPath = "{$dbDir}/dump.sql";
            $mysqldump = 'c:\xampp\mysql\bin\mysqldump.exe';
            $db = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');
            $dbHost = env('DB_HOST', '127.0.0.1');
            if (is_file($mysqldump)) {
                $cmd = "\"{$mysqldump}\" --user=\"{$dbUser}\" --password=\"{$dbPass}\" --host=\"{$dbHost}\" --routines --triggers \"{$db}\" --result-file=\"{$dumpPath}\"";
                @exec($cmd, $out, $status);
                if ($status === 0 && is_file($dumpPath)) {
                    $dbIncluded = true;
                }
            }
            if (!$dbIncluded) {
                $csvDir = "{$dbDir}/csv";
                if (!is_dir($csvDir)) {
                    mkdir($csvDir, 0775, true);
                }
                $tables = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
                $csvAny = false;
                foreach ($tables as $t) {
                    $arrT = (array) $t;
                    $table = reset($arrT);
                    if (!$table) {
                        continue;
                    }
                    $cols = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?", [$db, $table]);
                    $colNames = array_map(fn($c) => $c->COLUMN_NAME, $cols);
                    $csvPath = "{$csvDir}/{$table}.csv";
                    $fh = fopen($csvPath, 'w');
                    fputcsv($fh, $colNames);
                    $query = DB::table($table);
                    if (in_array('company_id', $colNames)) {
                        $query->where('company_id', $companyId);
                    }
                    if ($fromDate && in_array('created_at', $colNames)) {
                        $query->where('created_at', '>=', $fromDate);
                    }
                    foreach ($query->select('*')->cursor() as $row) {
                        $arr = (array) $row;
                        fputcsv($fh, array_map(fn($h) => $arr[$h] ?? null, $colNames));
                    }
                    fclose($fh);
                    if (filesize($csvPath) > 0) {
                        $csvAny = true;
                    }
                }
                if ($csvAny) {
                    $dbIncluded = true;
                }
            }
        }

        $zipFile = "{$base}/backup.zip";
        $zip = new \ZipArchive();
        $zip->open($zipFile, \ZipArchive::CREATE);
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            if (realpath($file) === realpath($zipFile)) {
                continue;
            }
            $localName = str_replace($base . DIRECTORY_SEPARATOR, '', $file);
            $zip->addFile($file, $localName);
        }
        $zip->close();

        $relative = str_replace(storage_path('app'), '', $zipFile);
        return response()->json([
            'success' => true,
            'message' => 'Respaldo generado',
            'data' => [
                'zip_path' => $relative,
                'db_included' => $dbIncluded
            ]
        ], 201);
    }

    public function download(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->company_id) {
            return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
        }
        $path = $request->query('path');
        if (!$path || !str_starts_with($path, "/backups/{$user->company_id}/")) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        $full = storage_path('app' . $path);
        if (!is_file($full)) {
            return response()->json(['success' => false, 'message' => 'Archivo no existe'], 404);
        }
        $date = date('Ymd', filemtime($full));
        $filename = "backup-{$user->company_id}-{$date}.zip";
        return response()->download($full, $filename);
    }

    public function list(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->company_id) {
            return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
        }
        $baseDir = storage_path('app/backups/' . $user->company_id);
        $items = [];
        if (is_dir($baseDir)) {
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($baseDir, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $file) {
                if ($file->isFile() && strtolower($file->getExtension()) === 'zip') {
                    $full = $file->getPathname();
                    $relative = str_replace(storage_path('app'), '', $full);
                    $items[] = [
                        'path' => $relative,
                        'size' => filesize($full),
                        'modified_at' => date('c', filemtime($full)),
                    ];
                }
            }
        }
        usort($items, fn($a, $b) => strcmp($b['modified_at'], $a['modified_at']));
        return response()->json(['success' => true, 'data' => $items]);
    }
}