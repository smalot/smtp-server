<?php

namespace Smalot\Smtp\Server\Event;

use Smalot\Smtp\Server\Auth\MethodInterface;
use Smalot\Smtp\Server\Connection;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ConnectionAuthAcceptedEvent
 * @package Smalot\Smtp\Server\Event
 */
class ConnectionAuthAcceptedEvent extends Event
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var MethodInterface
     */
    protected $authMethod;

    /**
     * ConnectionAuthAcceptedEvent constructor.
     * @param Connection $connection
     * @param MethodInterface $authMethod
     */
    public function __construct(Connection $connection, MethodInterface $authMethod)
    {
        $this->connection = $connection;
        $this->authMethod = $authMethod;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return MethodInterface
     */
    public function getAuthMethod()
    {
        return $this->authMethod;
    }
}
