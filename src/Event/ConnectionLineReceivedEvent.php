<?php

namespace SamIT\React\Smtp\Event;

use SamIT\React\Smtp\Auth\MethodInterface;
use SamIT\React\Smtp\Connection;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ConnectionLineReceivedEvent
 * @package SamIT\React\Smtp\Event
 */
class ConnectionLineReceivedEvent extends Event
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $line;

    /**
     * ConnectionLineReceivedEvent constructor.
     * @param Connection $connection
     * @param string $line
     */
    public function __construct(Connection $connection, $line)
    {
        $this->connection = $connection;
        $this->line = $line;
    }

    /**
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }
}
