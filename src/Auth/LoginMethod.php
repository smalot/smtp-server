<?php

namespace SamIT\React\Smtp\Auth;

/**
 * Class LoginMethod
 * @package SamIT\React\Smtp\Auth
 */
class LoginMethod
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
     * LoginMethod constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'LOGIN';
    }

    /**
     * @param string $user
     * @return $this
     */
    public function setUsername($user)
    {
        $this->username = base64_decode($user);

        return $this;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = base64_decode($password);

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
