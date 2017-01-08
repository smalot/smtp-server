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
     * @param string $password
     * @return bool
     */
    public function validateIdentity($password);
}
