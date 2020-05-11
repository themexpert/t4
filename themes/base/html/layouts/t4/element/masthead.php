<?php

$app = JFactory::getApplication();
$active = $app->getMenu()->getActive();
if (!$active) return '';

$title = null;
if (\T4\Helper\Layout::isSubpage()) {
    $title = $active->params ? $active->params->get('page_subheading') : null;
} else {
    $title = $active->params ? $active->params->get('page_heading') : null;
}
if (!$title) $title = $active->title;
if (!$title) {
    return '';
}
?>
<h2 class="page-title"><?php echo $title ?></h2>
