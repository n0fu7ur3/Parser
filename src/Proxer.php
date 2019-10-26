<?php

namespace Parser {

    class Proxer
    {
        private $blackList;
        private $site;

        public function __construct(string $site)
        {
            $this->blackList = new BlackList();
            $this->site = $site;
        }


        public function proxy(): string
        {
            $proxy = '';
            return $proxy;
        }

        private function banProxy(string $proxy): void
        {
            $this->blackList->add(new ListNode($proxy));
        }

        private function request()
        {
            $ch = curl_init($this->site);
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
