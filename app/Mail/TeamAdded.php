<?php

namespace App\Mail;

use App\Models\Team;
use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TeamAdded extends Mailable
{
    use Queueable, SerializesModels;

    protected $team;

    /**
     * TeamAdded constructor.
     * @param Team $team
     */
    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    /**
     * @param EmailTemplatesRepositoryInterface $emailTemplatesRepository
     * @return TeamAdded
     */
    public function build(
        EmailTemplatesRepositoryInterface $emailTemplatesRepository
    ) {
        $emailTemplate = $emailTemplatesRepository->getJoinTeamEmailTemplate();

        $html = str_replace([
            '[AUTHOR]',
            '[EMAIL]',
            '[NUMBER]',
            '[CERTIFICATION]',
            '[MESSAGE]',
            '[ID]'
        ], [
            $this->team->name,
            $this->team->email,
            $this->team->mobile_no,
            $this->team->qualification,
            $this->team->message,
            $this->team->id
        ], $emailTemplate->content);
        return $this->view('emails.team.created', [
            'html' => str_replace([
                '[UNSUBSCRIBE]', '[SUPPORT]', '[SHOWBROWSER]', '[SITEHOME]', '[TODAYDATE]', '[LOGINLINK]', '[FBLINK]',
                '[LNLINK]', '[TWLINK]', '[EMAIL_SETTINGS]'
            ], [
                config('constants.urls.unsubscribe') . "/{$this->team->email}", config('constants.urls.support'), '',
                config('constants.urls.home'), Carbon::now()->format('Y'),
                config('constants.urls.home') . '/signin', config('constants.urls.facebook'),
                config('constants.urls.linkedIn'), config('constants.urls.twitter'),
                config('constants.urls.home') . '/user/edit_details'
            ], $html)
        ])
            ->subject($emailTemplate->subject);
    }
}
