<?php

namespace App\Services;

use App\Contracts\Services\ServiceInterface;
use App\Helpers\AzureBlob;
use App\Repositories\Contracts\HubsRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class HubService implements ServiceInterface {
    private $hubsRepository;
    private $imagesSizes;

    public function __construct(HubsRepositoryInterface $hubsRepository)
    {
        $this->hubsRepository = $hubsRepository;
        $this->imagesSizes = [
            'small' => [
                'width' => 200
            ],
            'medium' => [
                'width' => 400
            ]
        ];
    }

    public function generatePermalink(string $title)
    {
        $permalink = str_slug($title);

        if ($hub = $this->hubsRepository->getByPermalink($permalink)) {
            $permalink = str_slug($permalink . ' ' . str_random(7));
        }

        return $permalink;
    }

    public function storeImage($image, int $objectId)
    {
        $imageParts = explode(";base64,", $image);
        $imageExtension = explode("image/", $imageParts[0])[1];
        $imageBase64 = base64_decode($imageParts[1]);

        $imageName = uniqid(time()) . '.' . $imageExtension;

        $imagePath = config('constants.files.hubs_path') . "$objectId/original";

        $imageTypeArray = explode(';', $image);
        $imageMimeType = str_replace('data:', '', $imageTypeArray[0]);

        Storage::put("$imagePath/$imageName", $imageBase64, [
            'visibility' => 'public',
            'ContentType' => $imageMimeType
        ]);

        $storedFilePath = AzureBlob::url("{$imagePath}/$imageName");

        $imageManager = new ImageManager();
        foreach ($this->imagesSizes as $key => $imagesSize) {
            $img = $imageManager->make($storedFilePath)
                ->resize($imagesSize['width'], null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            Storage::put(config('constants.files.hubs_path') . "$objectId/$key/" . $imageName, (string)$img->encode(), [
                'visibility' => 'public',
                'ContentType' => $img->mime()
            ]);
        }

        return $imageName;
    }

    public function getCoordinatesByAddress($address)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://maps.google.com/maps/api/geocode/json?key=' . config('constants.credentials.google_maps_api_key') . '&address=' . urlencode(trim($address)) . '&sensor=false');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = json_decode(curl_exec($ch));
        curl_close($ch);

        if ($response->status != 'OK') {
            return null;
        }

        return [
            'lat' => isset($response->results[0]->geometry->location->lat) ? $response->results[0]->geometry->location->lat : 0,
            'lng' => isset($response->results[0]->geometry->location->lat) ? $response->results[0]->geometry->location->lng : 0
        ];
    }
}