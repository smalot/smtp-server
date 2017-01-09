<?php

namespace Smalot\Smtp\Server;

use Smalot\Smtp\Server\Event\MessageSentEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Sendmail
 * @package Smalot\Smtp\Server
 */
class Sendmail
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Sendmail constructor.
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return bool
     */
    public function run()
    {
        if (0 === ftell(STDIN)) {
            $message = '';

            while (!feof(STDIN)) {
                $message .= fread(STDIN, 1024);
            }

            if (!is_null($this->dispatcher)) {
                $event = new MessageSentEvent($this, $message);
                $this->dispatcher->dispatch(Events::MESSAGE_SENT, $event);
            }

            return true;
        }

        return false;
    }
}
