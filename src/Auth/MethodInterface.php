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
     * @return bool
     */
    public function check();
}
