<?php

namespace App\Jobs;

use App\Notifications\ImportUserCompleted;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyUserOfCompletedImportUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function handle()
    {
        $this->user->notify(new ImportUserCompleted());
    }
}
