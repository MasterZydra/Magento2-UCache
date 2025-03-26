<?php

declare(strict_types=1);

namespace MasterZydra\UCache\Helper;

class UCache extends \Magento\Framework\App\Helper\AbstractHelper
{
    private \MasterZydra\UCache\Model\ResourceModel\UCache $ucacheRes;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        private \MasterZydra\UCache\Model\UCacheFactory $ucacheFactory,
        \MasterZydra\UCache\Model\ResourceModel\UCacheFactory $ucacheResFactory,
        private \MasterZydra\UCache\Model\ResourceModel\UCache\CollectionFactory $ucacheCollFactory,
    ) {
        parent::__construct($context);
        $this->ucacheRes = $ucacheResFactory->create();
    }

    public function load(string $cacheKey, mixed $default = null): mixed
    {
        /** @var \MasterZydra\UCache\Model\UCache $ucache */
        $ucache = $this->ucacheFactory->create();
        $this->ucacheRes->load($ucache, $cacheKey, 'key');

        if ($ucache->getId() === null) {
            return $default;
        }

        return $ucache->getValue();
    }

    public function save(string $cacheKey, mixed $value): void
    {
        /** @var \MasterZydra\UCache\Model\UCache $ucache */
        $ucache = $this->ucacheFactory->create();
        $this->ucacheRes->load($ucache, $cacheKey, 'key');

        $ucache->setKey($cacheKey);
        $ucache->setValue($value);

        $this->ucacheRes->save($ucache);
    }

    public function remove(string $cacheKey): void
    {
        /** @var \MasterZydra\UCache\Model\UCache $ucache */
        $ucache = $this->ucacheFactory->create();
        $this->ucacheRes->load($ucache, $cacheKey, 'key');

        if ($ucache->getId() === null) {
            return;
        }

        $this->ucacheRes->delete($ucache);
    }

    public function clean(): void
    {
        /** @var \MasterZydra\UCache\Model\ResourceModel\UCache\Collection $coll */
        $coll = $this->ucacheCollFactory->create();

        /** @var \MasterZydra\UCache\Model\UCache $ucache */
        foreach ($coll as $ucache) {
            $this->ucacheRes->delete($ucache);
        }
    }
}
