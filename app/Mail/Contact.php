<?php

namespace App\Mail;

use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Contact as ContactModel;

class Contact extends Mailable
{
    use Queueable, SerializesModels;

    protected $contact;

    /**
     * Contact constructor.
     * @param ContactModel $contact
     */
    public function __construct(ContactModel $contact)
    {
        $this->contact = $contact;
    }

    /**
     * @param EmailTemplatesRepositoryInterface $emailTemplatesRepository
     * @return Contact
     */
    public function build(
        EmailTemplatesRepositoryInterface $emailTemplatesRepository
    ) {
        $emailTemplate = $emailTemplatesRepository->getContactUsEmailTemplate();

        $html = str_replace([
            '[USER]',
            '[SUBJECT]',
            '[MSG]',
            '[EMAIL]'
        ], [
            $this->contact->name,
            $this->contact->subject,
            $this->contact->message,
            $this->contact->email
        ], $emailTemplate->content);
        return $this->view('emails.general.contact-us', [
            'html' => str_replace([
                '[UNSUBSCRIBE]', '[SUPPORT]', '[SHOWBROWSER]', '[SITEHOME]', '[TODAYDATE]', '[LOGINLINK]', '[FBLINK]',
                '[LNLINK]', '[TWLINK]', '[EMAIL_SETTINGS]'
            ], [
                config('constants.urls.unsubscribe') . "/{$this->contact->email}", config('constants.urls.support'), '',
                config('constants.urls.home'), Carbon::now()->format('Y'),
                config('constants.urls.home') . '/signin', config('constants.urls.facebook'),
                config('constants.urls.linkedIn'), config('constants.urls.twitter'),
                config('constants.urls.home') . '/user/edit_details'
            ], $html)
        ])
            ->subject($emailTemplate->subject);
    }
}
