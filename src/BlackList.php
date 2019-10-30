<?php

/**
 * Черный список
 *
 * Class BlackList
 * @package Parser
 */
class BlackList
{
    private $list;
    private $lockTime;

    /**
     * BlackList constructor.
     * @param int $lockTime время в минутах, через которое данные будут удалены из черного списка, 0 - не удалять
     */
    public function __construct(int $lockTime = 5)
    {
        $this->list = [];
        $this->lockTime = $lockTime;
    }

    /**
     * @param ListNode $node
     */
    public function add(ListNode $node): void
    {
        $this->checkList();
        if (!$this->has($node->data())) {
            $this->list[$node->data()] = $node;
        }
    }

    /**
     * Существование ключа
     *
     * @param string $data данные(ключ)
     * @return bool
     */
    public function has(string $data)
    {
        $this->checkList();
        return array_key_exists($data, $this->list);
    }

    /**
     * @param ListNode $node
     */
    private function remove(ListNode $node): void
    {
        unset($this->list[$node->data()]);
    }

    /**
     * Проверяет список, если в списке есть элемент, который был создан lockTime минут назад, - удаляет этот элемент
     */
    private function checkList(): void
    {
        if ($this->lockTime !== 0) {
            $timestamp = new DateTime();
            foreach ($this->list as $key => $node) {
                if (($timestamp->diff($node->timestamp())->i) >= $this->lockTime) {
                    $this->remove($node);
                }
            }
        }
    }
}

/**
 * Элемент черного списка
 *
 * Class ListNode
 * @package Parser
 */
class ListNode
{
    private $data;
    private $timestamp;

    /**
     * ListNode constructor.
     * @param string $data данные
     * @throws Exception
     */
    public function __construct(string $data)
    {
        $this->data = $data;
        $this->timestamp = new DateTime();
    }

    /**
     * Возвращает данные узла
     *
     * @return string
     */
    public function data(): string
    {
        return $this->data;
    }

    /**
     * Возвращает временную метку узла
     *
     * @return DateTime
     */
    public function timestamp(): DateTime
    {
        return $this->timestamp;
    }
}
