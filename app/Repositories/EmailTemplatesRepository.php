<?php

namespace App\Repositories;

use App\Models\EmailTemplate;
use App\Models\Settings;
use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use App\Traits\RepositoryTrait;

class EmailTemplatesRepository implements EmailTemplatesRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(EmailTemplate $emailTemplate)
    {
        $this->model = $emailTemplate;
    }

    public function getUserActivationEmailTemplate()
    {
        return $this->model->find(2);
    }

    public function getUserWelcomeEmailTemplate()
    {
        return $this->model->find(3);
    }

    public function getResetPasswordEmailTemplate()
    {
        return $this->model->find(7);
    }

    public function getContactUsEmailTemplate()
    {
        return $this->model->find(8);
    }

    public function getPostCommentEmailTemplate()
    {
        return $this->model->find(36);
    }

    public function getHubConfirmationEmailTemplate()
    {
        return $this->model->find(27);
    }

    public function getFeedbackEmailTemplate()
    {
        return $this->model->find(28);
    }

    public function getConnectionEmailTemplate()
    {
        return $this->model->find(33);
    }

    public function getGroupConfirmationEmailTemplate()
    {
        return $this->model->find(26);
    }

    public function getCompanyAdminActivationEmailTemplate()
    {
        return $this->model->find(40);
    }

    public function getReportedPostEmailTemplate()
    {
        return $this->model->find(25);
    }

    public function getReportedCommentEmailTemplate()
    {
        return $this->model->find(34);
    }

    public function getThanksForArticleEmailTemplate()
    {
        return $this->model->find(32);
    }

    public function getNewArticleAddedEmailTemplate()
    {
        return $this->model->find(38);
    }

    public function getMentorThanksEmailTemplate()
    {
        return $this->model->find(30);
    }

    public function getJoinTeamEmailTemplate()
    {
        return $this->model->find(39);
    }

    public function getTeamThanksEmailTemplate()
    {
        return $this->model->find(29);
    }

    public function getAdminFeedbackNotificationEmailTemplate()
    {
        return $this->model->find(42);
    }

    public function getPartnerThanksEmailTemplate()
    {
        return $this->model->find(31);
    }

    public function getContactUsSupportThanksEmailTemplate()
    {
        return $this->model->find(35);
    }

    public function getNewsletterSubscriptionEmailTemplate()
    {
        return $this->model->find(43);
    }

    public function getHubInvitationEmailTemplate()
    {
        return $this->model->find(37);
    }

    public function getCloseAccountEmailTemplate()
    {
        return $this->model->find(44);
    }
}