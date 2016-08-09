<?php


namespace SamIT\React\Smtp;


use React\Socket\ConnectionInterface;

/**
 * Class Sender
 * This class will send messages over connections.
 * @package SamIT\React\Smtp
 */
class Sender
{

    private $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }


    /**
     * @param ConnectionInterface $connection
     * @param MessageInterface $message
     * @param Request $request
     */
    public function send(
        ConnectionInterface $connection,
        MessageInterface $message,
        Request $request
    ) {

//        $connection->on

    }

}