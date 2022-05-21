<?php

namespace App\Policies;

use App\Models\EmailNotificationSettings;
use App\Models\UserPrivacySettings;
use App\Repositories\Contracts\EmailNotificationSettingsRepositoryInterface;
use App\Repositories\Contracts\FriendsRepositoryInterface;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAvatar(User $viewer, User $user)
    {
        if ($user->privacySettings) {
            $friendsRepository = app(FriendsRepositoryInterface::class);

            if ($user->privacySettings->profile_picture_picture == UserPrivacySettings::PROFILE_PICTURE_VISIBLE_FOR_CONNECTIONS && $friendsRepository->getByUserIdAndFriendId($user->id, $viewer->id)) {
                return true;
            }

            if ($user->privacySettings->profile_picture_picture == UserPrivacySettings::PROFILE_PICTURE_VISIBLE_FOR_ALL) {
                return true;
            }

            if ($viewer->id == $user->id) {
                return true;
            }

            return false;
        }

        return true;
    }

    public function viewFullName(User $viewer, User $user)
    {
        if ($viewer->id == $user->id) {
            return true;
        }

        if (!$user->privacySettings) {
            return false;
        }
        if ($user->privacySettings && $user->privacySettings->name_visible != UserPrivacySettings::FULL_NAME_VISIBLE) {
            return false;
        }

        return true;
    }

    public function viewDateOfBirth(User $viewer, User $user)
    {
        if ($viewer->id == $user->id) {
            return true;
        }

        if ($user->privacySettings) {
            if ($user->privacySettings->dob_visible == UserPrivacySettings::DOB_VISIBLE_FOR_ALL) {
                return true;
            }

            $friendsRepository = app(FriendsRepositoryInterface::class);
            if ($user->privacySettings->dob_visible == UserPrivacySettings::DOB_VISIBLE_FOR_CONNECTIONS && $friendsRepository->getByUserIdAndFriendId($user->id, $viewer->id)) {
                return true;
            }
        }

        return false;
    }

    public function viewEmail(User $viewer, User $user)
    {
        if ($viewer->id == $user->id) {
            return true;
        }

        if ($user->privacySettings) {
            if ($user->privacySettings->email_visible == UserPrivacySettings::EMAIL_VISIBLE_FOR_ALL) {
                return true;
            }

            $friendsRepository = app(FriendsRepositoryInterface::class);
            if ($user->privacySettings->email_visible == UserPrivacySettings::EMAIL_VISIBLE_FOR_CONNECTIONS && $friendsRepository->getByUserIdAndFriendId($user->id, $viewer->id)) {
                return true;
            }
        }

        return false;
    }

    public function viewMobileNumber(User $viewer, User $user)
    {
        if ($viewer->id == $user->id) {
            return true;
        }

        if ($user->privacySettings) {
            if ($user->privacySettings->phone_visible == UserPrivacySettings::PHONE_VISIBLE_FOR_ALL) {
                return true;
            }

            $friendsRepository = app(FriendsRepositoryInterface::class);
            if ($user->privacySettings->phone_visible == UserPrivacySettings::PHONE_VISIBLE_FOR_CONNECTIONS && $friendsRepository->getByUserIdAndFriendId($user->id, $viewer->id)) {
                return true;
            }
        }

        return false;
    }

    public function viewGroups(User $viewer, User $user)
    {
        if ($viewer->id == $user->id) {
            return true;
        }

        if ($user->privacySettings) {
            if ($user->privacySettings->group_visible == UserPrivacySettings::GROUPS_VISIBLE_FOR_ALL) {
                return true;
            }

            $friendsRepository = app(FriendsRepositoryInterface::class);
            if ($user->privacySettings->group_visible == UserPrivacySettings::GROUPS_VISIBLE_FOR_CONNECTION && $friendsRepository->getByUserIdAndFriendId($user->id, $viewer->id)) {
                return true;
            }
        }

        return false;
    }

    public function viewHubs(User $viewer, User $user)
    {
        if ($viewer->id == $user->id) {
            return true;
        }

        if ($user->privacySettings) {
            if ($user->privacySettings->hub_visible == UserPrivacySettings::HUBS_VISIBLE_FOR_ALL) {
                return true;
            }

            $friendsRepository = app(FriendsRepositoryInterface::class);
            if ($user->privacySettings->hub_visible == UserPrivacySettings::HUBS_VISIBLE_FOR_CONNECTIONS && $friendsRepository->getByUserIdAndFriendId($user->id, $viewer->id)) {
                return true;
            }
        }

        return false;
    }

    public function viewPhotos(User $viewer, User $user)
    {
        if ($viewer->id == $user->id) {
            return true;
        }

        if ($user->privacySettings) {
            if ($user->privacySettings->photo_visible == UserPrivacySettings::PHOTOS_VISIBLE_FOR_ALL) {
                return true;
            }

            $friendsRepository = app(FriendsRepositoryInterface::class);
            if ($user->privacySettings->photo_visible == UserPrivacySettings::PHOTOS_VISIBLE_FOR_CONNECTIONS && $friendsRepository->getByUserIdAndFriendId($user->id, $viewer->id)) {
                return true;
            }
        }


        return false;
    }

    public function viewConnectionsList(User $viewer, User $user)
    {
        if ($viewer->id == $user->id) {
            return true;
        }

        if ($user->privacySettings) {
            if ($user->privacySettings->see_connection_list == UserPrivacySettings::CONNECTIONS_LIST_VISIBLE_FOR_ALL) {
                return true;
            }

            $friendsRepository = app(FriendsRepositoryInterface::class);
            if (
                $user->privacySettings->see_connection_list == UserPrivacySettings::CONNECTIONS_LIST_VISIBLE_FOR_CONNECTIONS
                && $friendsRepository->getByUserIdAndFriendId($user->id, $viewer->id)
            ) {
                return true;
            }
        }

        return false;
    }

    public function viewActivity(User $viewer, User $user)
    {
        if ($viewer->id == $user->id) {
            return true;
        }

        if ($user->privacySettings && $user->privacySettings->allow_connetion == UserPrivacySettings::ALLOW_CONNECTIONS_VIEW_ACTIVITY) {
            return true;
        }


        return false;
    }

    public function receiveConnectionRequestEmailNotification(User $viewer, User $user)
    {
        if (!$user->emailNotificationsSettings || $user->emailNotificationsSettings->isEmpty()) {
            return false;
        }

        $emailNotificationsSettingsRepository = app(EmailNotificationSettingsRepositoryInterface::class);
        $emailNotificationsSetting = $emailNotificationsSettingsRepository->getByKeyword(EmailNotificationSettings::KEY_CONNECTION_REQUEST);

        if ($user->emailNotificationsSettings->contains('setting_id', $emailNotificationsSetting->id)) {
            return true;
        }

        return false;
    }

    public function receivePostCommentEmailNotification(User $viewer, User $user)
    {
        if (!$user->emailNotificationsSettings || $user->emailNotificationsSettings->isEmpty()) {
            return false;
        }

        $emailNotificationsSettingsRepository = app(EmailNotificationSettingsRepositoryInterface::class);
        $emailNotificationsSetting = $emailNotificationsSettingsRepository->getByKeyword(EmailNotificationSettings::KEY_POST_COMMENT);

        if ($user->emailNotificationsSettings->contains('setting_id', $emailNotificationsSetting->id)) {
            return true;
        }

        return false;
    }

    public function receiveGroupPostCommentEmailNotification(User $viewer, User $user)
    {
        if (!$user->emailNotificationsSettings || $user->emailNotificationsSettings->isEmpty()) {
            return false;
        }

        $emailNotificationsSettingsRepository = app(EmailNotificationSettingsRepositoryInterface::class);
        $emailNotificationsSetting = $emailNotificationsSettingsRepository->getByKeyword(EmailNotificationSettings::KEY_GROUP_POST_COMMENT);

        if ($user->emailNotificationsSettings->contains('setting_id', $emailNotificationsSetting->id)) {
            return true;
        }

        return false;
    }

    public function receiveHubInvitationEmailNotification(User $viewer, User $user)
    {
        if (!$user->emailNotificationsSettings || $user->emailNotificationsSettings->isEmpty()) {
            return false;
        }

        $emailNotificationsSettingsRepository = app(EmailNotificationSettingsRepositoryInterface::class);
        $emailNotificationsSetting = $emailNotificationsSettingsRepository->getByKeyword(EmailNotificationSettings::KEY_HUB_INVITATION);

        if ($user->emailNotificationsSettings->contains('setting_id', $emailNotificationsSetting->id)) {
            return true;
        }

        return false;
    }

    public function viewOutgoingFriendsRequests(User $viewer, User $user)
    {
        return $viewer->id == $user->id;
    }

    public function receivePushNotifications(User $viewer, User $user)
    {
        return $user->oneSignalPlayers->isNotEmpty();
    }
}
