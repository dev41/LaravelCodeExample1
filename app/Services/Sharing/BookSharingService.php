<?php

namespace App\Services\Sharing;

use App\Contracts\Sharing\ShareableInterface;
use App\Helpers\AzureBlob;
use App\Repositories\Contracts\BooksRepositoryInterface;

class BookSharingService implements ShareableInterface {
    private $booksRepository;
    private $generalSharingService;

    public function __construct(
        BooksRepositoryInterface $booksRepository,
        GeneralSharingService $generalSharingService
    ) {
        $this->booksRepository = $booksRepository;
        $this->generalSharingService = $generalSharingService;
    }

    public function getData(string $permalink) : array
    {
        if ($book = $this->booksRepository->getActiveBySlug($permalink)) {
            $image = AzureBlob::url(config('constants.images.default_book_image'));
            if ($book->image != '') {
                $image = AzureBlob::url(config('constants.files.banners_path') . $book->image);
            }

            return [
                'type' => 'book',
                'title' => $book->title,
                'description' => $book->short_desc,
                'image' => $image
            ];
        }

        return $this->generalSharingService->getData($permalink);
    }
}