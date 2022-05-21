<?php

namespace App\Http\Controllers;

use App\Contracts\Services\ImageServiceInterface;
use App\Events\ChatMessageCreated;
use App\Events\ChatUpdated;
use App\Http\Requests\CreateMessageRequest;
use App\Models\Chat;
use App\Models\File;
use App\Models\Message;
use App\Notifications\NewMessage;
use App\Notifications\UpdateOneSignalBadge;
use App\Repositories\Contracts\ChatsRepositoryInterface;
use App\Repositories\Contracts\FilesRepositoryInterface;
use App\Repositories\MessageLinksRepository;
use App\Serializers\AckSerializer;
use App\Services\MessagesService;
use App\Transformers\MessageTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class MessageController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/chats/{chatId}/relationships/messages",
     *   summary="Fetching list of chat messages",
     *   tags={"chat"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="access token (type Bearer)",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="last_message_created_date",
     *     in="query",
     *     description="Oldest message created date",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Page size",
     *     required=false,
     *     type="integer"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Messages fetched successfully"
     *   )
     * )
     * @param Chat $chat
     * @param Manager $fractal
     * @param ChatsRepositoryInterface $chatsRepository
     * @param Request $request
     * @param AckSerializer $serializer
     * @return array
     */
    public function index(
        Chat $chat,
        Manager $fractal,
        ChatsRepositoryInterface $chatsRepository,
        Request $request,
        AckSerializer $serializer
    ) {
        $conditions = [
            'user_id' => auth()->id(),
            'limit' => $request->input('limit', 10)
        ];

        if ($request->input('last_message_created_date', '')) {
            $conditions['last_message_created_date'] = $request->input('last_message_created_date');
        }

        $paginator = $chatsRepository->getChatMessages($chat, $conditions);
        $messages = $paginator->getCollection()->reverse();
        $resource = new Collection($messages, new MessageTransformer());
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));
        $fractal->setSerializer($serializer);

        return $fractal->createData($resource)->toArray();
    }

    /**
     * @SWG\Post(
     *   path="/chats/{chatId}/relationships/messages",
     *   summary="Create message",
     *   tags={"chat"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="access token (type Bearer)",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="text",
     *     in="formData",
     *     description="Message text",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="files",
     *     in="formData",
     *     description="Message files array",
     *     required=false,
     *     type="file"
     *   ),
     *   @SWG\Parameter(
     *     name="sharing_data",
     *     in="formData",
     *     description="Object with shared link data (title - link title, description - link description, image - link image, url - full link, text - message text if exists)",
     *     required=false,
     *     type="string",
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Message created successfully"
     *   )
     * )
     * @param Chat $chat
     * @param CreateMessageRequest $request
     * @param Message $message
     * @param Manager $fractal
     * @param AckSerializer $serializer
     * @param MessagesService $messagesService
     * @param ImageServiceInterface $imageService
     * @param FilesRepositoryInterface $filesRepository
     * @param MessageLinksRepository $messageLinksRepository
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function store(
        Chat $chat,
        CreateMessageRequest $request,
        Message $message,
        Manager $fractal,
        AckSerializer $serializer,
        MessagesService $messagesService,
        ImageServiceInterface $imageService,
        FilesRepositoryInterface $filesRepository,
        MessageLinksRepository $messageLinksRepository
    ) {
        if (auth()->id() == $chat->from_id || auth()->id() == $chat->to_id) {
            $message->chat_id = $chat->id;
            $message->user_id = auth()->id();
            $message->text = $request->input('text');
            $message->is_read = Message::IS_NOT_READ;

            if ($message->save()) {
                if ($chat->deleted_by) {
                    $chat->deleted_by = null;
                    $chat->update();
                }

                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $file) {
                        $images[] = [
                            'type' => File::TYPE_MESSAGES,
                            'file_name' => $imageService->store($file, $chat->id, $messagesService),
                            'object_id' => $message->id,
                            'user_id' => auth()->id(),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }

                    $filesRepository->insertMultiple($images ?? []);
                }

                if ($request->input('sharing_data', '')) {
                    $sharingData = $request->input('sharing_data');
                    if ($sharingData) {
                        if (isset($sharingData['image']['original'])) {
                            $fileNameArray = explode('/', $sharingData['image']['original']);
                            $image = end($fileNameArray);
                        }
                        $messageLinksRepository->store([
                            'message_id' => $message->id,
                            'url' => isset($sharingData['url']) ? $sharingData['url'] : '',
                            'title' => isset($sharingData['title']) ? $sharingData['title'] : '',
                            'description' => isset($sharingData['description']) ? $sharingData['description'] : '',
                            'image' => $image ?? ''
                        ]);
                    }
                }

                broadcast(new ChatMessageCreated($message))->toOthers();
                event(new ChatUpdated($message->chat));

                try {
                    if ($message->chat->from_id == auth()->id() && auth()->user()->can('receivePushNotifications', $message->chat->toUser)) {
                        $message->chat->toUser->notify(new NewMessage($message));
                    } elseif ($message->chat->to_id == auth()->id() && auth()->user()->can('receivePushNotifications', $message->chat->fromUser)) {
                        $message->chat->fromUser->notify(new NewMessage($message));
                    }
                } catch (\Exception $exception) {
                    Log::warning($exception);
                }

                $resource = new Item($message, new MessageTransformer());
                $fractal->setSerializer($serializer);
                return $fractal->createData($resource)->toArray();
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'An error occurred while performing an action!'
        ], 500);
    }

    /**
     * @SWG\Delete(
     *   path="/chats/{chatId}/relationships/messages/{messageId}",
     *   summary="Delete message",
     *   tags={"chat"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="access token (type Bearer)",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Message deleted successfully"
     *   )
     * )
     * @param Chat $chat
     * @param Message $message
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(
        Chat $chat,
        Message $message
    ) {
        $isProcessed = false;

        if ($message->chat_id == $chat->id && auth()->user()->can('delete', $message)) {
            if ($message->deleted_by && $message->deleted_by != auth()->id()) {
                $isProcessed = $message->delete();
            } else {
                $message->deleted_by = auth()->id();
                $isProcessed = $message->update();
            }
        }

        return response()->json([
            'Ack' => $isProcessed ? 1 : 0,
            'status' => $isProcessed ? true : false,
            'message' => $isProcessed
                ? 'Message deleted successfully!'
                : 'An error occurred while performing an action!'
        ]);
    }

    /**
     * @SWG\Patch(
     *   path="/chats/{chatId}/relationships/messages",
     *   summary="Mark messages as viewed",
     *   tags={"chat"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="access token (type Bearer)",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Messages updated successfully"
     *   ),
     *   @SWG\Response(
     *     response=204,
     *     description="No messages found for update"
     *   )
     * )
     * @param Chat $chat
     * @param ChatsRepositoryInterface $chatsRepository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function update(
        Chat $chat,
        ChatsRepositoryInterface $chatsRepository
    ) {
        if ($chatsRepository->setUserMessagesAsRead($chat, auth()->user())) {
            event(new ChatUpdated($chat));

            if (auth()->user()->can('receivePushNotifications', auth()->user())) {
                try {
                    auth()->user()->notify(new UpdateOneSignalBadge());
                } catch (\Exception $exception) {
                    Log::warning($exception);
                }
            }

            return response()->json([
                'Ack' => 1,
                'status' => true,
                'message' => 'Messages updated successfully!'
            ]);
        }

        return response('', 204);
    }
}
