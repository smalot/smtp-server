<?php


namespace SamIT\Tests\React\Smtp;


use SamIT\React\Smtp\Connection;
use SamIT\React\Smtp\Server;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {

        $loop   = $this->createLoopMock();
        $server = new Server($loop);

        $server->listen(0);
        $conn = new Connection($server->master, $loop);

        $this->assertInstanceOf(Connection::class, $conn);
    }

    private function createLoopMock()
    {
        return $this->createMock(\React\EventLoop\LoopInterface::class);
    }
}
