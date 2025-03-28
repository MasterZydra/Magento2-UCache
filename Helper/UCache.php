<?php

declare(strict_types=1);

namespace MasterZydra\UCache\Helper;

use MasterZydra\UCache\Model\UCache as UCacheModel;

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

    /** Load the given cache key */
    public function load(string $cacheKey, mixed $default = null): mixed
    {
        $ucache = $this->loadCache($cacheKey);
        if ($ucache->getId() === null) {
            return $default;
        }

        return $ucache->getValue();
    }

    /** Add or update the given cache key with the given value */
    public function save(string $cacheKey, mixed $value): bool
    {
        try {
            $ucache = $this->loadCache($cacheKey);
            $ucache->setKey($cacheKey);
            $ucache->setValue($value);
    
            $this->ucacheRes->save($ucache);

            return true;
        } catch (\Throwable $th) {
            $this->_logger->error('UCache: Failed to save value', [$th->getMessage(), $th->getTraceAsString()]);
            return false;
        }
    }

    /** Remove the given cache key */
    public function remove(string $cacheKey): bool
    {
        try {
            $ucache = $this->loadCache($cacheKey);
            if ($ucache->getId() === null) {
                return true;
            }

            $this->ucacheRes->delete($ucache);

            return true;
        } catch (\Throwable $th) {
            $this->_logger->error('UCache: Failed to clean cache', [$th->getMessage(), $th->getTraceAsString()]);
            return false;
        }
    }

    /** Load given cache key. If it is not cached yet or its expired, call given function and cache result. */
    public function remember(string $cacheKey, int $seconds, callable $value, ?array $args = null): mixed
    {
        $ucache = $this->loadCache($cacheKey);
        if ($ucache->getId() === null || $ucache->getModifiedAt()->getTimestamp() + $seconds < time()) {
            $ucache->setKey($cacheKey);
            if ($args === null) {
                $ucache->setValue($value());
            } else {
                $ucache->setValue($value(...$args));
            }
            $this->ucacheRes->save($ucache);
        }

        return $ucache->getValue();
    }

    /** Load given cache key. If it is not cached yet, call given function and cache result. */
    public function rememberForever(string $cacheKey, callable $value, ?array $args = null): mixed
    {
        $ucache = $this->loadCache($cacheKey);
        if ($ucache->getId() === null) {
            $ucache->setKey($cacheKey);
            if ($args === null) {
                $ucache->setValue($value());
            } else {
                $ucache->setValue($value(...$args));
            }
            $this->ucacheRes->save($ucache);
        }

        return $ucache->getValue();
    }

    /** Remove all cache entries */
    public function clean(): bool
    {
        try {
            /** @var \MasterZydra\UCache\Model\ResourceModel\UCache\Collection $coll */
            $coll = $this->ucacheCollFactory->create();

            /** @var \MasterZydra\UCache\Model\UCache $ucache */
            foreach ($coll as $ucache) {
                $this->ucacheRes->delete($ucache);
            }

            return true;
        } catch (\Throwable $th) {
            $this->_logger->error('UCache: Failed to clean cache', [$th->getMessage(), $th->getTraceAsString()]);
            return false;
        }
    }

    public function loadCache(string $cacheKey): UCacheModel
    {
        /** @var \MasterZydra\UCache\Model\UCache $ucache */
        $ucache = $this->ucacheFactory->create();
        $this->ucacheRes->load($ucache, $cacheKey, 'key');
        return $ucache;
    }
}
