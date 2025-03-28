<?php

declare(strict_types=1);

namespace MasterZydra\UCache\Model\Cache\Type;

use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;
use MasterZydra\UCache\Helper\UCache as UCacheHelper;

class Frontend extends TagScope implements \Zend_Cache_Backend_Interface
{
    const TYPE_IDENTIFIER = 'masterzydra_ucache';

    const CACHE_TAG = 'UCACHE';

    public function __construct(
        FrontendPool $cacheFrontendPool,
        private UCacheHelper $helper,
    ) {
        parent::__construct(
            $cacheFrontendPool->get(self::TYPE_IDENTIFIER),
            self::CACHE_TAG
        );
    }

    public function clean($mode = \Zend_Cache::CLEANING_MODE_ALL, $tags = [])
    {
        return $this->helper->clean();
    }

    public function load($id, $doNotTestCacheValidity = false): mixed
    {
        return $this->helper->load($id);
    }

    public function save($data, $id, $tags = [], $specificLifetime = false): bool
    {
        return $this->helper->save($id, $data);
    }

    public function test($id): false|int
    {
        $cache = $this->helper->loadCache($id);
        return $cache->getId() === null ? false : $cache->getModifiedAt()->getTimestamp();
    }

    public function remove($id): bool
    {
        return $this->helper->remove($id);
    }

    public function setDirectives($directives)
    {
    }

    public function getBackend()
    {
        return $this;
    }
}
