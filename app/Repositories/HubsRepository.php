<?php

namespace App\Repositories;

use App\Contracts\CrudInterface;
use App\Models\Hub;
use App\Models\HubInvite;
use App\Repositories\Contracts\HubsRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HubsRepository implements HubsRepositoryInterface, CrudInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(Hub $model)
    {
        $this->model = $model;
    }

    public function getByPermalink(string $permalink)
    {
        return $this->model
            ->where([
                'permalink' => $permalink
            ])
            ->active()
            ->first();
    }

    public function getAllActiveHubs(array $conditions, int $limit = 10)
    {
        $query = $this->model
            ->whereRaw('(? * ACOS(COS(radians(?)) * COS(radians(hubs.lat)) * COS(radians(?) - radians(hubs.lng)) + SIN(radians(?)) * SIN(radians(hubs.lat)))) <= ?', [
                // Earth radius
                6378245,
                $conditions['latitude'],
                $conditions['longitude'],
                $conditions['latitude'],
                // distance
                config('constants.coordinates.default_hubs_location_radius')
            ])
            ->where('end_date', '>=', Carbon::now())
            ->active();

        if (isset($conditions['search'])) {
            $query->where(function ($query) use ($conditions) {
                $query->where('title', 'like', "%{$conditions['search']}%")
                    ->orWhere('address', 'like', "%{$conditions['search']}%")
                    ->orWhere('organizer', 'like', "%{$conditions['search']}%");
            });
        }

        if (isset($conditions['category_id'])) {
            $query->where('category_id', $conditions['category_id']);
        }

        $query->orderBy('start_date', 'ASC');

        if (isset($conditions['count']) && $conditions['count']) {
            return $query->count();
        }

        return $query->paginate($limit);

    }

    public function getQueryByKeyword($keyword)
    {
        $query = $this->model
            ->where('end_date', '>=', Carbon::now())
            ->active();

        if ($keyword != '') {
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%$keyword%")
                    ->orWhere('address', 'like', "%$keyword%")
                    ->orWhere('organizer', 'like', "%$keyword%");
            });
        }

        return $query;
    }

    public function getUserHubs(User $user, int $limit = 10)
    {
        return $this->model
            ->select(['hubs.*'])
            ->join('hub_invites', 'hubs.id', '=', 'hub_invites.hub_id')
            ->where([
                'hub_invites.status' => HubInvite::STATUS_ACCEPTED,
                'hub_invites.user_id' => $user->id
            ])
            ->paginate($limit);
    }

    public function updateHub(Hub $hub, array $data) : Hub
    {
        return tap($hub)->update($data);
    }
}