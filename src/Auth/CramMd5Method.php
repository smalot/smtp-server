<?php

namespace SamIT\React\Smtp\Auth;

/**
 * Class CramMd5Method
 * @package SamIT\React\Smtp\Auth
 */
class CramMd5Method implements MethodInterface
{
    /**
     * @var string
     */
    protected $challenge;

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
        $this->challenge = $this->generateChallenge();
    }

    /**
     * @return string
     */
    protected function generateChallenge()
    {
        $strong = true;
        $random = openssl_random_pseudo_bytes(32, $strong);
        $challenge = '<'.bin2hex($random).'@react-smtp.tld>';

        return $challenge;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'CRAM-MD5';
    }

    /**
     * @return string
     */
    public function getChallenge()
    {
        return base64_encode($this->challenge);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $token
     * @return $this
     */
    public function decodeToken($token)
    {
        list($username, $password) = explode(' ', base64_decode($token));

        $this->username = $username;
        $this->password = $password;

        return $this;
    }

    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function validateIdentity($username, $password)
    {
        $hashMd5 = $this->_hmacMd5($password, $this->challenge);

        return $hashMd5 == $this->password;
    }

    /**
     * @see https://github.com/AOEpeople/Menta_GeneralComponents/blob/master/lib/Zend/Mail/Protocol/Smtp/Auth/Crammd5.php
     *
     * @param string $key
     * @param string $data
     * @param int $block
     * @return string
     */
    protected function _hmacMd5($key, $data, $block = 64)
    {
        if (strlen($key) > 64) {
            $key = pack('H32', md5($key));
        } elseif (strlen($key) < 64) {
            $key = str_pad($key, $block, "\0");
        }
        $k_ipad = substr($key, 0, 64) ^ str_repeat(chr(0x36), 64);
        $k_opad = substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64);
        $inner = pack('H32', md5($k_ipad . $data));
        $digest = md5($k_opad . $inner);

        return $digest;
    }
}
