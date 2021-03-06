<?php

/**
 * Категория товара
 */
class Category
{
    private $name;
    private $imgURL;
    private $href;

    /**
     * Category constructor.
     *
     * @param string $name имя категории
     * @param string $imgURL URL картинки категории
     * @param string $href ссылка категории
     * @throws Exception
     */
    public function __construct(
        string $name,
        string $imgURL,
        string $href
    )
    {
        $this->name = trim($name);
        $this->imgURL = trim($imgURL);
        $this->href = trim($href);

        $this->prepare(
            [
                $this->name,
                $this->imgURL,
                $this->href
            ]
        );
    }

    /**
     * Подготовка данных
     *
     * @param array $values данные
     * @throws Exception
     */
    private function prepare(array $values): void
    {
        foreach ($values as $value) {
            if (empty($value)) {
                $properties = implode("\n", $values);
                throw new Exception("Какое-то свойство в категории пусто\nСвойства:\n$properties");
            }
            $value = preg_replace('/\s+/', ' ', $value);
        }
    }

    public function name(): string
    {
        return $this->name;
    }

    public function imgURL(): string
    {
        return $this->imgURL;
    }

    public function href(): string
    {
        return $this->href;
    }
}
