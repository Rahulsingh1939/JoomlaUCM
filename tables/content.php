<?php
defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;

class ContentbridgeTableContent extends Table {
    public function __construct(&$db) {
        parent::__construct('#__contentbridge_content', 'id', $db);
    }

    public function bind($array, $ignore = '') {
        return parent::bind($array, $ignore);
    }

    public function store($updateNulls = false) {
        return parent::store($updateNulls);
    }
}