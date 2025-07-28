<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use App\Services\Notification\NotificationService;

class NotificationController extends Controller
{
    protected NotificationService $service;

    public function __construct(NotificationService $service){
        $this->service = $service;
    }


    public function index(Request $request){
        return response()->json(
            $this->service->getAll($request->user())
        );
    }


    public function markAllAsRead(Request $request){
        $this->service->markAllAsRead($request->user());

        return response()->json(['message' => 'Todas as notificações foram marcadas como lidas']);
    }


    public function markAsRead($id){
        $this->service->markAsRead(auth()->user(), $id);

        return response()->json(['message' => 'Notificação marcada como lida.']);
    }
}
