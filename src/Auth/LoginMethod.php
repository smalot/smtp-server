<?php

namespace SamIT\React\Smtp\Auth;

/**
 * Class LoginMethod
 * @package SamIT\React\Smtp\Auth
 */
class LoginMethod implements MethodInterface
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
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
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
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
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
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function validateIdentity($username, $password)
    {
        return $password == $this->password;
    }
}
