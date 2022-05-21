<?php

namespace App\Repositories\Contracts;

interface EmailTemplatesRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function getUserActivationEmailTemplate();

    public function getUserWelcomeEmailTemplate();

    public function getContactUsEmailTemplate();

    public function getResetPasswordEmailTemplate();

    public function getPostCommentEmailTemplate();

    public function getFeedbackEmailTemplate();

    public function getConnectionEmailTemplate();

    public function getGroupConfirmationEmailTemplate();

    public function getCompanyAdminActivationEmailTemplate();

    public function getReportedPostEmailTemplate();

    public function getReportedCommentEmailTemplate();

    public function getThanksForArticleEmailTemplate();

    public function getNewArticleAddedEmailTemplate();

    public function getHubConfirmationEmailTemplate();

    public function getMentorThanksEmailTemplate();

    public function getJoinTeamEmailTemplate();

    public function getTeamThanksEmailTemplate();

    public function getAdminFeedbackNotificationEmailTemplate();

    public function getPartnerThanksEmailTemplate();

    public function getContactUsSupportThanksEmailTemplate();

    public function getNewsletterSubscriptionEmailTemplate();

    public function getHubInvitationEmailTemplate();

    public function getCloseAccountEmailTemplate();
}