<?php

namespace App\Mail;

use App\Models\PostComment;
use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReportedComment extends Mailable
{
    use Queueable, SerializesModels;

    protected $comment;
    protected $user;

    /**
     * ReportedComment constructor.
     * @param PostComment $comment
     * @param User $user
     */
    public function __construct(PostComment $comment, User $user)
    {
        $this->comment = $comment;
        $this->user = $user;
    }

    /**
     * @param EmailTemplatesRepositoryInterface $emailTemplatesRepository
     * @return ReportedComment
     */
    public function build(
        EmailTemplatesRepositoryInterface $emailTemplatesRepository
    ) {
        $emailTemplate = $emailTemplatesRepository->getReportedCommentEmailTemplate();

        $html = str_replace([
            '[COMMENTID]',
            '[COMOWN]',
            '[COMMENT]',
            '[COMDT]',
            '[NAME]',
            '[EMAIL]'
        ], [
            $this->comment->id,
            $this->comment->user->name,
            $this->comment->comment,
            Carbon::parse($this->comment->c_date)->format('d-m-Y'),
            $this->user->name,
            $this->user->email
        ], $emailTemplate->content);
        return $this->view('emails.comment.reported', [
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
