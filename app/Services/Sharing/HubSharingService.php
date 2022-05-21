<?php

namespace App\Services\Sharing;

use App\Contracts\Sharing\ShareableInterface;
use App\Helpers\AzureBlob;
use App\Repositories\Contracts\HubsRepositoryInterface;

class HubSharingService implements ShareableInterface {
    private $hubsRepository;
    private $generalSharingService;

    public function __construct(
        HubsRepositoryInterface $hubsRepository,
        GeneralSharingService $generalSharingService
    ) {
        $this->hubsRepository = $hubsRepository;
        $this->generalSharingService = $generalSharingService;
    }

    public function getData(string $permalink) : array
    {
        if ($hub = $this->hubsRepository->getByPermalink($permalink)) {
            $image = AzureBlob::url(config('constants.files.hubs_path') . 'default/original/' . config('constants.images.default_hub_image_name'));
            if (!empty($hub->image)) {
                $image = AzureBlob::url(config('constants.files.hubs_path') . "{$hub->id}/original/$hub->image");
            }

            return [
                'type' => 'hub',
                'title' => $hub->title,
                'description' => $hub->description,
                'image' => $image
            ];
        }

        return $this->generalSharingService->getData($permalink);
    }
}