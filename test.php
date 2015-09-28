<?php

include __DIR__ . '/vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$smtp = new SamIT\React\Smtp\Server($loop);


$request = new \SamIT\React\Smtp\Request();


$counter = 0;
$smtp->on('message', function($from, array $recipients, \SamIT\React\Smtp\Message $message, \SamIT\React\Smtp\Connection $connection) use (&$counter, $loop) {
    echo "Mail ($counter) from $from\n";
    var_dump($message);
    echo "Sleeping 6 seconds before rejecting.";
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

