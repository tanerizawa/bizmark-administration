<?php

namespace App\Modules\Email\Providers;

use App\Modules\Shared\Providers\ModuleServiceProvider;

class EmailServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'Email';

    protected string $moduleNamespace = 'App\Modules\Email';
}
