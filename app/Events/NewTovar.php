<?php

namespace App\Events;

use App\Models\TovariTransport;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewTovar implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tovar;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($tovar)
    {
        $this->tovar = $tovar;
    }

     /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('online');
    }

    public function broadcastAs()
    {
      // return 'privatechat.'.$this->message->receiver_id;
      return 'NewTovar';
    }
}
