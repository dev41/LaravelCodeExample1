<?php

namespace App\Mail;

use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Feedback;

class AdminFeedback extends Mailable
{
    use Queueable, SerializesModels;

    public $feedback;

    /**
     * AdminFeedback constructor.
     * @param Feedback $feedback
     */
    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    /**
     * @param EmailTemplatesRepositoryInterface $emailTemplatesRepository
     * @return AdminFeedback
     */
    public function build(
        EmailTemplatesRepositoryInterface $emailTemplatesRepository
    ) {
        $emailTemplate = $emailTemplatesRepository->getAdminFeedbackNotificationEmailTemplate();

        $subject = str_replace([
            '[FEEDBACK_ID]'
        ], [
            $this->feedback->id
        ], $emailTemplate->subject);

        $html = str_replace([
            '[EMAIL]',
            '[FEEDBACK]',
            '[CONTACT_STATUS]'
        ], [
            $this->feedback->email,
            $this->feedback->description,
            $this->feedback->feedback == Feedback::CONTACT_ABOUT_FEEDBACK ? 'Yes' : 'No'
        ], $emailTemplate->content);

        return $this->view('emails.admin.feedback', [
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
            ->subject($subject);
    }
}
