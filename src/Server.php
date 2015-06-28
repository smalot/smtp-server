<?php

namespace SamIT\React\Smtp;

use Evenement\EventEmitter;
use React\EventLoop\LoopInterface;
use React\Http\ServerInterface;
use React\Socket\ServerInterface as SocketServerInterface;
use React\Socket\ConnectionInterface;

/** @event request */
class Server extends \React\Socket\Server
{
    private $loop;
    public function __construct(LoopInterface $loop)
    {
        // We need to save $loop here since it is private for some reason.
        $this->loop = $loop;
        parent::__construct($loop);
    }

    public function createConnection($socket)
    {
        $conn = new Connection($socket, $this->loop);
        // We let messages "bubble up" from the connection to the server.
        $conn->on('message', function() {
            $this->emit('message', func_get_args());
        });
        return $conn;
    }
}
