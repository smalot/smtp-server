<?php

namespace Smalot\Smtp\Server\Auth;

/**
 * Interface MethodInterface
 * @package Smalot\Smtp\Server\Auth
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
