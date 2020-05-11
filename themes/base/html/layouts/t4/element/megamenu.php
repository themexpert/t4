<?php
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry as JRegistry;

$app = \JFactory::getApplication('site');
// $menutype = 'mainmenu'; // read from template params
$paramsTpl = JFactory::getApplication()->getTemplate(true)->params;
$navigation_settings = $paramsTpl->get('navigation-settings');
$navigation = T4\Helper\Path::getFileContent('etc/navigation/default.json');
// get from element
$menutype = !empty($displayData->params['menutype']) ? $displayData->params['menutype'] : $navigation_settings->get('menu_type', 'mainmenu');

$modules = ModuleHelper::getModule('mod_menu');
$params = new JRegistry();
$params->loadString($modules->params);
$params->set('layout', 'mega');
$params->set('menutype', $menutype);
$params->set('startLevel', 1);
$params->set('endLevel', 0);
$params->set('showAllChildren', 1);
// create a module object to render
$module = new \stdClass;
$module->params = $params;
$module->module = 'mod_menu';
$module->id = 0;
$module->name = 'menu';
$module->title = $menutype;
$module->position = 'none';

// add megamenu js
\JFactory::getDocument()->addScript(T4PATH_BASE_URI . '/js/megamenu.js');

echo ModuleHelper::renderModule($module, []);
