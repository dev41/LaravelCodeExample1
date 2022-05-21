<?php

namespace App\Http\Controllers;

use App\Helpers\AzureBlob;
use App\Http\Requests\SearchByKeywordRequest;
use App\Models\Group;
use App\Models\GroupRequest;
use App\Models\Magazine;
use App\Repositories\Contracts\BooksRepositoryInterface;
use App\Repositories\Contracts\GroupConnectionsRepositoryInterface;
use App\Repositories\Contracts\GroupRequestsRepositoryInterface;
use App\Repositories\Contracts\HubInvitesRepositoryInterface;
use App\Repositories\Contracts\HubsRepositoryInterface;
use App\Repositories\Contracts\MagazinesRepositoryInterface;
use App\Repositories\Contracts\MentorshipsRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\GroupsRepository;
use App\Serializers\ResponseSerializer;
use App\Transformers\ArticleTransformer;
use App\Transformers\BookTransformer;
use App\Transformers\GroupTransformer;
use App\Transformers\HubTransformer;
use App\Transformers\SearchUserTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class SearchController extends Controller
{
    /**
     * @SWG\Post(
     *   path="/enrichment/appsearchbykeyword",
     *   summary="Search by keyword",
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="access token (type Bearer)",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="keyword",
     *     in="formData",
     *     description="Word for search",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="page_number",
     *     in="formData",
     *     description="Page number",
     *     required=false,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="limit",
     *     in="formData",
     *     description="Page size",
     *     required=false,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="type",
     *     in="formData",
     *     description="Search results type",
     *     required=false,
     *     type="string",
     *     @SWG\Items(
     *         type="string",
     *         enum={
     *          "users - search users",
     *          "articles - search articles",
     *          "groups - search groups",
     *          "hubs - search hubs",
     *          "books - search books"
     *         }
     *     ),
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Search results fetched successfully"
     *   )
     * )
     * @param SearchByKeywordRequest $request
     * @param UserRepositoryInterface $userRepository
     * @param MagazinesRepositoryInterface $magazinesRepository
     * @param GroupsRepository $groupsRepository
     * @param HubsRepositoryInterface $hubsRepository
     * @param BooksRepositoryInterface $booksRepository
     * @param Manager $fractal
     * @param ResponseSerializer $serializer
     * @return \Illuminate\Http\JsonResponse
     */
    public function byKeyword(
        SearchByKeywordRequest $request,
        UserRepositoryInterface $userRepository,
        MagazinesRepositoryInterface $magazinesRepository,
        GroupsRepository $groupsRepository,
        HubsRepositoryInterface $hubsRepository,
        BooksRepositoryInterface $booksRepository,
        Manager $fractal,
        ResponseSerializer $serializer
    ) {
        $fractal->setSerializer($serializer);

        $pageSize = $request->input('limit', 20);
        $offset = ($request->input('page_number', 1) - 1) * $pageSize ;

        $usersQuery = $userRepository->getQueryByKeyword($request->input('keyword'));
        $usersCountQuery = $usersQuery;
        $usersCount = $usersCountQuery->get()->count();

        if (!$request->has('type') || $request->input('type') == 'users') {
            $usersResource = new Collection(
                $usersQuery->offset($offset)->take($pageSize)->get(),
                new SearchUserTransformer()
            );
            $users = $fractal->createData($usersResource)->toArray();
        }

        $articlesQuery = $magazinesRepository->getQueryByKeyword($request->input('keyword'), Magazine::TYPE_ARTICLE);
        $articlesCount = $articlesQuery->count();

        if (!$request->has('type') || $request->input('type') == 'articles') {
            $articlesResource = new Collection(
                $articlesQuery->offset($offset)->take($pageSize)->get(),
                new ArticleTransformer()
            );
            $articles = $fractal->createData($articlesResource)->toArray();
        }

        $groupsQuery = $groupsRepository->getQueryByKeyword($request->input('keyword'));
        $groupsCount = $groupsQuery->count();

        if (!$request->has('type') || $request->input('type') == 'groups') {
            $fractal->parseIncludes('member_count');
            $groupsResource = new Collection(
                $groupsQuery->offset($offset)->take($pageSize)->get(),
                new GroupTransformer()
            );
            $groups = $fractal->createData($groupsResource)->toArray();
        }

        $hubsQuery = $hubsRepository->getQueryByKeyword($request->input('keyword'));
        $hubsCount = $hubsQuery->count();

        if (!$request->has('type') || $request->input('type') == 'hubs') {
            $fractal->parseIncludes('members_count');
            $hubsResource = new Collection($hubsQuery->offset($offset)->take($pageSize)->get(), new HubTransformer());
            $hubs = $fractal->createData($hubsResource)->toArray();
        }

        $booksQuery = $booksRepository->getQueryByKeyword($request->input('keyword'));
        $booksCount = $booksQuery->count();

        if (!$request->has('type') || $request->input('type') == 'books') {
            $booksResource = new Collection(
                $booksQuery->offset($offset)->take($pageSize)->get(),
                new BookTransformer()
            );
            $books = $fractal->createData($booksResource)->toArray();
        }

        $totalCount = $usersCount + $articlesCount + $groupsCount + $hubsCount + $booksCount;

        return response()->json([
            'Ack' => 1,
            'TotalSearch' => [
                'userresult_r' => $users ?? [],
                'user_count' => $usersCount,
                'articleresult_r' => $articles ?? [],
                'article_count' => $articlesCount,
                'groupresult_r' => $groups ?? [],
                'group_count' => $groupsCount,
                'hubresult_r' => $hubs ?? [],
                'hub_count' => $hubsCount,
                'bookresult_r' => $books ?? [],
                'book_count' => $booksCount
            ],
            'totalCount' => $totalCount
        ]);
    }
}
