<?php

namespace App\Services\Sharing;

use App\Contracts\Sharing\ShareableInterface;
use App\Helpers\AzureBlob;
use App\Repositories\Contracts\MagazineCategoriesRepositoryInterface;

class ArticleCategorySharingService implements ShareableInterface {
    private $magazineCategoriesRepository;
    private $generalSharingService;

    public function __construct(
        MagazineCategoriesRepositoryInterface $magazineCategoriesRepository,
        GeneralSharingService $generalSharingService
    ) {
        $this->magazineCategoriesRepository = $magazineCategoriesRepository;
        $this->generalSharingService = $generalSharingService;
    }

    public function getData(string $permalink) : array
    {
        if ($category = $this->magazineCategoriesRepository->getActiveBySlug($permalink)) {
            $image = AzureBlob::url(config('constants.images.default_article_category_image'));
            if ($category->banner_image != '') {
                $image = AzureBlob::url(config('constants.files.banners_path') . $category->banner_image);
            }

            return [
                'type' => 'articles-category',
                'title' => $category->name,
                'description' => $category->short_description,
                'image' => $image
            ];
        }

        return $this->generalSharingService->getData($permalink);
    }
}