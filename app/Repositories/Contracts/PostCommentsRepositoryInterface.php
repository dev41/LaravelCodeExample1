<?php

namespace App\Repositories\Contracts;

interface PostCommentsRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function destroyAllByPostId($postId);

    public function destroyById($id);

    public function getPostCommentsCount($post);

    public function getPostComments($postId);

    public function getPostCommentsList($postId, $ignores, $onlyCount = false, $commentId = null, $page = 1, $pageSize = 5);

    public function getByIdAndUserId(int $commentId, int $userId);

    public function getNotDeletedByPostId(int $postId);
}