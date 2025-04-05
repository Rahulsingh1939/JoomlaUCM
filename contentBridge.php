<?php
// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

// Include dependencies if needed.
$controller = BaseController::getInstance('ContentExchange');
$input = JFactory::getApplication()->input;
$controller->execute($input->get('task', 'display'));
$controller->redirect();
