<?php

/**
 * Единица товара
 */
class Product
{
    private $name;
    private $price;
    private $imgURL;

    /**
     * Product constructor.
     *
     * @param string $name
     * @param string $price
     * @param string $imgURL
     * @throws Exception
     */
    public function __construct(
        string $name,
        string $price,
        string $imgURL
    )
    {
        $this->name = $name;
        $this->price = $price;
        $this->imgURL = $imgURL;

        $this->prepare(
            [
                $this->name,
                $this->price,
                $this->imgURL
            ]);
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
                throw new Exception("Какое-то свойство в товаре пусто\nСвойства:\n$properties");
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

    public function price(): string
    {
        return $this->price;
    }
}