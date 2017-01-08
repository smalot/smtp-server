<?php

namespace SamIT\React\Smtp\Auth;

/**
 * Interface MethodInterface
 * @package SamIT\React\Smtp\Auth
 */
interface MethodInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function validateIdentity($username, $password);
}
