<?php


namespace SamIT\Tests\React\Smtp;


use SamIT\React\Smtp\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {

        $loop   = $this->createLoopMock();
        $server = new Server($loop);

        $this->assertInstanceOf(Server::class, $server);
    }

    private function createLoopMock()
    {
        return $this->createMock(\React\EventLoop\LoopInterface::class);
    }
}
