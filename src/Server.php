<?php

namespace SamIT\React\Smtp;

use React\EventLoop\LoopInterface;
use SamIT\React\Smtp\Auth\MethodInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Server constructor.
     * @param LoopInterface $loop
     */
    public function __construct(LoopInterface $loop, EventDispatcherInterface $dispatcher)
    {
        parent::__construct($loop);

        // We need to save $loop here since it is private for some reason.
        $this->loop = $loop;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param resource $socket
     * @return Connection
     */
    public function createConnection($socket)
    {
        $conn = new Connection($socket, $this->loop, $this, $this->dispatcher);

        $conn->recipientLimit = $this->recipientLimit;
        $conn->bannerDelay = $this->bannerDelay;
        $conn->authMethods = $this->authMethods;

        return $conn;
    }

    /**
     * @param MethodInterface $method
     * @return bool
     */
    public function checkAuth(MethodInterface $method)
    {
        return $method->validateIdentity('foo@gmail.com', 'foo@gmail.com');
    }
}
