<?php

/**
 * Парсер товаров
 */
class Parser
{
    private $loger;
    private $proxer;
    /**
     * @var array $config
     *
     * ['siteUrl']
     */
    private $config;
    private $pathToConfig;

    private $categories;

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
        $this->readConfig();
        $this->loger->log("Object created");
    }

    /***********************private*********************/
    /**
     * Запись конфига
     */
    public function writeConfig(): void
    {
        $this->loger->log("Write config");
        $fd = fopen($this->pathToConfig, 'w');
        $data = [
            'siteUrl' => $this->config['siteUrl']
        ];
        fwrite($fd, json_encode($data));
        fclose($fd);
    }

    /**
     * Чтение конфига
     */
    private function readConfig(): void
    {
        $this->loger->log("Read config");
        $jsonConfig = file_get_contents($this->pathToConfig);
        $this->config = json_decode($jsonConfig, true);
    }

    /**
     * Получение списка категорий
     *
     * @param string $selector селектор категорий
     * @param string $childrenSelector селектор потомков контейнера категорий
     * @return array
     * @throws Exception
     * @example $this->categories("#main > div.menu", "div.children");
     */
    public function categories(string $selector, string $childrenSelector): array
    {
        $url = $this->config['siteUrl'];
        $this->loger->log("Looking for categories on $url");
        $html = $this->proxer->request($url);
        $doc = phpQuery::newDocument($html);
        $categories = pq($selector);//->children($childrenSelector);
        echo "cats:" . $categories;
        foreach ($categories as $category) {
            echo "cat:" . pq($category) . "\n";
        }
        $this->loger->log("Categories: $categories");
        return $categories;
        foreach ($categories as $category) {
            $categoryName = '';
            $categoryImg = '';
            $categoryHref = '';
            $cat = new Category($categoryName, $categoryImg, $categoryHref);
            $this->categories[] = $cat;
        }
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
     * Возвращает список ссылок на страницы, если пагинатора нет - в списке одна страница
     *
     * @param string $paginatorSelector селектор пагинатора
     * @param string $childrenSelector селектор потомков пагинатора
     * @param string $textSelector селектор текса потомка пагинатора
     * @param string $url URL страницы, на который ищем, необходим для формирования ссылок
     * @param string $glue чем склеиваем URL и номер страницы
     * @return array
     */
    private function pages(
        string $paginatorSelector,
        string $childrenSelector,
        string $textSelector,
        string $url,
        string $glue = '/page/'
    ): array
    {
        $pages = [];
        $paginator = pq($paginatorSelector)->children($childrenSelector);
        if (!$paginator->length) {
            return [$url];
        }
        foreach ($paginator as $child) {
            $child = pq($child);
            $pageNumber = $child->find(($textSelector));
            if (is_numeric($pageNumber)) {
                $pages[] = $url . $glue . $pageNumber;
            }
        }
        return $pages;
    }

    /**
     * Парсим страницу
     */
    private function parsePage()
    {
        echo "asd\n";
    }

    /***********************public*********************/
    public function start()
    {
        try {
            $this->categories("selector", "children selector");

            foreach ($this->categories as $category) {
                $this->parseCategory("selector");
            }
        } catch (Exception $exception) {
        }
    }
}
