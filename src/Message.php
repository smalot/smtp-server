<?php
namespace SamIT\React\Smtp;


use GuzzleHttp\Psr7\MessageTrait;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class Message implements MessageInterface
{
   use MessageTrait;
}