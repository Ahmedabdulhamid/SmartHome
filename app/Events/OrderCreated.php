<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
class OrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $order,$adminId;
    public function __construct($order,$admin)
    {
       $this->order = $order->load('items');
        $this->adminId=$admin->id;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
   public function broadcastOn(): array
    {
        $channelName = 'admin.orders.' . $this->adminId;



        return [
            new Channel($channelName),
        ];
    }
    public function broadcastAs()
    {
        return 'order.created';
    }
}
