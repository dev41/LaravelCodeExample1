<?php

namespace App\Mail;

use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TeamThanks extends Mailable
{
    use Queueable, SerializesModels;

    protected $name;

    /**
     * TeamThanks constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param EmailTemplatesRepositoryInterface $emailTemplatesRepository
     * @return TeamThanks
     */
    public function build(
        EmailTemplatesRepositoryInterface $emailTemplatesRepository
    ) {
        $emailTemplate = $emailTemplatesRepository->getTeamThanksEmailTemplate();

        $html = str_replace([
            '[USER]'
        ], [
            $this->name,
        ], $emailTemplate->content);

        return $this->view('emails.general.feedback', [
            'html' => str_replace([
                '[UNSUBSCRIBE]', '[SUPPORT]', '[SHOWBROWSER]', '[SITEHOME]', '[TODAYDATE]', '[LOGINLINK]', '[FBLINK]',
                '[LNLINK]', '[TWLINK]', '[EMAIL_SETTINGS]'
            ], [
                config('constants.urls.unsubscribe') . "/{$emailTemplate->from_email}",
                config('constants.urls.support'), '', config('constants.urls.home'),
                Carbon::now()->format('Y'), config('constants.urls.home') . '/signin',
                config('constants.urls.facebook'), config('constants.urls.linkedIn'), config('constants.urls.twitter'),
                config('constants.urls.home') . '/user/edit_details'
            ], $html)
        ])
            ->subject($emailTemplate->subject);
    }
}
