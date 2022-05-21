<?php
namespace App\Services;

use App\Contracts\Services\ImageSaverInterface;
use App\Contracts\Services\ImageServiceInterface;
use App\Contracts\Services\ServiceInterface;
use App\Helpers\AzureBlob;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ImageService implements ImageServiceInterface {
    public $imagePaths;
    public $postImagesSizes;
    public $sharedImagesSizes;

    public function __construct()
    {
        $this->imagePaths = [
            'profile' => config('constants.files.all_images_path'),
            'cover' => config('constants.files.all_images_path'),
            'post' => config('constants.files.all_images_path'),
            'all' => config('constants.files.all_images_path'),
            'group' => config('constants.files.all_images_path'),
            'hub' => config('constants.files.all_images_path'),
            'article' => config('constants.files.banners_path'),
            'sharedImage' => config('constants.files.shared_images_path') . 'original/',
            'mentorship' => config('constants.files.all_images_path'),
            'team' => config('constants.files.all_images_path')
        ];

        $this->postImagesSizes = [
            'small' => [
                'width' => 200
            ],
            'medium' => [
                'width' => 400
            ]
        ];

        $this->sharedImagesSizes = [
            'small' => [
                'width' => 200
            ],
            'medium' => [
                'width' => 400
            ]
        ];
    }

    public function storeBase64Image($image, $type, $imageMimeType = false)
    {
        if (!$imageMimeType) {
            $imageParts = explode(";base64,", $image);
            $imageExtension = explode("image/", $imageParts[0])[1];
            $imageBase64 = base64_decode($imageParts[1]);
        } else {
            $imageExtension = explode("image/", $imageMimeType)[1];
            $imageBase64 = $image;
        }

        $imageName = uniqid(time()) . '.' . $imageExtension;

        $imagePath = $imageName;
        if (isset($this->imagePaths[$type])) {
            $imagePath = $this->imagePaths[$type] . $imageName;
        }

        if (!$imageMimeType) {
            $imageTypeArray = explode(';', $image);
            $imageMimeType = str_replace('data:', '', $imageTypeArray[0]);
        }

        Storage::put($imagePath, $imageBase64, [
            'visibility' => 'public',
            'ContentType' => $imageMimeType
        ]);

        return $imageName;
    }

    public function storeBase64File($image, $type)
    {
        $fileParts = explode(";base64,", $image);
        $imageExtension = explode("/", $fileParts[0])[1];
        $fileBase64 = base64_decode($fileParts[1]);

        if ($imageExtension == 'jpeg') {
            return $imageExtension;
        }

        $fileName = uniqid(time()) . '.' . $imageExtension;

        $filePath = $fileName;
        if (isset($this->imagePaths[$type])) {
            $filePath = $this->imagePaths[$type] . $fileName;
        }

        $fileTypeArray = explode(';', $image);
        $fileMimeType = str_replace('data:', '', $fileTypeArray[0]);

        Storage::put($filePath, $fileBase64, [
            'visibility' => 'public',
            'ContentType' => $fileMimeType
        ]);

        return $fileName;
    }

    public function storeImageFromUrl($imageUrl, $type)
    {
        $fileContent = file_get_contents($imageUrl);
        $fileNameArray = explode('/', $imageUrl);
        $fileName = end($fileNameArray);
        $imagePath = $fileName;
        if (isset($this->imagePaths[$type])) {
            $imagePath = config('constants.files.shared_images_path') . "original/$fileName";
        }

        Storage::disk('public')->put($imagePath, $fileContent);
        $imageType = mime_content_type(storage_path('app/public/'. $imagePath));

        $imageName = $this->storeBase64Image("data:$imageType;base64," . base64_encode($fileContent), $type);
        Storage::disk('public')->delete($imagePath);

        $fullImagePath = AzureBlob::url(config('constants.files.shared_images_path') . "original/$imageName");

        $imageManager = new ImageManager();
        foreach ($this->sharedImagesSizes as $key => $sharedImageSize) {
            $img = $imageManager->make($fullImagePath)
                ->resize($sharedImageSize['width'], null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            Storage::put(config('constants.files.shared_images_path') . "$key/" . $imageName, (string)$img->encode(), [
                'visibility' => 'public',
                'ContentType' => $img->mime()
            ]);
        }

        return [
            'small' => AzureBlob::url(config('constants.files.shared_images_path') . "small/$imageName"),
            'medium' => AzureBlob::url(config('constants.files.shared_images_path') . "medium/$imageName"),
            'original' => AzureBlob::url(config('constants.files.shared_images_path') . "original/$imageName")
        ];
    }

    public function storePostsFile(Post $post, $file)
    {
        $filePath = config('constants.files.posts_path') . "{$post->id}/original";
        $fileName = uniqid(time()) . '.' . $file->extension();

        Storage::putFileAs($filePath, $file, $fileName, [
            'visibility' => 'public',
            'ContentType' => $file->getClientMimeType()
        ]);

        $imageManager = new ImageManager();
        foreach ($this->postImagesSizes as $key => $postImageSize) {
            $img = $imageManager->make($file)
                ->orientate()
                ->resize($postImageSize['width'], null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            Storage::put(config('constants.files.posts_path') . "{$post->id}/$key/" . $fileName, (string)$img->encode(), [
                'visibility' => 'public',
                'ContentType' => $img->mime()
            ]);
        }

        return $fileName;
    }

    public function storeImage($image, int $objectId, ServiceInterface $service)
    {
        return $service->storeImage($image, $objectId);
    }

    public function store($image, int $objectId, ImageSaverInterface $service)
    {
        return $service->storeImage($image, $objectId);
    }
}