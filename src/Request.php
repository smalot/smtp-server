<?php
namespace SamIT\React\Smtp;

/**
 * Description of Request
 *
 * @author Sam
 */
class Request implements RequestInterface 
{
    use \GuzzleHttp\Psr7\MessageTrait {
        withHeader as withParentHeader;
    }

    /** @var array Cached HTTP header collection with lowercase key to values */
    private $recipients = [];


    public function withAddedRecipient($email, $name = null)
    {
        $this->validateEmail($email);
        $new = clone $this;
        $new->recipients[$email] = $name;
        return $new;
    }

    public function withAddedRecipients(array $recipients) 
    {
        $new = clone $this;
        foreach($recipients as $email => $name) {
            $this->validateEmail($email);
            $new->recipients[$email] = $name;
        }
        return $new;
    }

    public function withRecipient($email, $name = null)
    {
        $this->validateEmail($email);
        $new = clone $this;
        $new->recipients = [];
        $new->recipients[$email] = $name;
        return $new;
    }

    public function withRecipients(array $recipients) {
        $new = clone $this;
        $new->recipients = [];
        foreach($recipients as $email => $name) {
            $this->validateEmail($email);
            $new->recipients[$email] = $name;
        }
        return $new;
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function validateEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("$email is not a valid email address");
        }
    }

}
