<?php

include __DIR__ . '/vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$smtp = new SamIT\React\Smtp\Server($loop);

$dnsResolverFactory = new React\Dns\Resolver\Factory();
$dns = $dnsResolverFactory->createCached('8.8.8.8', $loop);

$connector = new React\SocketClient\Connector($loop, $dns);


$server = new \React\Socket\Server($loop);
$server->listen(10000, '0.0.0.0');
$server->on('connection', function(\React\Socket\Connection $connection) use ($connector) {
    echo "Connected.\n";
    $forward = $connector->create('127.0.0.1', 25000);
    list($sourceAddress, $sourcePort) = explode(':', stream_socket_get_name($connection->stream, true));
    list($targetAddress, $targetPort) = explode(':', stream_socket_get_name($connection->stream, false));
    $header = \SamIT\Proxy\Header::createForward4($sourceAddress, $sourcePort, $targetAddress, $targetPort)->__toString();
    $forward->then(function(React\Stream\Stream $stream) use ($connection, $header) {
        echo "Pipe set up.";
        $stream->write($header);
        var_dump($header);
        \React\Stream\Util::pipe($stream, $connection);
        \React\Stream\Util::pipe($connection,  $stream);
    });


});

$counter = 0;
$smtp->on('connection', function(\SamIT\React\Smtp\Connection $connection) {
    list($sourceAddress, $sourcePort) = explode(':', stream_socket_get_name($connection->stream, true));
    list($targetAddress, $targetPort) = explode(':', stream_socket_get_name($connection->stream, false));

    var_dump(bin2hex(\SamIT\Proxy\Header::createForward4($sourceAddress, $sourcePort, $targetAddress, $targetPort)->__toString()));

});

$smtp->on('message', function($from, array $recipients, \SamIT\React\Smtp\Message $message, \SamIT\React\Smtp\Connection $connection) use (&$counter, $loop) {
    echo "Mail ($counter) from $from\n";
    echo "Sleeping 6 seconds before rejecting.";
    $connection->delay(10);
    var_dump($connection->getRemoteAddress());

    $loop->addTimer(6, function() use ($connection) {
        try {
            $connection->reject(550, "I don't like messages.");
        } catch (\DomainException $e) {
            echo "Exception: " . $e->getMessage() . "\n";
        }
    });
    $counter++;
});

$smtp->listen(2525);


$loop->run();


