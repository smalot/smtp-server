<?php

include __DIR__ . '/vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$smtp = new SamIT\React\Smtp\Server($loop);

$dnsResolverFactory = new React\Dns\Resolver\Factory();
$dns = $dnsResolverFactory->createCached('8.8.4.4', $loop);

$forwarder = new \SamIT\Proxy\Forwarder($loop, $dns);



$server = new \React\Socket\Server($loop);
$server->listen(10000, '0.0.0.0');
$server->on('connection', function(\React\Socket\Connection $connection) use ($forwarder) {
    echo "Connected.\n";
    $connection->on('data', function($data) {
        echo ">> " . $data;
    });

    $f = $forwarder->forward($connection, '127.0.0.1', 10001);
    var_dump($f);




});

$dumper = new \React\Socket\Server($loop);
$dumper->listen(10001, '0.0.0.0');
$dumper->on('connection', function(\React\Socket\Connection $connection) {
    echo "Dumper connected\n";
   $connection->on('data', function($data, \React\Socket\Connection $connection) {
       echo "D: " . $data;
       if (is_numeric(trim($data))) {
           $connection->write(trim($data) + 5 . "\n");
       }
   });
});
$counter = 0;

//
//$smtp->on('message', function($from, array $recipients, \SamIT\React\Smtp\Message $message, \SamIT\React\Smtp\Connection $connection) use (&$counter, $loop) {
//    echo "Mail ($counter) from $from\n";
//    echo "Sleeping 6 seconds before rejecting.";
//    $connection->delay(10);
//    var_dump($connection->getRemoteAddress());
//
//    $loop->addTimer(6, function() use ($connection) {
//        try {
//            $connection->reject(550, "I don't like messages.");
//        } catch (\DomainException $e) {
//            echo "Exception: " . $e->getMessage() . "\n";
//        }
//    });
//    $counter++;
//});
//
//$smtp->listen(2525);
//

$loop->run();


