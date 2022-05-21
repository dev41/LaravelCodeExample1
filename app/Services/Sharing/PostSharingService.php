<?php

namespace App\Services\Sharing;

use App\Contracts\Sharing\ShareableInterface;
use App\Helpers\AzureBlob;
use App\Models\Post;
use App\Repositories\Contracts\PostsRepositoryInterface;

class PostSharingService implements ShareableInterface {
    private $postsRepository;
    private $generalSharingService;

    public function __construct(
        PostsRepositoryInterface $postsRepository,
        GeneralSharingService $generalSharingService
    ) {
        $this->postsRepository = $postsRepository;
        $this->generalSharingService = $generalSharingService;
    }

    public function getData(string $permalink) : array
    {
        if ($post = $this->postsRepository->getByKey(base64_decode($permalink))) {
            if ($post->images && $post->images->first()) {
                $image = AzureBlob::url(config('constants.files.posts_path') . "{$post->id}/original/{$post->images->first()->file_name}");
            }

            if ($sharingData = $post->links()->first()) {
                if ($sharingData->image && $sharingData->image != '') {
                    $image = $sharingData->image;
                    if (stripos($image, 'http://') !== false || stripos($image, 'https://') !== false) {
                        $fileNameArray = explode('/', $image);
                        $image = end($fileNameArray);
                    }
                    $image = AzureBlob::url(config('constants.files.shared_images_path') . "original/{$image}");
                }

                if ($post->description == '' || $post->description == $sharingData->url) {
                    $description = $sharingData->title;
                }
            }

            return [
                'type' => 'post',
                'title' => $post->post_type == Post::TYPE_ANONYMOUS ? Post::ANONYMOUS_USER_TITLE : $post->user->name,
                'description' => $description ?? $post->description,
                'image' => $image ?? ''
            ];
        }

        return $this->generalSharingService->getData($permalink);
    }
}