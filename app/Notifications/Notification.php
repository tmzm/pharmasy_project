<?php

namespace App\Notifications;

use App\Http\Controllers\NotificationController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Notification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title;
    protected $body;
    protected $device_key;

    /**
     * Create a new notification instance.
     * @param $title
     * @param $body
     * @param $device_key
     */
    public function __construct($title,$body,$device_key)
    {
        $this->title = $title;
        $this->body = $body;
        $this->device_key = $device_key;

    }

    /**
     * Execute the notification.
     */
    public function handle(): void
    {
        NotificationController::notify($this->title,$this->body,$this->device_key);
    }
}
