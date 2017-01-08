<?php

namespace SamIT\React\Smtp;

use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;
use React\Socket\ConnectionInterface;
use React\Stream\Stream;
use SamIT\React\Smtp\Auth\LoginMethod;
use SamIT\React\Smtp\Auth\MethodInterface;
use SamIT\React\Smtp\Auth\PlainMethod;

/**
 * Class Connection
 * @package SamIT\React\Smtp
 */
class Connection extends Stream implements ConnectionInterface
{
    const AUTH_METHOD_PLAIN = 'PLAIN';
    const AUTH_METHOD_LOGIN = 'LOGIN';

    const STATUS_NEW = 0;
    const STATUS_AUTH = 1;
    const STATUS_INIT = 2;
    const STATUS_FROM = 3;
    const STATUS_TO = 4;
    const STATUS_DATA = 5;

    const DELIMITER = "\r\n";

    /**
     * This status is used when all mail data has been received and the system is deciding whether to accept or reject.
     */
    const STATUS_PROCESSING = 6;

    /**
     * @var array
     */
    protected $states = [
      self::STATUS_NEW => [
        'Helo' => 'HELO',
        'Ehlo' => 'EHLO',
        'Quit' => 'QUIT',
      ],
      self::STATUS_AUTH => [
        'Auth' => 'AUTH',
        'Quit' => 'QUIT',
        'Reset' => 'RSET',
        'Login' => '',
      ],
      self::STATUS_INIT => [
        'MailFrom' => 'MAIL FROM',
        'Quit' => 'QUIT',
        'Reset' => 'RSET',
      ],
      self::STATUS_FROM => [
        'RcptTo' => 'RCPT TO',
        'Quit' => 'QUIT',
        'Reset' => 'RSET',
      ],
      self::STATUS_TO => [
        'RcptTo' => 'RCPT TO',
        'Quit' => 'QUIT',
        'Data' => 'DATA',
        'Reset' => 'RSET',
      ],
      self::STATUS_DATA => [
        'Line' => '' // This will match any line.
      ],
      self::STATUS_PROCESSING => [

      ],
    ];

    /**
     * @var int
     */
    protected $state = self::STATUS_NEW;

    /**
     * @var string
     */
    protected $lastCommand = '';

    /**
     * @var string
     */
    protected $banner = 'Welcome to ReactPHP SMTP Server';

    /**
     * @var bool Accept messages by default
     */
    protected $acceptByDefault = true;

    /**
     * If there are event listeners, how long will they get to accept or reject a message?
     * @var int
     */
    protected $defaultActionTimeout = 0;

    /**
     * The timer for the default action, canceled in [accept] and [reject]
     * @var TimerInterface
     */
    protected $defaultActionTimer;

    /**
     * The current line buffer used by handleData.
     * @var string
     */
    protected $lineBuffer = '';

    /**
     * @var string
     */
    protected $from;

    /**
     * @var array
     */
    protected $recipients = [];

    /**
     * @var array
     */
    protected $authMethods = [];

    /**
     * @var MethodInterface
     */
    protected $authMethod;

    /**
     * @var string
     */
    protected $login;

    /**
     * @var string
     */
    protected $rawContent;

    /**
     * @var int
     */
    public $bannerDelay = 0;

    /**
     * @var int
     */
    public $recipientLimit = 100;

    /**
     * Connection constructor.
     * @param resource $stream
     * @param \React\EventLoop\LoopInterface $loop
     */
    public function __construct($stream, LoopInterface $loop)
    {
        parent::__construct($stream, $loop);
        stream_get_meta_data($stream);
        // We sleep for 3 seconds, if client does not wait for our banner we disconnect.
        $disconnect = function ($data, ConnectionInterface $conn) {
            $conn->end("I can break rules too, bye.\n");
        };
        $this->on('data', $disconnect);
        $this->reset(self::STATUS_NEW);
        $this->on('line', [$this, 'handleCommand']);
        if ($this->bannerDelay > 0) {
            $loop->addTimer(
              $this->bannerDelay,
              function () use ($disconnect) {
                  $this->sendReply(220, $this->banner);
                  $this->removeListener('data', $disconnect);
              }
            );
        } else {
            $this->sendReply(220, $this->banner);
        }
    }

