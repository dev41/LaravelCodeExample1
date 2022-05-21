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

class ReportedPost extends Mailable
{
    use Queueable, SerializesModels;

    protected $post;
    protected $user;

    /**
     * ReportedPost constructor.
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
     * @return ReportedPost
     */
    public function build(
        EmailTemplatesRepositoryInterface $emailTemplatesRepository
    ) {
        $emailTemplate = $emailTemplatesRepository->getReportedPostEmailTemplate();

        $html = str_replace([
            '[POSTID]',
            '[POSTOWN]',
            '[POSTOWNEML]',
            '[POST]',
            '[POSTDT]',
            '[NAME]',
            '[EMAIL]'
        ], [
            $this->post->id,
            $this->post->user->name,
            $this->post->user->email,
            $this->post->description,
            Carbon::parse($this->post->created_date)->format('d-m-Y'),
            $this->user->name,
            $this->user->email
        ], $emailTemplate->content);
        return $this->view('emails.post.reported', [
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
