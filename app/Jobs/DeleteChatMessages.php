<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteChatMessages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $messages;
    private $user;

    /**
     * DeleteChatMessages constructor.
     * @param $messages
     * @param User $user
     */
    public function __construct($messages, User $user)
    {
        $this->messages = $messages;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->messages->isNotEmpty()) {
            foreach ($this->messages as $message) {
                if ($this->user->cannot('delete', $message)) {
                    continue;
                }

                if ($message->deleted_by && $message->deleted_by != $this->user->id) {
                    $message->delete();
                } else {
                    $message->deleted_by = $this->user->id;
                    $message->update();
                }
            }
        }
    }
}
