<?php

namespace App\Models\Traits;

use App\Models\Scopes\CompanyScope;

trait HasCompanyScope
{
    protected static function bootHasCompanyScope(): void
    {
        static::addGlobalScope(new CompanyScope());
    }
}
