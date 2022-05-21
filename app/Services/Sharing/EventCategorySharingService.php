<?php

namespace App\Services\Sharing;

use App\Contracts\Sharing\ShareableInterface;
use App\Helpers\AzureBlob;
use App\Models\MagazineCategory;
use App\Repositories\Contracts\MagazineCategoriesRepositoryInterface;

class EventCategorySharingService implements ShareableInterface {
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
        if ($category = $this->magazineCategoriesRepository->getActiveBySlugAndType($permalink, MagazineCategory::TYPE_EVENT)) {
            $image = AzureBlob::url(config('constants.images.default_event_category_image'));
            if ($category->banner_image != '') {
                $image = AzureBlob::url(config('constants.files.banners_path') . $category->banner_image);
            }

            return [
                'type' => 'event-category',
                'title' => $category->name,
                'description' => $category->short_description,
                'image' => $image
            ];
        }

        return $this->generalSharingService->getData($permalink);
    }
}