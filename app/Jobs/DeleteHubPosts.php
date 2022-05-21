<?php

namespace App\Jobs;

use App\Models\Hub;
use App\Repositories\Contracts\PostsRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteHubPosts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $hub;

    /**
     * DeleteHubPosts constructor.
     * @param Hub $hub
     */
    public function __construct(Hub $hub)
    {
        $this->hub = $hub;
    }

    /**
     * @param PostsRepositoryInterface $postsRepository
     * @return bool
     */
    public function handle(PostsRepositoryInterface $postsRepository)
    {
        if ($this->hub->posts->isNotEmpty()) {
            foreach ($this->hub->posts as $post) {
                DestroyPostDependencies::dispatch($post);

                $postsRepository->delete(['id' => $post->id]);
            }
        }

        return true;
    }
}
