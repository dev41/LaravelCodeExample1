<?php

namespace App\Mail;

use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewFriendRequest extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $friend;

    /**
     * NewFriendRequest constructor.
     * @param User $friend
     * @param User $user
     */
    public function __construct(User $friend, User $user)
    {
        $this->friend = $friend;
        $this->user = $user;
    }

    /**
     * @param EmailTemplatesRepositoryInterface $emailTemplatesRepository
     * @return NewFriendRequest
     */
    public function build(
        EmailTemplatesRepositoryInterface $emailTemplatesRepository
    ) {
        $emailTemplate = $emailTemplatesRepository->getConnectionEmailTemplate();

        $html = str_replace([
            '[USER]',
            '[FRIENDNAME]'
        ], [
            $this->friend->name,
            $this->user->name
        ], $emailTemplate->content);
        return $this->view('emails.friend.new-request', [
            'html' => str_replace([
                '[UNSUBSCRIBE]', '[SUPPORT]', '[SHOWBROWSER]', '[SITEHOME]', '[TODAYDATE]', '[LOGINLINK]', '[FBLINK]',
                '[LNLINK]', '[TWLINK]', '[EMAIL_SETTINGS]'
            ], [
                config('constants.urls.unsubscribe') . "/{$this->friend->email}", config('constants.urls.support'), '',
                config('constants.urls.home'), Carbon::now()->format('Y'),
                config('constants.urls.home') . '/signin', config('constants.urls.facebook'),
                config('constants.urls.linkedIn'), config('constants.urls.twitter'),
                config('constants.urls.home') . '/user/edit_details'
            ], $html)
        ])
            ->subject($emailTemplate->subject);
    }
}
