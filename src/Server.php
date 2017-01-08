<?php

namespace Smalot\Smtp\Server;

use React\EventLoop\LoopInterface;
use Smalot\Smtp\Server\Auth\MethodInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Server
 * @package Smalot\Smtp\Server
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
        $connection = new Connection($socket, $this->loop, $this, $this->dispatcher);

        $connection->recipientLimit = $this->recipientLimit;
        $connection->bannerDelay = $this->bannerDelay;
        $connection->authMethods = $this->authMethods;

        return $connection;
    }

    /**
     * @param Connection $connection
     * @param MethodInterface $method
     * @return bool
     */
    public function checkAuth(Connection $connection, MethodInterface $method)
    {
        return true;
    }
}
