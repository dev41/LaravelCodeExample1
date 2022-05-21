<?php

namespace App\Services;

use App\Contracts\Services\ImageSaverInterface;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class MessagesService implements ImageSaverInterface {
    private $imagesSizes;
    private $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
        $this->imagesSizes = [
            'small' => [
                'width' => 200
            ],
            'medium' => [
                'width' => 400
            ]
        ];
    }

    public function storeImage($image, int $objectId)
    {
        $imageName = uniqid(time()) . '.' . $image->extension();

        Storage::putFileAs(config('constants.files.chat_path') . "$objectId/original", $image, $imageName, [
            'visibility' => 'public',
            'ContentType' => $image->getClientMimeType()
        ]);

        foreach ($this->imagesSizes as $key => $imagesSize) {
            $img = $this->imageManager->make($image)
                ->orientate()
                ->resize($imagesSize['width'], null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            Storage::put(
                config('constants.files.chat_path') . "$objectId/$key/" . $imageName,
                (string)$img->encode(),
                [
                    'visibility' => 'public',
                    'ContentType' => $img->mime()
                ]);
        }

        return $imageName;
    }
}