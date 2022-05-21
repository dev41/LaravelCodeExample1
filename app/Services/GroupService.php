<?php

namespace App\Services;

use App\Contracts\Services\ServiceInterface;
use App\Helpers\AzureBlob;
use App\Models\Group;
use App\Models\GroupRequest;
use App\Repositories\Contracts\GroupConnectionsRepositoryInterface;
use App\Repositories\Contracts\GroupRequestsRepositoryInterface;
use App\Repositories\Contracts\GroupsRepositoryInterface;
use App\User;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class GroupService implements ServiceInterface {
    private $groupConnectionsRepository;
    private $groupRequestsRepository;
    private $imagesSizes;

    public function __construct(
        GroupConnectionsRepositoryInterface $groupConnectionsRepository,
        GroupRequestsRepositoryInterface $groupRequestsRepository
    ) {
        $this->groupConnectionsRepository = $groupConnectionsRepository;
        $this->groupRequestsRepository = $groupRequestsRepository;

        $this->imagesSizes = [
            'small' => [
                'width' => 200
            ],
            'medium' => [
                'width' => 400
            ]
        ];
    }

    public function getUserMemberType(Group $group, User $user)
    {
        if ($groupConnection = $this->groupConnectionsRepository->getByGroupIdAndUserId($group->id, $user->id)) {
            if ($group->user_id == $user->id || $group->admins->contains('user_id', $user->id)) {
                return Group::TYPE_ADMIN;
            }

            return Group::TYPE_MEMBER;
        } else {
            if ($groupRequest = $this->groupRequestsRepository->getByGroupIdAndUserId($group->id, $user->id)) {
                if (!$groupRequest->invited_by && in_array($groupRequest->request_type, [0, GroupRequest::TYPE_REQUEST_PENDING])) {
                    return Group::TYPE_SEND_REQUEST;
                }

                if ($groupRequest->invited_by && in_array($groupRequest->request_type, [0, GroupRequest::TYPE_REQUEST_PENDING])) {
                    return Group::TYPE_INVITED;
                }
            }

            return Group::TYPE_NOT_MEMBER;
        }
    }

    public function generatePermalink(string $title)
    {
        $groupsRepository = app(GroupsRepositoryInterface::class);
        $permalink = str_slug($title);

        if ($group = $groupsRepository->getBySlug($permalink)) {
            $permalink = str_slug($permalink . ' ' . str_random(7));
        }

        return $permalink;
    }

    public function storeImage($image, int $objectId)
    {
        $imageParts = explode(";base64,", $image);
        $imageExtension = explode("image/", $imageParts[0])[1];
        $imageBase64 = base64_decode($imageParts[1]);

        $imageName = uniqid(time()) . '.' . $imageExtension;

        $imagePath = config('constants.files.groups_path') . "$objectId/original";

        $imageTypeArray = explode(';', $image);
        $imageMimeType = str_replace('data:', '', $imageTypeArray[0]);

        Storage::put("$imagePath/$imageName", $imageBase64, [
            'visibility' => 'public',
            'ContentType' => $imageMimeType
        ]);

        $storedFilePath = AzureBlob::url("{$imagePath}/$imageName");

        $imageManager = new ImageManager();
        foreach ($this->imagesSizes as $key => $imagesSize) {
            $img = $imageManager->make($storedFilePath)
                ->resize($imagesSize['width'], null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            Storage::put(config('constants.files.groups_path') . "$objectId/$key/" . $imageName, (string)$img->encode(), [
                'visibility' => 'public',
                'ContentType' => $img->mime()
            ]);
        }

        return $imageName;
    }

    public function getGroupImages($groupId)
    {
        $groupsRepository = app(GroupsRepositoryInterface::class);
        $group = $groupsRepository->getByKey($groupId);

        return $group->getImages();
    }
}