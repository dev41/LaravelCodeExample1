<?php

namespace App\Mail;

use App\Models\Post;
use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PostCommentCreated extends Mailable
{
    use Queueable, SerializesModels;

    protected $post;
    protected $user;

    /**
     * PostCommentCreated constructor.
     * @param Post $post
     * @param User $user
     */
    public function __construct(Post $post, User $user)
    {
        $this->post = $post;
        $this->user = $user;
    }

    /**
     * @param EmailTemplatesRepositoryInterface $emailTemplatesRepository
     * @return PostCommentCreated
     */
    public function build(EmailTemplatesRepositoryInterface $emailTemplatesRepository)
    {
        if ($emailTemplate = $emailTemplatesRepository->getPostCommentEmailTemplate()) {

            $html = str_replace([
                '[USER]',
                '[UNAME]'
            ], [
                $this->post->user->name,
                $this->user->name
            ], $emailTemplate->content);
            return $this->view('emails.comment.created', [
                'html' => str_replace([
                    '[UNSUBSCRIBE]', '[SUPPORT]', '[SHOWBROWSER]', '[SITEHOME]', '[TODAYDATE]', '[LOGINLINK]', '[FBLINK]',
                    '[LNLINK]', '[TWLINK]', '[EMAIL_SETTINGS]'
                ], [
                    config('constants.urls.unsubscribe') . "/{$this->post->user->email}",
                    config('constants.urls.support'), '', config('constants.urls.home'),
                    Carbon::now()->format('Y'), config('constants.urls.home') . '/signin',
                    config('constants.urls.facebook'), config('constants.urls.linkedIn'),
                    config('constants.urls.twitter'), config('constants.urls.home') . '/user/edit_details'
                ], $html)
            ])
                ->subject($emailTemplate->subject);
        }
    }
}
