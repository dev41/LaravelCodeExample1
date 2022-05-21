<?php

namespace App\Mail;

use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CloseAccount extends Mailable
{
    use Queueable, SerializesModels;

    protected $userName;
    protected $userEmail;

    /**
     * CloseAccount constructor.
     * @param string $userName
     * @param string $userEmail
     */
    public function __construct(string $userName, string $userEmail)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
    }

    /**
     * @param EmailTemplatesRepositoryInterface $emailTemplatesRepository
     * @return CloseAccount
     */
    public function build(
        EmailTemplatesRepositoryInterface $emailTemplatesRepository
    ) {
        $emailTemplate = $emailTemplatesRepository->getCloseAccountEmailTemplate();

        $html = str_replace([
            '[USER]'
        ], [
            $this->userName,
        ], $emailTemplate->content);

        return $this->view('emails.general.close-account', [
            'html' => str_replace([
                '[UNSUBSCRIBE]', '[SUPPORT]', '[SHOWBROWSER]', '[SITEHOME]', '[TODAYDATE]', '[LOGINLINK]', '[FBLINK]',
                '[LNLINK]', '[TWLINK]', '[EMAIL_SETTINGS]'
            ], [
                config('constants.urls.unsubscribe') . "/{$this->userEmail}", config('constants.urls.support'), '',
                config('constants.urls.home'), Carbon::now()->format('Y'),
                config('constants.urls.home') . '/signin', config('constants.urls.facebook'),
                config('constants.urls.linkedIn'), config('constants.urls.twitter'),
                config('constants.urls.home') . '/user/edit_details'
            ], $html)
        ])
            ->subject($emailTemplate->subject);
    }
}
