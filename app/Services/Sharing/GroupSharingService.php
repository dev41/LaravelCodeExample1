<?php

namespace App\Services\Sharing;

use App\Contracts\Sharing\ShareableInterface;
use App\Helpers\AzureBlob;
use App\Repositories\Contracts\GroupsRepositoryInterface;

class GroupSharingService implements ShareableInterface {
    private $groupsRepository;
    private $generalSharingService;

    public function __construct(
        GroupsRepositoryInterface $groupsRepository,
        GeneralSharingService $generalSharingService
    ) {
        $this->groupsRepository = $groupsRepository;
        $this->generalSharingService = $generalSharingService;
    }

    public function getData(string $permalink) : array
    {
        if ($group = $this->groupsRepository->getBySlug($permalink)) {
            $image = AzureBlob::url(config('constants.files.groups_path') . 'default/original/' . config('constants.images.default_group_image_name'));
            if ($group->image != "") {
                $image = AzureBlob::url(config('constants.files.groups_path') . "{$group->id}/original/{$group->image}");
            }

            return [
                'type' => 'group',
                'title' => $group->group_name,
                'description' => $group->short_desc,
                'image' => $image
            ];
        }

        return $this->generalSharingService->getData($permalink);
    }
}