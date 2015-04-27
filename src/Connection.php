<?php
namespace SamIT\React\Smtp;


use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;

class Connection extends \React\Socket\Connection{

    protected $states = [
        [
            'Helo' => 'HELO',
            'Ehlo' => 'EHLO',
            'Quit' => 'QUIT'

        ], [
            'MailFrom' => 'MAIL FROM',
            'Quit' => 'QUIT',
            'Reset' => 'RSET'
        ], [
            'RcptTo' => 'RCPT TO',
            'Quit' => 'QUIT',
            'Reset' => 'RSET'
        ], [
            'RcptTo' => 'RCPT TO',
            'Quit' => 'QUIT',
            'Data' => 'DATA',
            'Reset' => 'RSET'
        ], [
            'Line' => '' // This will match any line.
        ]


    ];

    protected $state = 0;

    protected $banner = 'Welcome to ReactPHP Smtp';

    protected $lineBuffer;
    protected $from;
    protected $recipients = [];
    protected $body = [];
    /**
     * @var Server
     */
    private $server;

    public function __construct($stream, LoopInterface $loop)
    {
        parent::__construct($stream, $loop);

        // We sleep for 3 seconds, if client does not wait for our banner we disconnect.
        $disconnect = function($data, ConnectionInterface $conn) {
            $conn->end("I can break rules too, bye.\n");
        };
        $this->on('data', $disconnect);
        $loop->addTimer(2, function() use ($disconnect) {
            $this->sendReply(220, $this->banner);
            $this->removeListener('data', $disconnect);
            $this->on('data', [$this, 'handleCommand']);
        });
    }


    protected function parseCommand(&$line)
    {
        foreach ($this->states[$this->state] as $key => $candidate) {
            if (strncasecmp($candidate, $line, strlen($candidate)) == 0) {
                $line = substr($line, strlen($candidate));
                return $key;
            }
        }
    }

    public function handleCommand($data)
    {
        $lines = explode("\r\n", $data);
        foreach ($lines as $line) {
            if ($line !=='') {
                $command = $this->parseCommand($line);
                if ($command == null) {
                    $this->sendReply(500, "Unexpected or unknown command.");
                    $this->sendReply(500, $this->states[$this->state]);

                } else {
                    $func = "handle{$command}Command";
                    $this->$func($line);
                }
            }
        }

    }

    protected function sendReply($code, $message, $close = false)
    {
        $out = '';
        if (is_array($message)) {
            $last = array_pop($message);
            foreach($message as $line) {
                $out .= "$code-$line\r\n";
            }
            $this->write($out);
            $message = $last;
        }
        if ($close) {
            $this->end("$code $message\r\n");
        } else {
//            echo "$code $message\r\n";
            $this->write("$code $message\r\n");
        }

    }

    protected function handleResetCommand($domain)
    {
        $this->reset();
        $this->sendReply(250, "Reset OK");
    }
    protected function handleHeloCommand($domain)
    {
        $this->state++;
        $this->sendReply(250, "Hello {$this->getRemoteAddress()}");
    }

    protected function handleEhloCommand($domain)
    {
        $this->state++;
        $this->sendReply(250, "Hello {$this->getRemoteAddress()}");
    }

    protected function handleMailFromCommand($arguments)
    {

        // Parse the email.
        if (preg_match('/:\s*\<(?<email>.*)\>( .*)?/', $arguments, $matches) == 1) {
            $this->state = 2;
            $this->from  = $matches['email'];
            $this->sendReply(250, "MAIL OK");
        } else {
            $this->sendReply(500, "Invalid mail argument");
        }

    }

    protected function handleQuitCommand($arguments)
    {
        $this->sendReply(221, "Goodbye.", true);

    }

    protected function handleRcptToCommand($arguments) {
        // Parse the recipient.
        if (preg_match('/:\s?\<(?<email>.*)\>( .*)?/', $arguments, $matches) == 1) {
            // Always set to 3, since this command might occur multiple times.
            $this->state = 3;
            $this->recipients[] = $matches['email'];
            $this->sendReply(250, "Accepted");
        } else {
            $this->sendReply(500, "Invalid rcpt argument");
        }
    }

    public function handleDataCommand($arguments)
    {
        $this->state++;
        $this->sendReply(354, "Enter message, end with CRLF . CRLF");
    }

    public function handleLineCommand($arguments)
    {
        if ($arguments === '.') {
            $this->sendReply(250, 'OK');
            $this->emit('message', [
                'from' => $this->from,
                'recipients' => $this->recipients,
                'body' => $this->body
            ]);
            $this->reset();
        } else {
            $this->body[] = $arguments;
        }

    }

    protected function reset() {
        $this->state = 1;
        $this->from = null;
        $this->lineBuffer = '';
        $this->recipients = [];
        $this->body = [];
    }


}