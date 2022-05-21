<?php

namespace App\Repositories;

use App\Models\Chat;
use App\Models\Message;
use App\Models\UserPrivacySettings;
use App\Repositories\Contracts\ChatsRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChatsRepository implements ChatsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(Chat $model)
    {
        $this->model = $model;
    }

    public function getByUsersIds(int $firstUserId, int $secondUserId)
    {
        return $this->model
            ->where(function ($query) use ($firstUserId, $secondUserId) {
                $query->where([
                    'from_id' => $firstUserId,
                    'to_id' => $secondUserId
                ])
                    ->orWhere(function ($query) use ($firstUserId, $secondUserId) {
                        $query->where(['from_id' => $secondUserId])
                            ->where(['to_id' => $firstUserId]);
                    });
            })
            ->first();
    }

    public function getAllUserActiveChats(User $user, array $conditions)
    {
        $query = $this->model
            ->leftJoin('messages', 'chats.id', '=', 'messages.chat_id')
            ->select([
                'chats.*',
                DB::raw("(SELECT created_at FROM messages WHERE messages.chat_id=chats.id AND (messages.deleted_by<>{$user->id} OR messages.deleted_by IS NULL) ORDER BY messages.created_at DESC LIMIT 1) AS last_message_created_date")
            ])
            ->where(function ($query) use ($user) {
                $query->where(['chats.from_id' => $user->id])
                    ->orWhere(['chats.to_id' => $user->id]);
            })
            ->where(function ($query) use ($user) {
                $query->where('chats.deleted_by', '<>', $user->id)
                    ->orWhereNull('chats.deleted_by');
            })
            ->whereExists(function ($query) use ($user) {
                $query->select('id')
                    ->from('messages')
                    ->whereRaw('messages.chat_id=chats.id')
                    ->where(function ($query) use ($user) {
                        $query->where('messages.deleted_by', '<>', $user->id)
                            ->orWhereNull('messages.deleted_by');
                    })
                    ->where(function ($query) use ($user) {
                        $query->where('messages.system', Message::NOT_SYSTEM)
                            ->orWhereNull('messages.system');
                    });
            });

        if (isset($conditions['lastCreatedDate'])) {
            $query->whereRaw("(SELECT created_at FROM messages WHERE messages.chat_id=chats.id AND (messages.deleted_by<>{$user->id} OR messages.deleted_by IS NULL) ORDER BY messages.created_at DESC LIMIT 1)<'{$conditions['lastCreatedDate']}'");
        }

        if (isset($conditions['search'])) {
            $query->whereExists(function ($query) use ($user, $conditions) {
                $query->select('users.id')
                    ->from('users')
                    ->join('user_privacy_settings', 'users.id', '=', 'user_privacy_settings.user_id')
                    ->where(function ($query) use ($conditions) {
                        $query->whereRaw('chats.from_id=users.id')
                            ->orWhereRaw('chats.to_id=users.id');
                    })
                    ->where('users.id', '<>', $user->id)
                    ->where(function ($query) use ($conditions) {
                        $query->where(function ($query) use ($conditions) {
                            $query->where('users.name', 'like', "%{$conditions['search']}%")
                                ->where('user_privacy_settings.name_visible', UserPrivacySettings::FULL_NAME_VISIBLE);
                        })
                        ->orWhere(function ($query) use ($conditions) {
                            $query->where('users.first_name', 'like', "%{$conditions['search']}%")
                                ->where('user_privacy_settings.name_visible', UserPrivacySettings::FIRST_NAME_ONLY_VISIBLE);
                        });
                    });
            });
        }

        return $query->orderBy('last_message_created_date', 'DESC')
            ->groupBy('chats.id')
            ->distinct()
            ->paginate($conditions['limit']);
    }

    public function getUserNotViewedMessages(Chat $chat, User $user)
    {
        return $chat->messages()
            ->where('user_id', '<>', $user->id)
            ->where(function ($query) {
                $query->whereNull('is_read')
                    ->orWhere('is_read', Message::IS_NOT_READ);
            })
            ->get();
    }

    public function setUserMessagesAsRead(Chat $chat, User $user)
    {
        return $chat->messages()
            ->where('user_id', '<>', $user->id)
            ->where(function ($query) {
                $query->whereNull('is_read')
                    ->orWhere('is_read', Message::IS_NOT_READ);
            })
            ->update([
                'is_read' => Message::IS_READ
            ]);
    }

    public function getUserUnreadChatsCount(int $userId)
    {
        return $this->model
            ->select('chats.*')
            ->join('messages', 'chats.id', '=', 'messages.chat_id')
            ->where(function ($query) use ($userId) {
                $query->where('chats.from_id', $userId)
                    ->orWhere('chats.to_id', $userId);
            })
            ->where('messages.user_id', '<>', $userId)
            ->where(function ($query) use ($userId) {
                $query->whereNull('chats.deleted_by')
                    ->orWhere('chats.deleted_by', '<>', $userId);
            })
            ->where(function ($query) {
                $query->whereNull('messages.system')
                    ->orWhere('messages.system', Message::NOT_SYSTEM);
            })
            ->where(function ($query) use ($userId) {
                $query->whereNull('messages.deleted_by')
                    ->orWhere('messages.deleted_by', '<>', $userId);
            })
            ->where([
                'messages.is_read' => Message::IS_NOT_READ,
            ])
            ->groupBy('messages.chat_id')
            ->distinct()
            ->get()
            ->count();
    }

    public function getAllUserChats(User $user)
    {
        return $this->model
            ->where('from_id', $user->id)
            ->orWhere('to_id', $user->id)
            ->get();
    }

    public function getChatMessages(Chat $chat, array $conditions)
    {
        $query = $chat->messages()
            ->where(function ($query) use ($conditions) {
                $query->whereNull('deleted_by')
                    ->orWhere('deleted_by', '<>', $conditions['user_id']);
            });

        if ($query->count() <= 1 && optional($query->first())->system == Message::SYSTEM) {
            $query->where('system', '<>', Message::SYSTEM);
        }

        if (isset($conditions['last_message_created_date'])) {
            $query->where('created_at', '<', $conditions['last_message_created_date']);
        }

        return $query->latest()->paginate($conditions['limit']);
    }
}