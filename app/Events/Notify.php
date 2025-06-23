<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Notify implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( $arr ) {

        $arr = [
            'title'       => $arr['title'] ?? null,
            'description' => $arr['description'] ?? null,
            'user_id'     => $arr['user_id'] ?? null,
            'admin_id'    => $arr['admin_id'] ?? null,
        ];
        $noti = Notification::create( $arr );

        $data          = $noti ? $noti->toArray() : [];
        $data['new']   = true;
        $this->message = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        // return new PrivateChannel( 'channel-name' );
        return new Channel( 'notify-channel' );
    }
}
