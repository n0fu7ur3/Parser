<?php

namespace Parser {

    use Exception;
    use phpQuery;

    /**
     * Проксирование запросов
     *
     * Class Proxer
     * @package Parser
     */
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
        private function proxy(): array
        {
            $html = file_get_contents($this->proxySite());
            $doc = phpQuery::newDocument($html);
            $tableRows = $doc->find("#proxylisttable > tbody")->children('tr');
            phpQuery::unloadDocuments($doc);
            $proxy = ['addr' => '', 'type' => ''];
            return $proxy;
        }

        /**
         * @return string сайт, откуда парсим прокси
         */
        private function proxySite(): string
        {
            return "https://free-proxy-list.net/";
        }

        /**
         * Блокировка прокси
         *
         * @param string $proxy
         * @throws Exception
         */
        private function banProxy(string $proxy): void
        {
            $this->blackList->add(new ListNode($proxy));
        }

        /**
         * Запрос страницы используя прокси
         *
         * @param string $url URL страницы
         * @return string html-код страницы
         */
        private function request(string $url)
        {
            do {
                $proxy = $this->proxy();
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt(
                    $ch,
                    CURLOPT_USERAGENT,
                    'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/76.0.3809.100 Chrome/76.0.3809.100 Safari/537.36'
                );
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_PROXY, $proxy['addr']);
                curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy['type']);
                curl_setopt($ch, CURLOPT_TIMEOUT, 8);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);

                $html = curl_exec($ch);
                if ($html == false) {
                    $errno = curl_errno($ch);
                    $errorMsg = curl_strerror($errno);
                    $this->checkCurlError($errorMsg);
                    curl_close($ch);
                }
            } while ($html == false);
            curl_close($ch);
            return $html;
        }
    }
}
