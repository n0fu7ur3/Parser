<?php

namespace Loger;

use Exception;

/**
 * Логер
 *
 * Class Loger
 * @package Loger
 */
class Loger
{
    private $filepath;
    private $fd;

    /**
     * Loger constructor.
     * @param string $filepath имя лог-файла
     * @throws Exception
     */
    public function __construct(
        string $filepath = 'default.log'
    )
    {
        $this->filepath = $filepath;
        $this->fd = fopen($filepath, 'w+');
        if ($this->fd == false) {
            throw new Exception("File inst open: $this->filepath\n");
        }
    }

    public function __destruct()
    {
        fclose($this->fd);
    }

    /**
     * Запись сообщения
     *
     * @param string $message сообщение
     * @throws Exception
     */
    public function log(string $message): void
    {
        $timeStamp = date("d.m.y H:i:s");
        fwrite($this->fd, "$timeStamp: $message");
    }
}
