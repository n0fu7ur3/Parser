<?php

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
            throw new Exception('TODO');
        }

        /**
         * @param string $url URL запроса
         * @return string HTML страница
         */
        private function request(string $url): string
        {
            $proxy = $this->proxer->newProxy();
        }

        /***********************public*********************/
        public function start()
        {
            try {
                $this->categories("selector");
            } catch (Exception $exception) {
            }
        }
    }
}
