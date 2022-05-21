<?php

namespace App\Mail;

use App\Models\Settings;
use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use App\Repositories\Contracts\SettingsRepositoryInterface;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccountActivation extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new message instance.
     *
     * AccountActivation constructor.
     * @param User $user
     */
    public function __construct(User $user) {
        $this->user = $user;
    }

    /**
     * @param SettingsRepositoryInterface $settingsRepository
     * @param EmailTemplatesRepositoryInterface $emailTemplatesRepository
     * @return AccountActivation
     */
    public function build(
        SettingsRepositoryInterface $settingsRepository,
        EmailTemplatesRepositoryInterface $emailTemplatesRepository
    ) {
        $settings = $settingsRepository->getSiteSettings();
        $emailTemplate = $emailTemplatesRepository->getUserActivationEmailTemplate();

        $html = str_replace([
            '[USER]',
            '[EMAIL]',
            '[LINK]'
        ], [
            $this->user->name,
            $this->user->email,
            "{$settings->account_acctivation_link}/" . base64_encode($this->user->id)
        ], $emailTemplate->content);
        return $this->view('emails.user.account-activation', [
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
