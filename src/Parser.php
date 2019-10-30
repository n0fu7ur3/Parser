<?php
//TODO: проксер отвечает за запросы, получает url, отдаёт html, в parser'e метод request мб не нужен
namespace Parser {

    use Exception;
    use Loger\Loger;
    use phpQuery;

    class Parser
    {
        private $loger;
        private $proxer;
        private $config;
        private $categories;
        private $pathToConfig;
        private $curlErrorBlackList;
        private $curlBadErrorBlackList;

        /**
         * Parser constructor.
         * @param string $pathToConfig путь к файлу конфигурации
         */
        public function __construct(
            string $pathToConfig = 'pxr.cfg'
        )
        {
            $this->proxer = new Proxer();
            $this->loger = new Loger('pxr.log');
            $this->curlErrorBlackList = new BlackList(0);
            $this->curlBadErrorBlackList = new BlackList(0);
            $this->pathToConfig = $pathToConfig;
            $this->config = $this->readConfig($pathToConfig);
        }

        /***********************private*********************/
        /**
         * Запись конфига
         */
        private function writeConfig(): void
        {
            $fd = fopen($this->pathToConfig, 'w');
            fclose($fd);
        }

        /**
         * Чтение конфига
         */
        private function readConfig(): void
        {
            $jsonConfig = file_get_contents($this->pathToConfig);
            $this->config = json_decode($jsonConfig, true);
        }

        /**
         * Получение списка категорий
         *
         * @param string $selector селектор категорий
         * @throws Exception
         * @example $this->categories("#main > div.menu");
         */
        private function categories(string $selector): void
        {
            $html = $this->request($this->config['url']);
            $doc = phpQuery::newDocument($html);
            $categories = [];
            $this->categories = $categories;
            phpQuery::unloadDocuments($doc);
        }

        /**
         * Запрос данных
         *
         * @param string $url URL запроса
         * @return string HTML страница
         */
        private function request(string $url): string
        {
            do {
                $proxy = $this->proxer->newProxy();
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
                $errno = curl_errno($ch);
                $errorMsg = curl_strerror($errno);
                $this->checkCurlError($errorMsg);
            } while ($html == false);
            curl_close($ch);
            return $html;
        }

        private function checkCurlError(string $errorMsg)
        {
            if ($this->curlErrorBlackList->has($errorMsg)) {

            }
        }

        /**
         * Парсим категорию
         * Получаем пагинатор $this->pages()
         *      если пагинатора нет - в массиве будет 1 ссылка, на текущую страницу
         *      если пагинатор есть - в массиве будут ссылки,
         * @param string $selector
         * @param string $paginatorSelector
         */
        private function parseCategory(string $selector, string $paginatorSelector)
        {
            $pages = $this->pages();
            foreach ($pages as $page) {
                $this->parseParse($page);
            }
        }

        private function pages()
        {

        }

        /***********************public*********************/
        public function start()
        {
            try {
                $this->categories("selector");
                foreach ($this->categories as $category) {
                    $this->parseCategory("selector");
                }
            } catch (Exception $exception) {
            }
        }
    }
}
