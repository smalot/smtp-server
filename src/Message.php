<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 6/28/15
 * Time: 3:13 PM
 */

namespace SamIT\React\Smtp;

use GuzzleHttp\Psr7\MessageTrait;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Implements an SMTP message based on HTTP message defined in PSR-7.
 * @package SamIT\React\Smtp
 */
class Message implements  MessageInterface
{
    use MessageTrait;

}