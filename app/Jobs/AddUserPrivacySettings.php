<?php

namespace App\Jobs;

use App\Models\UserPrivacySettings;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddUserPrivacySettings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * AddUserPrivacySettings constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param UserPrivacySettings $userPrivacySettings
     * @return bool
     */
    public function handle(UserPrivacySettings $userPrivacySettings)
    {
        $userPrivacySettings->user_id = $this->user->id;

        return $userPrivacySettings->save();
    }
}
