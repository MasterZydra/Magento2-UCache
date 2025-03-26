<?php

declare(strict_types=1);

namespace MasterZydra\UCache\Model\ResourceModel\UCache;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'ucache_id';

    protected function _construct()
    {
        $this->_init(
            \MasterZydra\UCache\Model\UCache::class,
            \MasterZydra\UCache\Model\ResourceModel\UCache::class
        );
    }
}
