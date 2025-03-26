<?php

declare(strict_types=1);

namespace MasterZydra\UCache\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class UCache extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('masterzydra_ucache_ucache', 'ucache_id');
    }

    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setData('modified_at', date('Y-m-d H:i:s'));
        parent::save($object);
        return $this;
    }
}