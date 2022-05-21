<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddMentorshipRequest;
use App\Http\Requests\MentorshipDetailsRequest;
use App\Mail\MentorThanks;
use App\Models\Mentorship;
use App\Repositories\Contracts\MentorshipsRepositoryInterface;
use App\Serializers\ResponseSerializer;
use App\Services\ImageService;
use App\Transformers\MentorshipTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class MentorshipController extends Controller
{
    /**
     * @SWG\Post(
     *   path="/enrichment/appaddmentorship",
     *   summary="Create mentorship",
     *   tags={"mentorship"},
     *   @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     description="Mentor name",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     description="Mentor email",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="mobile_no",
     *     in="formData",
     *     description="Mentor mobile number",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="short_desc",
     *     in="formData",
     *     description="Mentorship short description",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="long_desc",
     *     in="formData",
     *     description="Mentorship description",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="qualification",
     *     in="formData",
     *     description="Mentor qualification",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="image",
     *     in="formData",
     *     description="Mentorship image in base64",
     *     required=false,
     *     type="integer"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Mentorship added successfully"
     *   )
     * )
     * @param AddMentorshipRequest $request
     * @param ImageService $imageService
     * @param MentorshipsRepositoryInterface $mentorshipsRepository
     * @param Manager $fractal
     * @param ResponseSerializer $serializer
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(
        AddMentorshipRequest $request,
        ImageService $imageService,
        MentorshipsRepositoryInterface $mentorshipsRepository,
        Manager $fractal,
        ResponseSerializer $serializer
    ) {
        $mentorshipData = collect([
            'name' => $request->input('name'),
            'short_desc' => $request->input('short_desc'),
            'email' => $request->input('email'),
            'mobile_no' => $request->input('mobile_no')
        ]);

        if ($request->input('long_desc', '')) {
            $mentorshipData->put('long_desc', $request->input('long_desc'));
        }

        if ($request->input('qualification', '')) {
            $mentorshipData->put('qualification', $request->input('qualification'));
        }

        if ($request->input('image', '')) {
            $mentorshipData->put('image', $imageService->storeBase64Image($request->input('image'), 'mentorship'));
        }

        $mentorshipData->put('is_active', Mentorship::IS_NOT_ACTIVE)
            ->put('cdate', Carbon::now()->format('Y-m-d H:i:s'))
            ->put('is_deleted', Mentorship::IS_NOT_DELETED);

        if ($mentorship = $mentorshipsRepository->store($mentorshipData->toArray())) {
            Mail::to($mentorship->email)->queue(new MentorThanks());

            $fractal->setSerializer($serializer);
            $response = new Item($mentorship, new MentorshipTransformer());

            return response()->json([
                'last_id' => $mentorship->id,
                'Ack' => 1,
                'msg' => 'Mentorship Saved Successfully',
                'MentorshipDetails' => $fractal->createData($response)->toArray()
            ]);
        }

        return response()->json([
            'Ack' => 0,
            'msg' => 'Mentorship was not created'
        ]);
    }

    /**
     * @SWG\Get(
     *   path="/enrichment/appallmentorlist",
     *   summary="Get mentorships list",
     *   tags={"mentorship"},
     *   @SWG\Response(
     *     response=200,
     *     description="Mentorships fetched successfully"
     *   )
     * )
     * @param MentorshipsRepositoryInterface $mentorshipsRepository
     * @param Manager $fractal
     * @param ResponseSerializer $serializer
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(
        MentorshipsRepositoryInterface $mentorshipsRepository,
        Manager $fractal,
        ResponseSerializer $serializer
    ) {
        $mentorships = $mentorshipsRepository->getAllActive();
        if ($mentorships->isNotEmpty()) {
            $fractal->setSerializer($serializer);
            $response = new Collection($mentorships, new MentorshipTransformer());

            return response()->json([
                'Ack' => 1,
                'AllmentorList' => $fractal->createData($response)->toArray()
            ]);
        }

        return response()->json([
            'Ack' => 0,
            'msg' => 'No mentor List Found.'
        ]);
    }

    /**
     * @SWG\Post(
     *   path="/enrichment/appmentorshipdetailsbyid",
     *   summary="Get mentorship details by id",
     *   tags={"mentorship"},
     *   @SWG\Parameter(
     *     name="id",
     *     in="formData",
     *     description="Mentorship id",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Mentorship added successfully"
     *   )
     * )
     * @param MentorshipDetailsRequest $request
     * @param MentorshipsRepositoryInterface $mentorshipsRepository
     * @param Manager $fractal
     * @param ResponseSerializer $serializer
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(
        MentorshipDetailsRequest $request,
        MentorshipsRepositoryInterface $mentorshipsRepository,
        Manager $fractal,
        ResponseSerializer $serializer
    ) {
        if ($mentorship = $mentorshipsRepository->getActiveById((int)$request->input('id'))) {
            $fractal->setSerializer($serializer);
            $response = new Item($mentorship, new MentorshipTransformer());

            return response()->json([
                'Ack' => 1,
                'MentorListById' => $fractal->createData($response)->toArray()
            ]);
        }

        return response()->json([
            'Ack' => 0,
            'msg' => 'No mentor List Found.'
        ]);
    }
}
