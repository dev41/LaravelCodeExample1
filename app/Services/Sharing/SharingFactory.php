<?php
namespace App\Services\Sharing;

use App\Contracts\Sharing\ShareableInterface;
use App\Contracts\Sharing\SharingFactoryInterface;

class SharingFactory implements SharingFactoryInterface {
    public function make(string $url) : ShareableInterface
    {
        if (stripos($url, '/articles/category') !== false) {
            return app(ArticleCategorySharingService::class);
        } elseif (stripos($url, '/articles') !== false) {
            return app(ArticleSharingService::class);
        } elseif (stripos($url, '/books/category') !== false) {
            return app(GeneralSharingService::class);
        } elseif (stripos($url, '/books') !== false) {
            return app(BookSharingService::class);
        } elseif (stripos($url, '/enrichment/category_details') !== false) {
            return app(EventCategorySharingService::class);
        } elseif (stripos($url, '/hub_detail') !== false) {
            return app(HubSharingService::class);
        } elseif (stripos($url, '/user/post_details') !== false) {
            return app(PostSharingService::class);
        } elseif (stripos($url, '/groups/detail') !== false) {
            return app(GroupSharingService::class);
        } elseif (stripos($url, '/mentorship/details/') !== false) {
            return app(MentorshipSharingService::class);
        }

        return app(GeneralSharingService::class);
    }
}