    /**
     * We read until we find an end of line sequence for SMTP.
     * http://www.jebriggs.com/blog/2010/07/smtp-maximum-line-lengths/
     * @param $stream
     */
    public function handleData($stream)
    {
        // Socket is raw, not using fread as it's interceptable by filters
        // See issues #192, #209, and #240
        $data = stream_socket_recvfrom($stream, $this->bufferSize);;

        $limit = $this->state == self::STATUS_DATA ? 1000 : 512;
        if ('' !== $data && false !== $data) {
            $this->lineBuffer .= $data;
            if (strlen($this->lineBuffer) > $limit) {
                $this->sendReply(500, "Line length limit exceeded.");
                $this->lineBuffer = '';
            }

            $delimiter = self::DELIMITER;
            while (false !== $pos = strpos($this->lineBuffer, $delimiter)) {
                $line = substr($this->lineBuffer, 0, $pos);
                $this->lineBuffer = substr($this->lineBuffer, $pos + strlen($delimiter));
                $this->emit('line', [$line, $this]);
            }
        }

        if ('' === $data || false === $data || !is_resource($stream) || feof($stream)) {
            $this->end();
        }
    }

    /**
     * Parses the command from the beginning of the line.
     *
     * @param string $line
     * @return string
     */
    protected function parseCommand(&$line)
    {
        if ($line) {
            foreach ($this->states[$this->state] as $key => $candidate) {
                if (strncasecmp($candidate, $line, strlen($candidate)) == 0) {
                    $line = substr($line, strlen($candidate));
                    $this->lastCommand = $key;

                    return $key;
                }
            }
        }

        if ($this->lastCommand == 'Line') {
            return 'Line';
        }

        return null;
    }

    protected function handleCommand($line)
    {
        $command = $this->parseCommand($line);

        if ($command == null) {
            $this->sendReply(500, "Unexpected or unknown command: ".$line);
            $this->sendReply(500, $this->states[$this->state]);

        } else {
            $func = "handle{$command}Command";
            $this->$func($line);
        }
    }

    protected function sendReply($code, $message = '', $close = false)
    {
        $out = '';
        if (is_array($message)) {
            $last = array_pop($message);
            foreach ($message as $line) {
                $out .= "$code-$line\r\n";
            }
            $this->write($out);
            $message = $last;
        }
        if ($close) {
            $this->end("$code $message\r\n");
        } else {
            $this->write("$code $message\r\n");
        }

    }

    /**
     * @param string $domain
     */
    protected function handleResetCommand($domain)
    {
        $this->reset();
        $this->sendReply(250, "Reset OK");
    }

    /**
     * @param string $domain
     */
    protected function handleHeloCommand($domain)
    {
        $messages = [
          "Hello {$this->getRemoteAddress()}",
        ];
        if ($this->authMethods) {
            $this->state = self::STATUS_AUTH;
            $messages[] = 'AUTH '.implode(' ', $this->authMethods);
        } else {
            $this->state = self::STATUS_INIT;
        }
        $this->sendReply(250, $messages);
    }

    /**
     * @param string $domain
     */
    protected function handleEhloCommand($domain)
    {
        $this->handleHeloCommand($domain);
    }

    /**
     * @param string $method
     */
    protected function handleAuthCommand($method)
    {
        list($method, $token) = explode(' ', trim($method), 2);

        switch (strtoupper($method)) {
            case self::AUTH_METHOD_PLAIN:
                $this->authMethod = new PlainMethod();

                if (!isset($token)) {
                    $this->sendReply(334);

                    return;
                } else {
                    // Plain auth accepts token in the same call.
                    $this->authMethod->decodeToken($token);

                    if ($this->authMethod->check()) {
                        $this->state = self::STATUS_INIT;

                        return;
                    }

                    $this->sendReply(235, '2.7.0 Authentication successful');
                }
                break;

            case self::AUTH_METHOD_LOGIN:
                $this->authMethod = new LoginMethod();
                // Send 'Username:'.
                $this->sendReply(334, 'VXNlcm5hbWU6');

                return;
        }

        $this->sendReply(530, "5.7.0 Authentication required");
        $this->reset();
    }

    /**
     * @param string $value
     */
    protected function handleLoginCommand($value)
    {
        if ($this->authMethod instanceof LoginMethod) {
            if (!$this->authMethod->getUsername()) {
                $this->authMethod->setUsername($value);

                // Send 'Password:'.
                $this->sendReply(334, 'UGFzc3dvcmQ6');

                return;
            } else {
                $this->authMethod->setPassword($value);

                if ($this->authMethod->check()) {
                    $this->login = $this->authMethod->getUsername();
                    $this->state = self::STATUS_INIT;
                    $this->sendReply(235, '2.7.0 Authentication successful');

                    return;
                }
            }
        } elseif ($this->authMethod instanceof PlainMethod) {
            $this->authMethod->decodeToken($value);

            if ($this->authMethod->check()) {
                $this->login = $this->authMethod->getUsername();
                $this->state = self::STATUS_INIT;
                $this->sendReply(235, '2.7.0 Authentication successful');

                return;
            }
        }

        $this->sendReply(530, "5.7.0 Authentication required");
        $this->reset();
    }

