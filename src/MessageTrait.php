<?php
namespace SamIT\React\Smtp;

trait MessageTrait
{
    use \GuzzleHttp\Psr7\MessageTrait;

    public function withHeader($name, $value)
    {
        throw new \Exception('SMTP Message does not support replacing headers.');
    }

    public function withAddedHeader($name, $value)
    {
        $new = clone $this;
        $new->headers[] = [$name, $value];
        return $new;

    }

    public function getHeaderLine($name) {
        throw new \Exception('SMTP Message does not support merging headers with the same name.');
    }

    public function getHeader($header)
    {
        $result = [];
        $header = strtolower($header);
        foreach($this->headers as list($name, $value)) {
            if ($header == strtolower($name)) {
                $result[] = [$name, $value];
            }
        }
        return $result;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function hasHeader($header)
    {
        return !empty($this->getHeader($header));
    }

    public function withoutHeader($header)
    {
        throw new \Exception('SMTP Message does not support removing headers.');
    }


}
