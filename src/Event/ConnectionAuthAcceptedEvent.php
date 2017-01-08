<?php

namespace SamIT\React\Smtp\Event;

use SamIT\React\Smtp\Auth\MethodInterface;
use SamIT\React\Smtp\Connection;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ConnectionAuthAcceptedEvent
 * @package SamIT\React\Smtp\Event
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
