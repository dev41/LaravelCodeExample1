<?php

namespace App\Jobs;

use App\Repositories\Contracts\FilesRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $file;

    /**
     * AddFile constructor.
     * @param $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * @param FilesRepositoryInterface $filesRepository
     * @return mixed
     */
    public function handle(FilesRepositoryInterface $filesRepository)
    {
        return $filesRepository->store($this->file);
    }
}
