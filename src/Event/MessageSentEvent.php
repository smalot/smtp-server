<?php

namespace Smalot\Smtp\Server\Event;

use Smalot\Smtp\Server\Sendmail;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class MessageSentEvent
 * @package Smalot\Smtp\Server\Event
 */
class MessageSentEvent extends Event
{
    /**
     * @var Sendmail
     */
    protected $sendmail;

    /**
     * @var string
     */
    protected $message;

    /**
     * MessageSentEvent constructor.
     * @param Sendmail $sendmail
     * @param string $message
     */
    public function __construct(Sendmail $sendmail, $message)
    {
        $this->sendmail = $sendmail;
        $this->message = $message;
    }

    /**
     * @return Sendmail
     */
    public function getSendmail()
    {
        return $this->sendmail;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
