<?php

namespace App\Repositories\Contracts;

interface ImagesRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function delete(array $conditions);

    public function getUserPostsImages($userId, int $offset = 0, int $limit = 10);

    public function getUserPostsImagesTotalCount(int $userId);

    public function destroyAllByPostId($postId);

    public function getAllPostImagesByPostImgIdAndImageType($postImgId, $imageType, $cut = false, int $offset = 0, int $limit = 10);

    public function getAllPostImagesByPostImgIdAndImageTypeCount($postImgId, $imageType);
}