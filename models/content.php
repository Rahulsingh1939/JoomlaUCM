<?php
defined('_JEXEC') or die;

class ContentbridgeModelContent extends JModelAdmin
{
    public function getTable($type = 'Content', $prefix = 'ContentbridgeTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        // Form implementation can be added later if needed
        return null;
    }

    public function save($data)
    {
        $table = $this->getTable();
        if (!$table->bind($data)) {
            $this->setError($table->getError());
            return false;
        }
        if (!$table->store()) {
            $this->setError($table->getError());
            return false;
        }
        return true;
    }

    public function getItems()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__contentbridge_items'));
        $db->setQuery($query);
        return $db->loadAssocList();
    }
}