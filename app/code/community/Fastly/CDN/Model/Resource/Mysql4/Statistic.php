<?php

class Fastly_CDN_Model_Resource_Mysql4_Statistic extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('fastlycdn/statistics', 'stat_id');
    }

    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setCreatedAt(Varien_Date::now());
        return parent::_beforeSave($object); // TODO: Change the autogenerated stub
    }
}