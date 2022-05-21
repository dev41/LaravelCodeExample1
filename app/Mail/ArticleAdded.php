<?php

namespace App\Mail;

use App\Helpers\AzureBlob;
use App\Models\Magazine;
use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ArticleAdded extends Mailable
{
    use Queueable, SerializesModels;

    protected $article;

    /**
     * ArticleAdded constructor.
     * @param Magazine $article
     */
    public function __construct(Magazine $article)
    {
        $this->article = $article;
    }

    /**
     * @param EmailTemplatesRepositoryInterface $emailTemplatesRepository
     * @return ArticleAdded
     */
    public function build(
        EmailTemplatesRepositoryInterface $emailTemplatesRepository
    ) {
        $emailTemplate = $emailTemplatesRepository->getNewArticleAddedEmailTemplate();

        $subject = str_replace([
            '[NAME]'
        ], [
            $this->article->author_name
        ], $emailTemplate->subject);

        $html = str_replace([
            '[AUTHOR]',
            '[TITLE]',
            '[EMAIL]',
            '[ID]'
        ], [
            $this->article->author_name,
            $this->article->title,
            $this->article->auther_email,
            $this->article->id
        ], $emailTemplate->content);

        if ($this->article->image) {
            $filePath = config('constants.files.banners_path') . $this->article->image;
        } elseif ($this->article->attach_file) {
            $filePath = config('constants.files.banners_path') . $this->article->attach_file;
        }

        $result = $this->view('emails.general.contact-us', [
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

        if (isset($filePath)) {
            $result = $result->attach(AzureBlob::url($filePath));
        }

        return $result;
    }
}
