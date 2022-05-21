<?php

namespace App\Jobs;

use App\Models\EmailNotificationSettings;
use App\Repositories\Contracts\EmailNotificationSettingsRepositoryInterface;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddUserEmailNotificationSettings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param EmailNotificationSettingsRepositoryInterface $emailNotificationSettingsRepository
     */
    public function handle(EmailNotificationSettingsRepositoryInterface $emailNotificationSettingsRepository)
    {
        $emailNotificationSettings = $emailNotificationSettingsRepository->getByStatus(EmailNotificationSettings::ACTIVE);
        $emailNotificationSettingsArray = [];
        if ($emailNotificationSettings->isNotEmpty()) {
            foreach ($emailNotificationSettings as $emailNotificationSetting) {
                $emailNotificationSettingsArray[] = [
                    'setting_id' => $emailNotificationSetting->id
                ];
            }

            $this->user->emailNotificationsSettings()->createMany($emailNotificationSettingsArray);
        }
    }
}
