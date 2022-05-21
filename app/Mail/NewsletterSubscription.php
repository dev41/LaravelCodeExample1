<?php

namespace App\Mail;

use App\Models\Subscriber;
use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewsletterSubscription extends Mailable
{
    use Queueable, SerializesModels;

    protected $subscriber;
    protected $user;

    /**
     * NewsletterSubscription constructor.
     * @param User $user
     * @param Subscriber $subscriber
     */
    public function __construct(User $user, Subscriber $subscriber)
    {
        $this->user = $user;
        $this->subscriber = $subscriber;
    }

    /**
     * @param EmailTemplatesRepositoryInterface $emailTemplatesRepository
     * @return NewsletterSubscription
     */
    public function build(EmailTemplatesRepositoryInterface $emailTemplatesRepository)
    {
        if ($emailTemplate = $emailTemplatesRepository->getNewsletterSubscriptionEmailTemplate()) {

            $html = str_replace([
                '[USER]',
                '[EMAIL]'
            ], [
                $this->user->name,
                $this->subscriber->email
            ], $emailTemplate->content);
            return $this->view('emails.general.newsletter-subscription', [
                'html' => str_replace([
                    '[UNSUBSCRIBE]', '[SUPPORT]', '[SHOWBROWSER]', '[SITEHOME]', '[TODAYDATE]', '[LOGINLINK]', '[FBLINK]',
                    '[LNLINK]', '[TWLINK]', '[EMAIL_SETTINGS]'
                ], [
                    config('constants.urls.unsubscribe') . "/{$emailTemplate->from_email}",
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
