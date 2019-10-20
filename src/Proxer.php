<?php

namespace Parser {

    class Proxer
    {
        private $blackList;

        public function __construct()
        {
            $this->blackList = new BlackList();
        }

        public function newProxy(): string
        {
        }
    }
}
