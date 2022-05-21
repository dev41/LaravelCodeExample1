<?php

namespace App\Http\Controllers;

use App\Helpers\AzureBlob;
use App\Http\Requests\RemoveImageRequest;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UserImagesRequest;
use App\Repositories\Contracts\GroupsRepositoryInterface;
use App\Repositories\Contracts\HubsRepositoryInterface;
use App\Repositories\Contracts\ImagesRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Serializers\ResponseSerializer;
use App\Services\ImageService;
use App\Transformers\HubTransformer;
use App\Transformers\ImageTransformer;
use App\Transformers\UserTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ImageController extends Controller
{
    /**
     * @SWG\Post(
     *   path="/users/appuserallimagesbyid",
     *   summary="Get user posts images",
     *   tags={"users"},
     *   deprecated=true,
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="access token (type Bearer)",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="formData",
     *     description="User id",
     *     required=true,
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
     *     name="page",
     *     in="formData",
     *     description="Current page",
     *     required=false,
     *     type="integer"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Posts images fetched successfully"
     *   )
     * )
     * @param UserImagesRequest $request
     * @param ImagesRepositoryInterface $imagesRepository
     * @param Manager $fractal
     * @param ResponseSerializer $serializer
     * @param UserRepositoryInterface $userRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function userImages(
        UserImagesRequest $request,
        ImagesRepositoryInterface $imagesRepository,
        Manager $fractal,
        ResponseSerializer $serializer,
        UserRepositoryInterface $userRepository
    ) {
        $pageSize = $request->input('limit', 10);
        $offset = ($request->input('page', 1) - 1) * $pageSize;

        $user = $userRepository->getByKey($request->input('id'));

        if ($user && auth()->user()->cannot('viewPhotos', $user)) {
            return response()->json([
                'Ack' => 1,
                'UserAllImageById' => [],
                'blocked' => true,
                'TotalCount' => 0
            ]);
        }

        $images = $imagesRepository->getUserPostsImages($request->input('id'), $offset, $pageSize);
        if ($images->isNotEmpty()) {
            $fractal->setSerializer($serializer);
            $resource = new Collection($images, new ImageTransformer());

            return response()->json([
                'Ack' => 1,
                'UserAllImageById' => $fractal->createData($resource)->toArray(),
                'blocked' => false,
                'TotalCount' => $imagesRepository->getUserPostsImagesTotalCount($request->input('id'))
            ]);
        }


        return response()->json([
            'Ack' => 0,
            'msg' => 'No Records Found.',
            'blocked' => auth()->user()->cannot('viewPhotos', $user),
            'TotalCount' => 0
        ]);
    }

    /**
     * @SWG\Post(
     *   path="/users/uploadfile",
     *   summary="Store file",
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="access token (type Bearer)",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="file_name",
     *     in="formData",
     *     description="File in base64 encode",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Image stored successfully"
     *   )
     * )
     *
     * @param StoreImageRequest $request
     * @param ImageService $imageService
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(
        StoreImageRequest $request,
        ImageService $imageService
    ) {
        if ($imageName = $imageService->storeBase64Image($request->input('file_name'), 'all')) {
            return response()->json([
                'Ack' => 1,
                'file_name' => AzureBlob::url(config('constants.files.all_images_path') . $imageName),
                'msg' => "Image Uploaded Successfully"
            ]);
        }

        return response()->json([
            'Ack' => 0,
            'msg' => "Image Not Uploaded Successfully"
        ]);
    }

    /**
     * @SWG\Post(
     *   path="/users/appremoveimg",
     *   summary="Remove resource image",
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="access token (type Bearer)",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="formData",
     *     description="Resource id",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="type",
     *     in="formData",
     *     description="Removing type (1 - group image, 2 - hub image, 3 - user profile image, 4 - user cover image)",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="image",
     *     in="formData",
     *     description="Image (set null to remove image)",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="profile_image",
     *     in="formData",
     *     description="Profile Image (set null to remove image)",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="cover_img",
     *     in="formData",
     *     description="Cover Image (set null to remove image)",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Image removed successfully"
     *   )
     * )
     * @param RemoveImageRequest $request
     * @param UserRepositoryInterface $userRepository
     * @param HubsRepositoryInterface $hubsRepository
     * @param GroupsRepositoryInterface $groupsRepository
     * @param Manager $fractal
     * @param ResponseSerializer $serializer
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(
        RemoveImageRequest $request,
        UserRepositoryInterface $userRepository,
        HubsRepositoryInterface $hubsRepository,
        GroupsRepositoryInterface $groupsRepository,
        Manager $fractal,
        ResponseSerializer $serializer
    ) {
        $fractal->setSerializer($serializer);

        if (in_array($request->input('type'), [3, 4])) {
            if ($userRepository->update($request->only(['profile_image', 'cover_img']), ['id' => $request->input('id')])) {
                $resource = new Item($userRepository->getByKey($request->input('id')), new UserTransformer());
                return response()->json([
                    'Ack' => 1,
                    'msg' => 'Data remove successfully.',
                    'user' => $fractal->createData($resource)->toArray()
                ]);
            }

            return response()->json([
                'Ack' => 0,
                'msg' => 'Data could not be saved.'
            ]);
        }

        if ($request->input('type') == 2) {
            if ($hubsRepository->update($request->only('image'), ['id' => $request->input('id')])) {
                $resource = new Item($hubsRepository->getByKey($request->input('id')), new HubTransformer());
                return response()->json([
                    'Ack' => 1,
                    'msg' => 'Data remove successfully.',
                    'data' => $fractal->createData($resource)->toArray()
                ]);
            }

            return response()->json([
                'Ack' => 0,
                'msg' => 'Data could not be saved.'
            ]);
        }

        if ($request->input('type') == 1) {
            if ($groupsRepository->update($request->only('image'), ['id' => $request->input('id')])) {
                return response()->json([
                    'Ack' => 1,
                    'msg' => 'Data remove successfully.'
                ]);
            }

            return response()->json([
                'Ack' => 0,
                'msg' => 'Data could not be saved.'
            ]);
        }

        return response()->json([
            'Ack' => 0,
            'msg' => 'No Records Found.'
        ]);
    }
}
