<?php

declare(strict_types=1);

namespace MasterZydra\UCache\Model;

use DateTime;

class UCache extends \Magento\Framework\Model\AbstractModel
{
    private const KEY = 'key';
    private const VALUE = 'value';
    private const MODIFIED_AT = 'modified_at';

    public function _construct(
    ) {
        $this->_init(\MasterZydra\UCache\Model\ResourceModel\UCache::class);
    }

    public function getKey(): string
    {
        return (string)$this->getData(self::KEY);
    }
    public function setKey(string $key): self
    {
        return $this->setData(self::KEY, $key);
    }
    
    public function getValue(): mixed
    {
        return unserialize((string)$this->getData(self::VALUE));
    }
    public function setValue($value): self
    {
        return $this->setData(self::VALUE, serialize($value));
    }

    public function getModifiedAt(): DateTime
    {
        return new DateTime($this->getData(self::MODIFIED_AT));
    }
}