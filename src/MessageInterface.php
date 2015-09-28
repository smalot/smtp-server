<?php
namespace SamIT\React\Smtp;

use React\Dns\BadServerException;

interface MessageInterface extends \Psr\Http\Message\MessageInterface {
    /**
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is a string associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $index => [$name, $value]) {
     *         echo $name . ": " . $value;
     *     }
     *
     * While header names are not case-sensitive, getHeaders() must preserve the
     * exact case in which headers were originally specified. It must also preserve the order of headers, appending
     * new ones to the end. This is especially important when signing schemes like DKIM are in use.
     *
     * @return array Returns an array of the message's headers. Each
     *     key a header index, and each value MUST be an array of name, value pairs for that header.
     */
    public function getHeaders();

    /**
     * This function will throw an exception since replacing headers is will mangle SMTP messages.
     * @throws \Exception
     */
    public function withHeader($name, $value);

    /**
     * This function will throw an exception since removing headers is will mangle SMTP messages.
     * @throws \Exception
     */
    public function withoutHeader($name);

    /**
     * This function will throw an exception since removing headers is will mangle SMTP messages.
     * @throws \Exception
     */
    public function getHeaderLine($name);

    /**
     * @inheritdoc
     */
    public function withAddedHeader($name, $value);

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name Case-insensitive header field name.
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array. The keys are the names of the header.
     */
    public function getHeader($name);



}