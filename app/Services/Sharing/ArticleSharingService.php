<?php

namespace App\Services\Sharing;

use App\Contracts\Sharing\ShareableInterface;
use App\Helpers\AzureBlob;
use App\Repositories\MagazinesRepository;

class ArticleSharingService implements ShareableInterface {
    private $magazinesRepository;
    private $generalSharingService;

    public function __construct(
        MagazinesRepository $magazinesRepository,
        GeneralSharingService $generalSharingService
    ) {
        $this->magazinesRepository = $magazinesRepository;
        $this->generalSharingService = $generalSharingService;
    }

    public function getData(string $permalink) : array
    {
        if ($article = $this->magazinesRepository->getArticleBySlug($permalink)) {
            $image = AzureBlob::url(config('constants.images.default_article_image'));
            if ($article->image != '') {
                $image = AzureBlob::url(config('constants.files.banners_path') . $article->image);
            }

            return [
                'type' => 'article',
                'title' => $article->title,
                'description' => $article->short_desc,
                'image' => $image
            ];
        }

        return $this->generalSharingService->getData($permalink);
    }
}