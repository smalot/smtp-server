<?php

namespace Smalot\Smtp\Server\Event;

use Smalot\Smtp\Server\Connection;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ConnectionChangeStateEvent
 * @package Smalot\Smtp\Server\Event
 */
class ConnectionChangeStateEvent extends Event
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $oldState;

    /**
     * @var string
     */
    protected $newState;

    /**
     * ConnectionChangeStateEvent constructor.
     * @param Connection $connection
     * @param string $oldState
     * @param string $newState
     */
    public function __construct(Connection $connection, $oldState, $newState)
    {
        $this->connection = $connection;
        $this->oldState = $oldState;
        $this->newState = $newState;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getOldState()
    {
        return $this->oldState;
    }

    /**
     * @return string
     */
    public function getNewState()
    {
        return $this->newState;
    }
}
