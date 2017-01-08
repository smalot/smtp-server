<?php

namespace SamIT\React\Smtp\Event;

use Psr\Log\LoggerInterface;
use SamIT\React\Smtp\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogSubscriber
 * @package SamIT\React\Smtp\Event
 */
class LogSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * LogSubscriber constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
          Events::CONNECTION_CHANGE_STATE => 'onConnectionChangeState',
          Events::CONNECTION_HELO_RECEIVED => 'onConnectionHeloReceived',
          Events::CONNECTION_AUTH_ACCEPTED => 'onConnectionAuthAccepted',
          Events::CONNECTION_AUTH_REFUSED => 'onConnectionAuthRefused',
          Events::CONNECTION_FROM_RECEIVED => 'onConnectionFromReceived',
          Events::CONNECTION_RCPT_RECEIVED => 'onConnectionRcptReceived',
          Events::CONNECTION_LINE_RECEIVED => 'onConnectionLineReceived',
          Events::MESSAGE_RECEIVED => 'onMessageReceived',
        ];
    }

    /**
     * @param ConnectionChangeStateEvent $event
     */
    public function onConnectionChangeState(ConnectionChangeStateEvent $event)
    {
        $this->logger->debug('State changed from '.$event->getOldState().' to '.$event->getNewState());
    }

    /**
     * @param ConnectionHeloReceivedEvent $event
     */
    public function onConnectionHeloReceived(ConnectionHeloReceivedEvent $event)
    {
        $this->logger->debug('Domain: '.$event->getDomain());
    }

    /**
     * @param ConnectionFromReceivedEvent $event
     */
    public function onConnectionFromReceived(ConnectionFromReceivedEvent $event)
    {
        $mail = $event->getMail();
        $name = $event->getName() ?: $mail;
        $this->logger->debug('From: '.$name.' <'.$mail.'>');
    }

    /**
     * @param ConnectionRcptReceivedEvent $event
     */
    public function onConnectionRcptReceived(ConnectionRcptReceivedEvent $event)
    {
        $mail = $event->getMail();
        $name = $event->getName() ?: $mail;
        $this->logger->debug('Rcpt: '.$name.' <'.$mail.'>');
    }

    /**
     * @param ConnectionLineReceivedEvent $event
     */
    public function onConnectionLineReceived(ConnectionLineReceivedEvent $event)
    {
        $this->logger->debug('Line: '.$event->getLine());
    }

    /**
     * @param ConnectionAuthAcceptedEvent $event
     */
    public function onConnectionAuthAccepted(ConnectionAuthAcceptedEvent $event)
    {
        $this->logger->debug('Auth used: '.$event->getAuthMethod()->getType());
        $this->logger->info('User granted: '.$event->getAuthMethod()->getUsername());
    }

    /**
     * @param ConnectionAuthRefusedEvent $event
     */
    public function onConnectionAuthRefused(ConnectionAuthRefusedEvent $event)
    {
        $this->logger->debug('Auth used: '.$event->getAuthMethod()->getType());
        $this->logger->error('User refused: '.$event->getAuthMethod()->getUsername());
    }

    /**
     * @param MessageReceivedEvent $event
     */
    public function onMessageReceived(MessageReceivedEvent $event)
    {
        $this->logger->info('Message received: '.strlen($event->getMessage()).' bytes length');
    }
}
