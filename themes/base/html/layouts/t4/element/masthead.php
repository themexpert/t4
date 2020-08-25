<?php

$app = JFactory::getApplication();
$active = $app->getMenu()->getActive();
if (!$active) return '';
if(version_compare(JVERSION, '4', 'ge')){
	$params = JFactory::getApplication()->getMenu()->getParams($active->id);
}else{
	$params = $active->params;
}
$title = null;
if (\T4\Helper\Layout::isSubpage()) {
    $title = $params ? $params->get('page_subheading') : null;
} else {
    $title = $params ? $params->get('page_heading') : null;
}
if (!$title) $title = $active->title;
if (!$title) {
    return '';
}
?>
<h2 class="page-title"><?php echo $title ?></h2>
