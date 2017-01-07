<?php

namespace SamIT\React\Smtp\Auth;

/**
 * Class PlainMethod
 * @package SamIT\React\Smtp\Auth
 */
class PlainMethod
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * PlainMethod constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'PLAIN';
    }

    /**
     * @param string $token
     * @return $this
     */
    public function decodeToken($token)
    {
        list($username, $password) = explode(':', base64_decode($token), 2);

        $this->username = $username;
        $this->password = $password;

        return $this;
    }

    /**
     * @return bool
     */
    public function check()
    {
        var_dump('check', $this->username, $this->password);

        return true;
    }
}
