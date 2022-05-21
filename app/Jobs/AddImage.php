<?php

namespace App\Jobs;

use App\Models\Image;
use App\Repositories\Contracts\ImagesRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $image;

    /**
     * AddImage constructor.
     * @param $image
     */
    public function __construct($image)
    {
        $this->image = $image;
    }

    /**
     * @param ImagesRepositoryInterface $imagesRepository
     * @return mixed
     */
    public function handle(ImagesRepositoryInterface $imagesRepository)
    {
        return $imagesRepository->store($this->image);
    }
}
