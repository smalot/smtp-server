<?php

include __DIR__ . '/vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$smtp = new SamIT\React\Smtp\Server($loop);

$counter = 0;
$smtp->on('message', function($from, array $recipients, $body) use (&$counter) {
    echo "Mail ($counter) from $from\n";
    $counter++;
});

$smtp->listen(2525);


$loop->run();

