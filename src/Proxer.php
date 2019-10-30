<?php

namespace Parser {

    class Proxer
    {
        private $blackList;

        /**
         * Proxer constructor.
         */
        public function __construct()
        {
            $this->blackList = new BlackList();
        }

        /**
         * Получение новой прокси
         *
         * @return array
         */
        public function newProxy(): array
        {
            $proxy = ['addr' => '', 'type' => ''];
            return $proxy;
        }

        private function proxySite(): string
        {
            return "https://free-proxy-list.net/";
        }

        private function banProxy(string $proxy): void
        {
            $this->blackList->add(new ListNode($proxy));
        }

        private function request()
        {
            $ch = curl_init($this->proxySite());
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt(
                $ch,
                CURLOPT_USERAGENT,
                'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/76.0.3809.100 Chrome/76.0.3809.100 Safari/537.36'
            );
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        }
    }
}
