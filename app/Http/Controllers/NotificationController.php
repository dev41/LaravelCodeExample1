<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateNotificationRequest;
use App\Models\Notification;
use App\Models\OffNotification;
use App\Notifications\UpdateOneSignalBadge;
use App\Repositories\Contracts\NotificationsRepositoryInterface;
use App\Repositories\Contracts\PostCommentsRepositoryInterface;
use App\Repositories\Contracts\PostLikesRepositoryInterface;
use App\Serializers\AckSerializer;
use App\Transformers\NotificationTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * @SWG\Post(
     *   path="/notifications/{notificationId}/off",
     *   summary="Off notification",
     *   tags={"notifications"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="access token (type Bearer)",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="notificationId",
     *     in="path",
     *     description="Notification id",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Success"
     *   )
     * )
     * @param Notification $notification
     * @param OffNotification $offNotification
     * @param PostLikesRepositoryInterface $postLikesRepository
     * @param PostCommentsRepositoryInterface $postCommentsRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function off(
        Notification $notification,
        OffNotification $offNotification,
        PostLikesRepositoryInterface $postLikesRepository,
        PostCommentsRepositoryInterface $postCommentsRepository
    ) {
        $offNotification->user_id = auth()->id();
        $offNotification->notification_id = $notification->id;
        if ($notification->table_name == 'Posts') {
            $offNotification->post_id = $notification->table_p_id;
        } elseif($notification->table_name =='PostLike') {
            if ($postLike = $postLikesRepository->getByKey($notification->table_p_id)) {
                $offNotification->post_id = $postLike->post_id;
            }
        } elseif ($postComment = $postCommentsRepository->getByKey($notification->table_p_id)) {
            $offNotification->post_id = $postComment->post_id;
        }

        if ($offNotification->save()) {
            return response()->json([
                'Ack' => 1,
                'msg' => 'Notification Off Successfully.',
                'Details' => $offNotification
            ]);
        }

        return response()->json([
            'Ack' => 0,
            'msg' => 'No Such Notification Found.'
        ]);
    }

    /**
     * @SWG\Get(
     *   path="/notifications",
     *   summary="Get user notifications",
     *   tags={"notifications"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="access token (type Bearer)",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page number",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Page size",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="is_read",
     *     in="query",
     *     description="Read status (0 - not read, 1 - read)",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Notifications fetched successfully"
     *   )
     * )
     * @param Request $request
     * @param NotificationsRepositoryInterface $notificationsRepository
     * @param Manager $fractal
     * @param AckSerializer $serializer
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index(
        Request $request,
        NotificationsRepositoryInterface $notificationsRepository,
        Manager $fractal,
        AckSerializer $serializer
    ) {
        if ($request->has('is_read')) {
            $notifications = $notificationsRepository->getByUserAndReadStatus(auth()->user(), $request->input('is_read', Notification::IS_NOT_READ), $request->input('limit', 10));
        } else {
            $notifications = $notificationsRepository->getByUser(auth()->user(), $request->input('limit', 10));
        }

        $resource = new Collection($notifications->getCollection(), new NotificationTransformer());
        $resource->setPaginator(new IlluminatePaginatorAdapter($notifications));
        $resource->setMetaValue('total_unread', $notificationsRepository->getCountByUserAndReadStatus(auth()->user(), Notification::IS_NOT_VIEWED));

        $fractal->setSerializer($serializer);
        $fractal->parseIncludes('object.member_count');
        return $fractal->createData($resource)->toArray();
    }

    /**
     * @SWG\Patch(
     *   path="/notifications/{notificationId}",
     *   summary="Update notification",
     *   tags={"notifications"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="access token (type Bearer)",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="notificationId",
     *     in="path",
     *     description="Notification id",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="is_read",
     *     in="formData",
     *     description="Read notification status",
     *     required=true,
     *     type="integer",
     *     @SWG\Items(
     *      type="integer", enum={"0 - is not read", "1 - is read"}
     *     ),
     *   ),
     *   @SWG\Parameter(
     *     name="is_active",
     *     in="formData",
     *     description="Hide notification status",
     *     required=true,
     *     type="integer",
     *     @SWG\Items(
     *      type="integer", enum={"0 - is not hidden", "1 - is hidden"}
     *     ),
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Notification updated successfully"
     *   )
     * )
     * @param Notification $notification
     * @param UpdateNotificationRequest $request
     * @param Manager $fractal
     * @param AckSerializer $serializer
     * @return array
     */
    public function update(
        Notification $notification,
        UpdateNotificationRequest $request,
        Manager $fractal,
        AckSerializer $serializer
    ) {
        if ($notification->to_id != auth()->id()) {
            return response()->json([
                'Ack' => 0,
                'message' => 'Notifications not found'
            ]);
        }

        if ($request->has('is_read')) {
            if ($request->input('is_read') == Notification::IS_READ && ($notification->is_view == Notification::IS_NOT_VIEWED || !$notification->is_view)) {
                $notification->is_view = Notification::IS_VIEWED;
            }

            $notification->is_read = $request->input('is_read');
        }

        if ($request->has('is_active')) {
            $notification->is_active = $request->input('is_active');
        }

        if (
            $notification->update() &&
            auth()->user()->can('receivePushNotifications', $notification->to)
        ) {
            try {
                $notification->to->notify(new UpdateOneSignalBadge());
            } catch (\Exception $exception) {
                Log::warning($exception);
            }
        }

        $resource = new Item($notification, new NotificationTransformer());

        $fractal->setSerializer($serializer);
        return $fractal->createData($resource)->toArray();
    }

    /**
     * @SWG\Patch(
     *   path="/notifications/mark-all-as-read",
     *   summary="Set all user notifications as read",
     *   tags={"notifications"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="access token (type Bearer)",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Notifications updated successfully"
     *   )
     * )
     * @param NotificationsRepositoryInterface $notificationsRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAll(
        NotificationsRepositoryInterface $notificationsRepository
    ) {
        if ($notificationsRepository->setUserNotificationsAsViewed(auth()->user())) {
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
                'message' => 'Notifications updated successfully!'
            ]);
        }

        return response()->json([
            'Ack' => 0,
            'status' => false,
            'message' => 'Notifications updated successfully!'
        ]);
    }
}
