<?php

namespace App\Jobs;

use App\Contracts\Services\ImageServiceInterface;
use App\Models\File;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $post;

    /**
     * AddPostJob constructor.
     * @param $post
     */
    public function __construct($post)
    {
        $this->post = $post;
    }

    /**
     * @param ImageServiceInterface $imageService
     * @param PostService $postService
     * @return mixed
     */
    public function handle(
        ImageServiceInterface $imageService,
        PostService $postService
    ) {
        $image = $this->post['file'];
        unset($this->post['file']);

        $post = Post::create($this->post);

        if ($image != '') {
            $imageName = $imageService->storeImage($image, $post->id, $postService);
            AddFile::dispatch([
                'type' => File::TYPE_POSTS,
                'file_name' => $imageName,
                'object_id' => $post->id,
                'user_id' => $post->user->id
            ]);
        }

        return $post;
    }
}
