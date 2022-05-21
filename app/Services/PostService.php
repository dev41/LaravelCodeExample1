<?php

namespace App\Services;

use App\Contracts\Services\ServiceInterface;
use App\Helpers\AzureBlob;
use App\Models\Post;
use App\Repositories\Contracts\GroupsRepositoryInterface;
use App\Repositories\Contracts\HubsRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class PostService implements ServiceInterface {
    private $imagesSizes;

    public function __construct()
    {
        $this->imagesSizes = [
            'small' => [
                'width' => 200
            ],
            'medium' => [
                'width' => 400
            ]
        ];
    }

    public $objectsTypes = [
        Post::GROUP_TYPE_GROUP_POST => 'group',
        Post::GROUP_TYPE_HUB_POST => 'hub',
        Post::GROUP_TYPE_ANOTHER_USER => 'user'
    ];

    public $objectsRepositories = [
        Post::GROUP_TYPE_GROUP_POST => GroupsRepositoryInterface::class,
        Post::GROUP_TYPE_HUB_POST => HubsRepositoryInterface::class,
        Post::GROUP_TYPE_ANOTHER_USER => UserRepositoryInterface::class
    ];

    public function generatePermalink(string $permalink)
    {
        return $permalink;
    }

    public function getPostObject($post)
    {
        if ($post->group_id > 0 && isset($this->objectsTypes[$post->group_type])) {
            $data = [
                'type' => $this->objectsTypes[$post->group_type]
            ];

            if ($object = app($this->objectsRepositories[$post->group_type])->getByKey($post->group_id)) {
                switch ($post->group_type) {
                    case Post::GROUP_TYPE_GROUP_POST :
                        $data['title'] = $object->group_name;
                        $data['link'] = $object->group_uname;
                        break;
                    case Post::GROUP_TYPE_HUB_POST :
                        $data['title'] = $object->title;
                        $data['link'] = $object->permalink;
                        break;
                    case Post::GROUP_TYPE_ANOTHER_USER :
                        $data['title'] = $object->name;
                        $data['link'] = $object->display_name;
                        break;
                }

                return $data;
            }
        }

        return new \stdClass();
    }

    public function storeImage($image, int $objectId)
    {
        $imageParts = explode(";base64,", $image);
        $imageExtension = explode("image/", $imageParts[0])[1];
        $imageBase64 = base64_decode($imageParts[1]);

        $imageName = uniqid(time()) . '.' . $imageExtension;

        $imagePath = config('constants.files.posts_path') . "$objectId/original";

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
            Storage::put(config('constants.files.posts_path') . "$objectId/$key/" . $imageName, (string)$img->encode(), [
                'visibility' => 'public',
                'ContentType' => $img->mime()
            ]);
        }

        return $imageName;
    }
}