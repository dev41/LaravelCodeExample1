<?php

namespace App\Mail;

use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use App\Repositories\Contracts\SettingsRepositoryInterface;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $resetPasswordToken;

    /**
     * ResetPassword constructor.
     * @param User $user
     * @param $resetPasswordToken
     */
    public function __construct(User $user, $resetPasswordToken)
    {
        $this->user = $user;
        $this->resetPasswordToken = $resetPasswordToken;
    }

    /**
     * @param SettingsRepositoryInterface $settingsRepository
     * @param EmailTemplatesRepositoryInterface $emailTemplatesRepository
     * @return ResetPassword
     */
    public function build(
        SettingsRepositoryInterface $settingsRepository,
        EmailTemplatesRepositoryInterface $emailTemplatesRepository
    ) {
        $settings = $settingsRepository->getSiteSettings();
        $emailTemplate = $emailTemplatesRepository->getResetPasswordEmailTemplate();

        $html = str_replace([
            '[LINK]'
        ], [
            $settings->forgot_password_url . '/' . $this->resetPasswordToken
        ], $emailTemplate->content);
        return $this->view('emails.user.reset-password', [
            'html' => str_replace([
                '[UNSUBSCRIBE]', '[SUPPORT]', '[SHOWBROWSER]', '[SITEHOME]', '[TODAYDATE]', '[LOGINLINK]', '[FBLINK]',
                '[LNLINK]', '[TWLINK]', '[EMAIL_SETTINGS]'
            ], [
                config('constants.urls.unsubscribe') . "/{$this->user->email}", config('constants.urls.support'), '',
                config('constants.urls.home'), Carbon::now()->format('Y'),
                config('constants.urls.home') . '/signin', config('constants.urls.facebook'),
                config('constants.urls.linkedIn'), config('constants.urls.twitter'),
                config('constants.urls.home') . '/user/edit_details'
            ], $html)
        ])
            ->subject($emailTemplate->subject);
    }
}
