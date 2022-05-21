<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\Post;
use App\Repositories\Contracts\FilesRepositoryInterface;
use App\Repositories\Contracts\ImagesRepositoryInterface;
use App\Repositories\Contracts\NotificationsRepositoryInterface;
use App\Repositories\Contracts\PostCommentsRepositoryInterface;
use App\Repositories\Contracts\PostConnectionsRepositoryInterface;
use App\Repositories\Contracts\PostImagesRepositoryInterface;
use App\Repositories\Contracts\PostLikesRepositoryInterface;
use App\Repositories\Contracts\PostLinksRepositoryInterface;
use App\Services\ImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;

class DestroyPostDependencies implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $postId;

    /**
     * DestroyPostDependencies constructor.
     * @param int $postId
     */
    public function __construct(int $postId)
    {
        $this->postId = $postId;
    }

    /**
     * @param NotificationsRepositoryInterface $notificationsRepository
     * @param PostCommentsRepositoryInterface $postCommentsRepository
     * @param PostLikesRepositoryInterface $postLikesRepository
     * @param PostConnectionsRepositoryInterface $postConnectionsRepository
     * @param PostImagesRepositoryInterface $postImagesRepository
     * @param ImagesRepositoryInterface $imagesRepository
     * @param ImageService $imageService
     * @param FilesRepositoryInterface $filesRepository
     * @param PostLinksRepositoryInterface $postLinksRepository
     * @return bool
     */
    public function handle(
        NotificationsRepositoryInterface $notificationsRepository,
        PostCommentsRepositoryInterface $postCommentsRepository,
        PostLikesRepositoryInterface $postLikesRepository,
        PostConnectionsRepositoryInterface $postConnectionsRepository,
        PostImagesRepositoryInterface $postImagesRepository,
        ImagesRepositoryInterface $imagesRepository,
        ImageService $imageService,
        FilesRepositoryInterface $filesRepository,
        PostLinksRepositoryInterface $postLinksRepository
    ) {
        $postCommentsRepository->destroyAllByPostId($this->postId);
        $postLikesRepository->destroyAllByPostId($this->postId);
        $postConnectionsRepository->destroyAllByPostId($this->postId);
        $postLinksRepository->destroyAllByPostId($this->postId);

        if ($postImages = $postImagesRepository->getAllByPostIdAndVersion($this->postId, 1)) {
            foreach ($postImages as $postImage) {
                foreach ($imageService->postImagesSizes as $size => $postImagesSize) {
                    Storage::delete(config('constants.files.posts_path') . "{$postImage->post_id}/$size/$postImage->image");
                }

                $postImage->delete();
            }
        }

        $imagesRepository->destroyAllByPostId($this->postId);

        $filesRepository->deletePostFiles($this->postId);
        Storage::deleteDirectory(config('constants.files.posts_path') . $this->postId);

        $notificationsRepository->delete([
            'object_id' => $this->postId,
            'type' => Notification::OBJECT_TYPE_POST
        ]);

        return true;
    }
}
