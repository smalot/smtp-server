<?php
namespace SamIT\React\Smtp;

trait MessageTrait
{
    use \GuzzleHttp\Psr7\MessageTrait;

    protected $recipients = [];
    public function withHeader($name, $value)
    {
        throw new \Exception('SMTP Message does not support replacing headers.');
    }

    public function withAddedHeader($name, $value)
    {
        $new = clone $this;
        $new->headers[] = [$name, $value];
        return $new;

    }

    public function getHeaderLine($name) {
        throw new \Exception('SMTP Message does not support merging headers with the same name.');
    }

    public function getHeader($header)
    {
        $result = [];
        $header = strtolower($header);
        foreach($this->headers as list($name, $value)) {
            if ($header == strtolower($name)) {
                $result[] = [$name, $value];
            }
        }
        return $result;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function hasHeader($header)
    {
        return !empty($this->getHeader($header));
    }

    public function withoutHeader($header)
    {
        throw new \Exception('SMTP Message does not support removing headers.');
    }

    /**
     * @inheritdoc
     */
    public function withRecipient($email, $name = null)
    {
        return $this->withRecipients([$email => $name]);
    }

   public function withRecipients(array $recipients)
   {
       $new = clone $this;
       $new->recipients = $recipients;
       return $new;
   }

    public function withAddedRecipient($email, $name = null)
    {
        $new = clone $this;
        $new->recipients[$email] = $name;
        return $new;
    }

    /**
     * Return an instance with the provided recipients added to the original recipient(s).
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @return self
     * @param array $recipients Array of email => name pairs.
     * @throws \InvalidArgumentException for invalid email.
     */
    public function withAddedRecipients(array $recipients)
    {
        $new = clone $this;
        $new->recipients = array_merge($this->recipients, $recipients);
        return $new;
    }


}
