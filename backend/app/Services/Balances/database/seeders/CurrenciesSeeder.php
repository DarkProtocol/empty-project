<?php

declare(strict_types=1);

namespace App\Services\Balances\Database\Seeders;

use App\Data\Models\Currency;
use Illuminate\Database\Seeder;
use Throwable;

class CurrenciesSeeder extends Seeder
{
    /** @var array<array<string, mixed>> */
    protected array $currencies = [
        [
            'name' => 'Рубль',
            'symbol' => 'RUB',
            'decimals' => 2,
        ],
    ];

    /**
     * Run the database seeders.
     *
     * @return void
     * @throws Throwable
     */
    public function run()
    {
        foreach ($this->currencies as $c) {
            if (!$currency = Currency::query()->where('symbol', $c['symbol'])->first()) {
                $currency = new Currency();
            }

            $currency->fill($c);
            $currency->saveOrFail();
        }
    }
}
