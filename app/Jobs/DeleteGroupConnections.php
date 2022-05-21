<?php

namespace App\Jobs;

use App\Models\Hub;
use App\Repositories\Contracts\PostsRepositoryInterface;
use App\Repositories\GroupConnectionsRepository;
use App\Repositories\GroupRequestsRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteGroupConnections implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $groupId;

    /**
     * DeleteGroupConnections constructor.
     * @param int $groupId
     */
    public function __construct(int $groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @param GroupRequestsRepository $groupRequestsRepository
     * @param GroupConnectionsRepository $groupConnectionsRepository
     */
    public function handle(
        GroupRequestsRepository $groupRequestsRepository,
        GroupConnectionsRepository $groupConnectionsRepository
    ) {
        $groupRequestsRepository->delete(['group_id' => $this->groupId]);
        $groupConnectionsRepository->delete(['group_id' => $this->groupId]);
    }
}
