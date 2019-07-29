<?php
declare(strict_types=1);

namespace Tests;

use Artisan;
use DB;
use Illuminate\Support\Facades\Config;

trait TestHelper
{
    protected function getTables()
    {
        return [
            'admins',
            'guests',
            'guest_details',
            'password_resets',
            'reservations',
            'rooms',
            'settings',
        ];
    }

    protected function dropTables()
    {
        collect($this->getTables())->each(function ($table) {
            DB::statement('DROP TABLE IF EXISTS `'.$table.'`');
        });
    }

    protected function truncateTables()
    {
        $this->dropTables();
        $this->runMigrate();
    }

    protected function runMigrate()
    {
        Artisan::call('migrate');
    }

    protected function runSeed()
    {
        Artisan::call('db:seed');
    }
}