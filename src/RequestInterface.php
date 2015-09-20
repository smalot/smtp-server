<?php
namespace SamIT\React\Smtp;

interface RequestInterface extends MessageInterface
{
    /**
     * Return an instance with the provided value replacing the original recipient(s).
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @return self
     * @param string $email
     * @param string $name
     * @throws \InvalidArgumentException for invalid email.
     */
    public function withRecipient($email, $name = null);

    /**
     * Return an instance with the provided values replacing the original recipient(s).
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @return self
     * @param array $recipients Array of email => name pairs.
     * @throws \InvalidArgumentException for invalid email.
     */
    public function withRecipients(array $recipients);

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
    public function withAddedRecipient($email, $name = null);

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
    public function withAddedRecipients(array $recipients);

}