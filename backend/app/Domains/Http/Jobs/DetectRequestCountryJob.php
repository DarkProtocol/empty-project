<?php

declare(strict_types=1);

namespace App\Domains\Http\Jobs;

use Illuminate\Http\Request;
use Lucid\Units\Job;

class DetectRequestCountryJob extends Job
{
    public function __construct(
        protected bool $detectTor = false
    ) {
    }

    /**
     * Execute the job.
     *
     * @param Request $request
     * @return string|null
     */
    public function handle(Request $request): ?string
    {
        $country = $request->header('CF-IPCountry');

        if (!$country || $country === 'XX') {
            // Country does not detected by CloudFlare
            return null;
        }

        if ($country === 'T1' && !$this->detectTor) {
            // Request was made from Tor network
            return null;
        }

        return $country;
    }
}
