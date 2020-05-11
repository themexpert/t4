<?php
/**
T4 Overide
 */

defined('_JEXEC') or die;

$attributes = array();

if ($item->anchor_title)
{
	$attributes['title'] = $item->anchor_title;
}

// T4: add class nav-link
if ($item->anchor_css)
{
	if(($item->level > 1)) {
		$attributes['class'] = $item->anchor_css . ' dropdown-item'; 
	} else {
		$attributes['class'] = $item->anchor_css . 'nav-link'; 
	}
}else{
	if(($item->level > 1)) {
		$attributes['class'] = ' dropdown-item'; 
	} else {
		$attributes['class'] = 'nav-link'; 
	}
}

// if ($item->anchor_css)
// {
// 	$attributes['class'] = $item->anchor_css;
// }


if ($item->anchor_rel)
{
	$attributes['rel'] = $item->anchor_rel;
}

$dropdown = '';
$caret = '';
///* && $item->level < 2*/
if($item->deeper){
	$attributes['class'] .= ' dropdown-toggle';
	$attributes['role'] = 'button';
	$attributes['aria-haspopup'] = 'true';
	$attributes['aria-expanded'] = 'false';
	$attributes['data-toggle'] = 'dropdown';
	// $attributes['id'] = 'navbarDropdown';
}

$linktype = $item->title;

if ($item->menu_image)
{
	if ($item->menu_image_css)
	{
		$image_attributes['class'] = $item->menu_image_css;
		$linktype = JHtml::_('image', $item->menu_image, $item->title, $image_attributes);
	}
	else
	{
		$linktype = JHtml::_('image', $item->menu_image, $item->title);
	}

	if ($item->params->get('menu_text', 1))
	{
		$linktype .= '<span class="image-title">' . $item->title . '</span>';
	}
}

if ($item->browserNav == 1)
{
	$attributes['target'] = '_blank';
}
elseif ($item->browserNav == 2)
{
	$options = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes';

	$attributes['onclick'] = "window.open(this.href, 'targetWindow', '" . $options . "'); return false;";
}

$linktype = $item->icon . $linktype . $item->caret . $item->caption;

echo JHtml::_('link', JFilterOutput::ampReplace(htmlspecialchars($item->flink, ENT_COMPAT, 'UTF-8', false)), $linktype, $attributes);
