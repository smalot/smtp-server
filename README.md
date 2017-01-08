# react-smtp

SMTP Server based on ReactPHP

It supports many concurrent SMTP connections.

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SAM-IT/react-smtp/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SAM-IT/react-smtp/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/SAM-IT/react-smtp/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/SAM-IT/react-smtp/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/SAM-IT/react-smtp/badges/build.png?b=master)](https://scrutinizer-ci.com/g/SAM-IT/react-smtp/build-status/master)

## Sample code

### Server side

````php
include 'vendor/autoload.php';

try {
    $loop = React\EventLoop\Factory::create();
    $server = new \SamIT\React\Smtp\Server($loop);
    $server->authMethods = [\SamIT\React\Smtp\Connection::AUTH_METHOD_LOGIN, \SamIT\React\Smtp\Connection::AUTH_METHOD_PLAIN];
    $server->listen(25);
    $server->on('message', function($from, array $recipients, $message, \SamIT\React\Smtp\Connection $connection) {
        echo 'Message received'.PHP_EOL.'--------------------------'.PHP_EOL;
        echo $message.PHP_EOL;
    });
    $loop->run();
}
catch(\Exception $e) {
    var_dump($e);
}
````

### Client side

````php
include 'vendor/autoload.php';

try {
    $mail = new PHPMailer();

    $mail->isSMTP();
    $mail->Host = 'localhost';
    $mail->Port = 25;
    $mail->SMTPDebug = true;

    $mail->SMTPAuth = true;
    $mail->Username = "foo@gmail.com";
    $mail->Password = "foo@gmail.com";

    $mail->setFrom('from@example.com', 'Mailer');
    $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
    $mail->addAddress('ellen@example.com');               // Name is optional
    $mail->addReplyTo('info@example.com', 'Information');
    $mail->addCC('cc@example.com');
    $mail->addBCC('bcc@example.com');

    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    if(!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message has been sent';
    }
}
catch(\Exception $e) {
    var_dump($e);
}
````

### Composer

````json
{
    "require": {
        "react/event-loop": "^0.4.2",
        "sam-it/react-smtp": "dev-master",
        "phpmailer/phpmailer": "^5.2"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:smalot/react-smtp.git"
        }
    ]
}
````

### Decode message

Using the `"php-mime-mail-parser/php-mime-mail-parser": "^2.6"` package, you can parse the whole message.

However, need to install the `mailparse` PHP Extension.
To do such a thing, you need to install the `mbstring` PHP Extension, compile it with `PEAR` and enable the `mailparse` extension after the `mbstring` (using a higher digit).

It should be necessary to alter source code to remove the check on `mbstring` existence due to an error in this check.
