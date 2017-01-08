<?php

namespace SamIT\React\Smtp\Event;

use SamIT\React\Smtp\Connection;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class MessageReceivedEvent
 * @package SamIT\React\Smtp\Event
 */
class MessageReceivedEvent extends Event
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $message;

    /**
     * MessageReceivedEvent constructor.
     * @param Connection $connection
     * @param string $message
     */
    public function __construct(Connection $connection, $message)
    {
        $this->connection = $connection;
        $this->message = $message;
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
    public function getMessage()
    {
        return $this->message;
    }
}
