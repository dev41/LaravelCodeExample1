<?php

namespace App\Repositories;

use App\Models\GroupConnection;
use App\Models\Image;
use App\Repositories\Contracts\GroupConnectionsRepositoryInterface;
use App\Repositories\Contracts\ImagesRepositoryInterface;
use App\Traits\RepositoryTrait;

class ImagesRepository implements ImagesRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(Image $image)
    {
        $this->model = $image;
    }

    public function getUserPostsImages($userId, int $offset = 0, int $limit = 10)
    {
        return $this->model
            ->where([
                'user_id' => $userId,
                'type' => Image::TYPE_POST_IMAGE
            ])
            ->orderBy('id', 'DESC')
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    public function getUserPostsImagesTotalCount(int $userId)
    {
        return $this->model
            ->where([
                'user_id' => $userId,
                'type' => Image::TYPE_POST_IMAGE
            ])
            ->count();
    }

    public function destroyAllByPostId($postId)
    {
        return $this->model->where([
            'object_id' => $postId,
            'type' => Image::TYPE_POST_IMAGE
        ])->delete();
    }

    public function getAllPostImagesByPostImgIdAndImageType($postImgId, $imageType, $cut = false, int $offset = 0, int $limit = 10)
    {
        $query = $this->model
            ->where([
                'post_img_id' => $postImgId,
                'image_type' => $imageType,
                'type' => Image::TYPE_POST_IMAGE
            ])
            ->where('file_name', '<>', '');

        if ($cut) {
            $limit = 6;
        }

        return $query
            ->orderBy('id', 'DESC')
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    public function getAllPostImagesByPostImgIdAndImageTypeCount($postImgId, $imageType)
    {
        return $this->model
            ->where([
                'post_img_id' => $postImgId,
                'image_type' => $imageType,
                'type' => Image::TYPE_POST_IMAGE
            ])
            ->where('file_name', '<>', '')
            ->count();
    }
}