<?php

/**
 * Проксирование запросов
 *
 * Class Proxer
 * @package Parser
 */
class Proxer
{
    private $proxyBlackList;
    private $proxyBanReason;
    private $curlErrorBlackList;
    private $loger;

    /**
     * Proxer constructor.
     */
    public function __construct()
    {
        $this->proxyBlackList = new BlackList();
        $this->proxyBanReason = new BlackList(0);
        $this->proxyBanReason->add(new ListNode("Timeout was reached"));
        $this->proxyBanReason->add(new ListNode("Failure when receiving data from the peer"));
        $this->proxyBanReason->add(new ListNode("Couldn\'t connect to server"));
        $this->proxyBanReason->add(new ListNode("Number of redirects hit maximum amount"));
        $this->proxyBanReason->add(new ListNode("SSL connect error"));
        $this->curlErrorBlackList = new BlackList(0);
        $this->curlErrorBlackList->add(new ListNode("URL using bad/illegal format or missing URL"));
        $this->loger = new Loger('prxr.log');
    }

    /**
     * Получение новой прокси
     *
     * @return array
     * @throws Exception
     */
    private function proxy(): array
    {
        $this->loger->log("Получаем новую прокси");
        $html = file_get_contents($this->proxySite());
        $doc = phpQuery::newDocument($html);
        $tableRows = $doc->find("#proxylisttable > tbody")->children('tr');
        foreach ($tableRows as $row) {
            $row = pq($row);
            $tds = $row->children('td');
            $data = explode("\n", $tds->text());
            $ip = $data[0];
            $port = $data[1];
            if ($this->checkProxy($ip . $port)) {
                continue;
            }
            $proxyType = CURLPROXY_HTTP;
            $https = $data[6];
            if ($https == 'yes') {
                // 2 == CURLPROXY_HTTPS
                $proxyType = 2;
            }
            $this->loger->log("Прокси: $ip:$port");
            phpQuery::unloadDocuments($doc);
            return [
                "address" => $ip . ":" . $port,
                "type" => $proxyType
            ];
        }
    }

    /**
     * Проверяет наличие прокси в черном списке
     *
     * @param string $proxy прокси
     * @return bool
     * @throws Exception
     */
    private function checkProxy(string $proxy): bool
    {
        $this->loger->log("проверяем прокси: [$proxy]");
        return $this->proxyBlackList->has($proxy);
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
        $this->loger->log("баним прокси [$proxy]");
        $this->proxyBlackList->add(new ListNode($proxy));
    }

    /**
     * Запрос страницы используя прокси
     *
     * @param string $url URL страницы
     * @return string html-код страницы
     * @throws Exception
     */
    public function request(string $url)
    {
        $this->loger->log("шлём запрос на $url");
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
            curl_setopt($ch, CURLOPT_PROXY, $proxy['address']);
            curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy['type']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);

            $html = curl_exec($ch);
            if ($html == false) {
                $errno = curl_errno($ch);
                $errorMsg = curl_strerror($errno);
                $this->loger->log("Ошибка CURL: $errorMsg");
                $this->checkCurlError($errorMsg, $proxy['address']);
                curl_close($ch);
            }
        } while ($html == false);
        curl_close($ch);
        return $html;
    }

    /**
     * Проверка ошибки CURL
     *
     * @param string $errorMsg текст ошибки
     * @param string $proxy прокси
     * @throws Exception
     */
    private function checkCurlError(string $errorMsg, string $proxy)
    {
        $this->loger->log("проверяем ошибку CURL: $errorMsg");
        if ($this->proxyBanReason->has($errorMsg)) {
            $this->banProxy($proxy);
        }
        if ($this->curlErrorBlackList->has($errorMsg)) {
            throw new Exception($errorMsg);
        }
    }
}
