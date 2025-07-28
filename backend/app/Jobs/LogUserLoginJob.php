<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User\User;
use Illuminate\Support\Facades\Log;

class LogUserLoginJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $user;
    protected $ip;

    public function __construct(User $user, string $ip)
    {
        $this->user = $user;
        $this->ip = $ip;
    }

    
    public function handle()
    {
        Log::info('Usuário logado', [
            'user_id' => $this->user->id,
            'name' => $this->user->name,
            'ip' => $this->ip,
            'timestamp' => now()->toDateTimeString()
        ]);
    }
}
