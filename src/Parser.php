<?php

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

    /**
     * Parser constructor.
     * @param string $pathToConfig путь к файлу конфигурации
     * @throws Exception
     */
    public function __construct(
        string $pathToConfig = 'prsr.cfg'
    )
    {
        $this->proxer = new Proxer();
        $this->loger = new Loger('prsr.log');
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
        $html = $this->proxer->request($this->config['url']);
        $doc = phpQuery::newDocument($html);
        $categories = [];
        $this->categories = $categories;
        phpQuery::unloadDocuments($doc);
    }

    /**
     * Парсим категорию
     * Получаем пагинатор $this->pages()
     *      если пагинатора нет - в массиве будет 1 ссылка, на текущую страницу
     *      если пагинатор есть - в массиве будут ссылки,
     * @param string $selector
     * @param string $paginatorSelector
     */
    private function parseCategory(string $selector, string $paginatorSelector): void
    {
        $pages = $this->pages();
        foreach ($pages as $page) {
            $this->parseParse($page);
        }
    }

    /**
     * Возвращает список страниц
     */
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
