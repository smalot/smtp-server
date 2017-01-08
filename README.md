# SMTP Server

SMTP Server based on ReactPHP.

Widely inspired [SAM-IT/react-smtp](https://github.com/SAM-IT/react-smtp).

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SAM-IT/react-smtp/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SAM-IT/react-smtp/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/SAM-IT/react-smtp/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/SAM-IT/react-smtp/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/SAM-IT/react-smtp/badges/build.png?b=master)](https://scrutinizer-ci.com/g/SAM-IT/react-smtp/build-status/master)

Features:
* supports many concurrent SMTP connections
* supports anonymous connections
* supports PLAIN, LOGIN and CRAM-MD5 authentication methods
* use Symfony event dispatcher

It is advised to install additionnal PHP libraries:
* [events](https://pecl.php.net/package/event)
* [mailparse](https://pecl.php.net/package/mailparse)

## Security

By default, `username` and `password` are not checked. However, you can override the `Server` class to implement your own logic.

````php
class MyServer extends \SamIT\React\Smtp\Server
{
    /**
     * @param Connection $connection
     * @param MethodInterface $method
     * @return bool
     */
    public function checkAuth(Connection $connection, MethodInterface $method)
    {
        $username = $method->getUsername();
        $password = $this->getPasswordForUsername();
    
        return $method->validateIdentity($password);
    }
    
    /**
     * @param string $username
     * @return string
     */
    protected function getPasswordForUsername($username)
    {
        // @Todo: Load password from Database or somewhere else.
        $password = '';
    
        return $password;
    }
}
````

## Sample code

### Server side - launcher

````php
include 'vendor/autoload.php';

try {
    $dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();

    $logger = new \Monolog\Logger('log');
    $dispatcher->addSubscriber(new \SamIT\React\Smtp\Event\LogSubscriber($logger));
    
    $loop = React\EventLoop\Factory::create();
    $server = new \SamIT\React\Smtp\Server($loop, $dispatcher);
    // Enable 3 authentication methods.
    $server->authMethods = [
      \SamIT\React\Smtp\Connection::AUTH_METHOD_LOGIN,
      \SamIT\React\Smtp\Connection::AUTH_METHOD_PLAIN,
      \SamIT\React\Smtp\Connection::AUTH_METHOD_CRAM_MD5,
    ];
    // Listen on port 25.
    $server->listen(25);
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

Sample project code for both `client` and `server` parts.

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

doc: https://packagist.org/packages/php-mime-mail-parser/php-mime-mail-parser

However, need to install the `mailparse` PHP Extension.
To do such a thing, you need to install the `mbstring` PHP Extension, compile it with `PEAR` and enable the `mailparse` extension after the `mbstring` (using a higher digit).

It should be necessary to alter source code to remove the check on `mbstring` existence due to an error in this check.
