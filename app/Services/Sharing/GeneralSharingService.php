<?php

namespace App\Services\Sharing;

use App\Contracts\Sharing\ShareableInterface;
use App\Helpers\AzureBlob;

class GeneralSharingService implements ShareableInterface {
    public function getData(string $permalink) : array
    {
        return [
            'type' => 'general',
            'title' => 'Femnesty',
            'description' => 'Femnesty is a platform uniting women, women’s groups and women’s organisations' .
                'all around the world. We connect women locally and globally by offering a space to talk, share,' .
                'confide and debate in a secure and supportive setting.',
            'image' => AzureBlob::url(config('constants.images.default_shared_logo_image'))
        ];
    }
}