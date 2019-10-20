<?php

namespace Loger;

class Loger
{
    private $filepath;
    private $fd;

    public function __construct(
        string $filepath = 'default.log'
    )
    {
        $this->filepath = $filepath;
        $this->fd = fopen($filepath, 'w+');
    }

    public function __destruct()
    {
        if (fopen($this->fd, 'w+')) {
            fclose($this->fd);
        }
    }

    public function log(string $message)
    {
        $timeStamp = date("d.m.y H:i:s");
        fwrite($this->fd, "$timeStamp: $message");
    }
}