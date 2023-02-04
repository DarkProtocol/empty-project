<?php

declare(strict_types=1);

namespace App\Domains\Balances\Jobs;

use App\Data\Models\Balance;
use App\Data\Models\Currency;
use Lucid\Units\Job;

class GenerateBalancesByUserIdJob extends Job
{
    public function __construct(
        protected string $userId,
        protected bool $check = false,
    ) {
    }

    public function handle(): void
    {
        $currencies = Currency::get();
        $balances = $this->check ? Balance::where('user_id', $this->userId)->get() : null;

        foreach ($currencies as $currency) {
            /** @var Currency $currency */
            if ($balances !== null && $balances->where('currency_id', $currency->id)->count() > 0) {
                continue;
            }

            $balance = new Balance();
            $balance->user_id = $this->userId;
            $balance->currency_id = $currency->id;
            $balance->value = '0';

            $balance->saveOrFail();
        }
    }
}
