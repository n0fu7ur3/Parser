<?php

namespace Parser {

    use Exception;
    use Loger\Loger;
    use phpQuery;

    /**
     * Парсер товаров
     *
     * Class Parser
     * @package Parser
     */
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
         * @throws Exception
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
