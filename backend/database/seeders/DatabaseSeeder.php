<?php

namespace Database\Seeders;

use App\Services\Balances\Database\Seeders\CurrenciesSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CurrenciesSeeder::class);
    }
}
