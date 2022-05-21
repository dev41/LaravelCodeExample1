<?php

namespace App\Models;

use App\Helpers\AzureBlob;
use Illuminate\Database\Eloquent\Model;

class PostLink extends Model
{
    protected $table = 'post_links';

    protected $fillable = [
        'post_id', 'url', 'title', 'description', 'image'
    ];

    public $timestamps = false;

    public function getImage()
    {
        $image = $this->image;

        if ($image != '') {
            if (stripos($image, 'http://') !== false || stripos($image, 'https://') !== false) {
                $fileNameArray = explode('/', $image);
                $image = end($fileNameArray);
            }

            $images = [
                'small' => AzureBlob::url(config('constants.files.shared_images_path') . "small/$image"),
                'medium' => AzureBlob::url(config('constants.files.shared_images_path') . "medium/$image"),
                'original' => AzureBlob::url(config('constants.files.shared_images_path') . "original/$image")
            ];
        }

        return $images ?? new \stdClass();
    }
}