    /**
     * @param mixed $arguments
     */
    protected function handleMailFromCommand($arguments)
    {
        // Parse the email.
        if (preg_match('/:\s*\<(?<email>.*)\>( .*)?/', $arguments, $matches) == 1) {
            if (!$this->login && count($this->authMethods)) {
                $this->sendReply(530, "5.7.0 Authentication required");
                $this->reset();

                return;
            }

            $this->state = self::STATUS_FROM;
            $this->from = $matches['email'];
            $this->sendReply(250, "MAIL OK");
        } else {
            $this->sendReply(500, "Invalid mail argument");
        }
    }

    /**
     * @param mixed $arguments
     */
    protected function handleQuitCommand($arguments)
    {
        $this->sendReply(221, "Goodbye.", true);
    }

    /**
     * @param mixed $arguments
     */
    protected function handleRcptToCommand($arguments)
    {
        // Parse the recipient.
        if (preg_match('/:\s*(?<name>.*?)?\<(?<email>.*)\>( .*)?/', $arguments, $matches) == 1) {
            // Always set to 3, since this command might occur multiple times.
            $this->state = self::STATUS_TO;
            $this->recipients[$matches['email']] = $matches['name'];
            $this->sendReply(250, "Accepted");
        } else {
            $this->sendReply(500, "Invalid RCPT TO argument.");
        }
    }

    /**
     * @param mixed $arguments
     */
    protected function handleDataCommand($arguments)
    {
        $this->state = self::STATUS_DATA;
        $this->sendReply(354, "Enter message, end with CRLF . CRLF");
    }

    /**
     * @param string $line
     */
    protected function handleLineCommand($line)
    {
        if ($line === '.') {
            $this->state = self::STATUS_PROCESSING;

            /**
             * Default action, using timer so that callbacks above can be called asynchronously.
             */
            $this->defaultActionTimer = $this->loop->addTimer(
              $this->defaultActionTimeout,
              function () {
                  if ($this->acceptByDefault) {
                      $this->accept();
                  } else {
                      $this->reject();
                  }
              }
            );

            $this->emit(
              'message',
              [
                'from' => $this->from,
                'recipients' => $this->recipients,
                'message' => $this->rawContent,
                'connection' => $this,
              ]
            );
        } else {
            $this->rawContent .= $line.self::DELIMITER;
        }
    }

    /**
     * Reset the SMTP session.
     * By default goes to the initialized state (ie no new EHLO or HELO is required / possible.)
     *
     * @param int $state The state to go to.
     */
    protected function reset($state = self::STATUS_INIT)
    {
        $this->state = $state;
        $this->lastCommand = '';
        $this->from = null;
        $this->recipients = [];
        $this->rawContent = '';
        $this->authMethod = false;
        $this->login = false;
    }

    /**
     * @param string $message
     */
    public function accept($message = "OK")
    {
        if ($this->state != self::STATUS_PROCESSING) {
            throw new \DomainException("SMTP Connection not in a valid state to accept a message.");
        }
        $this->loop->cancelTimer($this->defaultActionTimer);
        unset($this->defaultActionTimer);
        $this->sendReply(250, $message);
        $this->reset();
    }

    /**
     * @param int $code
     * @param string $message
     */
    public function reject($code = 550, $message = "Message not accepted")
    {
        if ($this->state != self::STATUS_PROCESSING) {
            throw new \DomainException("SMTP Connection not in a valid state to reject message.");
        }
        $this->defaultActionTimer->cancel();
        unset($this->defaultActionTimer);
        $this->sendReply($code, $message);
        $this->reset();
    }

    /**
     * Delay the default action by $seconds.
     * @param int $seconds
     */
    public function delay($seconds)
    {
        if (isset($this->defaultActionTimer)) {
            $this->defaultActionTimer->cancel();
            $this->defaultActionTimer = $this->loop->addTimer($seconds, $this->defaultActionTimer->getCallback());
        }
    }

    /**
     * @inheritdoc
     */
    public function handleClose()
    {
        if (is_resource($this->stream)) {
            // @see http://chat.stackoverflow.com/transcript/message/7727858#7727858
            stream_socket_shutdown($this->stream, STREAM_SHUT_RDWR);
            stream_set_blocking($this->stream, false);
            fclose($this->stream);
        }
    }

    /**
     * @param string $address
     * @return string
     */
    protected function parseAddress($address)
    {
        return trim(substr($address, 0, strrpos($address, ':')), '[]');
    }

    /**
     * @return string
     */
    public function getRemoteAddress()
    {
        return $this->parseAddress(stream_socket_get_name($this->stream, true));
    }

}
