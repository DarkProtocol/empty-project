<?php

declare(strict_types=1);

namespace App\Domains\Auth\Jobs;

use App\Data\Models\AuthorizationToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Lucid\Units\Job;

class RevokeTokensBySessionIdJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @param string $sessionId
     */
    public function __construct(
        protected string $sessionId
    ) {
    }

    /**
     * Execute the job.
     *
     * @param Request $request
     * @return void
     */
    public function handle(): void
    {
        $revocationDate = Carbon::now();

        AuthorizationToken::where('session_id', $this->sessionId)
            ->where('expire_at', '>', $revocationDate)
            ->update([
                'revoked_at' => $revocationDate,
            ]);
    }
}
