<?php

namespace App\Jobs;

use App\Models\Hub;
use App\Repositories\Contracts\PostsRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteGroupPosts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $posts;

    /**
     * DeleteGroupPosts constructor.
     * @param $posts
     */
    public function __construct($posts)
    {
        $this->posts = $posts;
    }

    /**
     * @return bool
     */
    public function handle()
    {
        if ($this->posts->isNotEmpty()) {
            foreach ($this->posts as $post) {
                $post->delete();
            }
        }

        return true;
    }
}
