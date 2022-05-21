<?php

namespace App\Http\Controllers;

use App\Helpers\AzureBlob;
use App\Repositories\Contracts\SettingsRepositoryInterface;
use App\Serializers\ResponseSerializer;
use App\Transformers\SettingsTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class SettingsController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/enrichment/appallsitesettings",
     *   summary="Return site configuration",
     *   @SWG\Response(
     *     response=200,
     *     description="Site configuration fetched successfully"
     *   )
     * )
     *
     * @param SettingsRepositoryInterface $settingsRepository
     * @param Manager $fractal
     * @param ResponseSerializer $serializer
     * @param SettingsTransformer $transformer
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(
        SettingsRepositoryInterface $settingsRepository,
        Manager $fractal,
        ResponseSerializer $serializer,
        SettingsTransformer $transformer
    ) {
        if ($settings = $settingsRepository->getSiteSettings()) {
            $fractal->setSerializer($serializer);
            $response = new Item($settings, $transformer);

            $settings->site_logo_url = AzureBlob::url(config('constants.images.default_site_logo'));
            if ($settings->site_logo != ""){
                $settings->site_logo_url = AzureBlob::url(config('constants.files.site_logo_path') . $settings->site_logo);
            }

            $settings->favicon_url = AzureBlob::url('favicon.ico');
            if ($settings->favicon != "") {
                $settings->favicon_url = AzureBlob::url($settings->favicon);
            }

            if ($settings->aboutus_banner_image != ""){
                $settings->aboutus_banner_image = AzureBlob::url(config('constants.files.banners_path') . $settings->aboutus_banner_image);
            } else {
                $settings->aboutus_banner_image = AzureBlob::url(config('constants.images.default_static_content_image'));
            }

            return response()->json([
                'Ack' => 1,
                'SiteSettings' => $fractal->createData($response)->toArray()
            ]);
        }

        return response()->json([
            'Ack' => 0,
            'msg' => 'No SiteSettings Found.'
        ], 401);
    }
}
