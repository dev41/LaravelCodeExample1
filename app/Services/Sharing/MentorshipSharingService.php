<?php

namespace App\Services\Sharing;

use App\Contracts\Sharing\ShareableInterface;
use App\Helpers\AzureBlob;
use App\Repositories\Contracts\MentorshipsRepositoryInterface;

class MentorshipSharingService implements ShareableInterface {
    private $mentorshipsRepository;
    private $generalSharingService;

    public function __construct(
        MentorshipsRepositoryInterface $mentorshipsRepository,
        GeneralSharingService $generalSharingService
    ) {
        $this->mentorshipsRepository = $mentorshipsRepository;
        $this->generalSharingService = $generalSharingService;
    }

    public function getData(string $permalink) : array
    {
        if ($mentorship = $this->mentorshipsRepository->getByKey($permalink)) {
            $image = AzureBlob::url(config('constants.images.default_mentorship_image'));
            if ($mentorship->image != '') {
                $image = AzureBlob::url(config('constants.files.all_images_path') . $mentorship->image);
            }

            return [
                'type' => 'mentorship',
                'title' => $mentorship->name,
                'description' => $mentorship->short_desc,
                'image' => $image
            ];
        }

        return $this->generalSharingService->getData($permalink);
    }
}