<?php

namespace Smalot\Smtp\Server\Event;

use Smalot\Smtp\Server\Connection;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ConnectionHeloReceivedEvent
 * @package Smalot\Smtp\Server\Event
 */
class ConnectionHeloReceivedEvent extends Event
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $domain;

    /**
     * ConnectionHeloReceivedEvent constructor.
     * @param Connection $connection
     * @param string $domain
     */
    public function __construct(Connection $connection, $domain)
    {
        $this->connection = $connection;
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }
}
