<?php

namespace App\Mail;

use App\Models\HubInvite;
use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FriendAcceptedHubInvitation extends Mailable
{
    use Queueable, SerializesModels;

    protected $invite;

    /**
     * FriendAcceptedHubInvitation constructor.
     * @param HubInvite $invite
     */
    public function __construct(HubInvite $invite)
    {
        $this->invite = $invite;
    }

    /**
     * @param EmailTemplatesRepositoryInterface $emailTemplatesRepository
     * @return FriendAcceptedHubInvitation
     */
    public function build(
        EmailTemplatesRepositoryInterface $emailTemplatesRepository
    ) {
        $emailTemplate = $emailTemplatesRepository->getHubInvitationEmailTemplate();

        $html = str_replace([
            '[USER]',
            '[UNAME]',
            '[HUB]',
            '[STATUS]'
        ], [
            optional($this->invite->inviter)->name,
            $this->invite->invitedUser->name,
            $this->invite->hub->title,
            'accepted'
        ], $emailTemplate->content);

        return $this->view('emails.hub.accepted-invite', [
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
