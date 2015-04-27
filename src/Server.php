<?php

namespace SamIT\Smtp;

use Evenement\EventEmitter;
use React\EventLoop\LoopInterface;
use React\Http\ServerInterface;
use React\Socket\ServerInterface as SocketServerInterface;
use React\Socket\ConnectionInterface;
use SamIT\Smtp\Connection;

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
        $conn->on('message', function($from, $recipients, $body) {
            $this->emit('message', compact('from', 'recipients', 'body'));
        });
        return $conn;
    }
}
