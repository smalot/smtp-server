<?php

namespace SamIT\React\Smtp;

use React\EventLoop\LoopInterface;
use SamIT\React\Smtp\Auth\MethodInterface;

/**
 * Class Server
 * @package SamIT\React\Smtp
 */
class Server extends \React\Socket\Server
{
    /**
     * @var int
     */
    public $recipientLimit = 100;

    /**
     * @var int
     */
    public $bannerDelay = 0;

    /**
     * @var array
     */
    public $authMethods = [];

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * Server constructor.
     * @param LoopInterface $loop
     */
    public function __construct(LoopInterface $loop)
    {
        // We need to save $loop here since it is private for some reason.
        $this->loop = $loop;
        parent::__construct($loop);
    }

    /**
     * @param resource $socket
     * @return Connection
     */
    public function createConnection($socket)
    {
        $conn = new Connection($socket, $this->loop, $this);

        $conn->recipientLimit = $this->recipientLimit;
        $conn->bannerDelay = $this->bannerDelay;
        $conn->authMethods = $this->authMethods;
        // We let messages "bubble up" from the connection to the server.
        $conn->on('message', function() {
            $this->emit('message', func_get_args());
        });

        return $conn;
    }

    /**
     * @param MethodInterface $method
     * @return bool
     */
    public function checkAuth(MethodInterface $method)
    {
        echo 'Connection granted for '.$method->getUsername().PHP_EOL;

        return $method->validateIdentity('foo@gmail.com', 'foo@gmail.com');
    }
}
