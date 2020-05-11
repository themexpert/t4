<?php

	$doc = $displayData->doc;
	$paramsTpl = $doc->params;
	//if(empty($paramsTpl->get('t4-navigation'))) $paramsTpl->set('t4-navigation','default');
	//$params = json_decode(\T4\Helper\Path::getFileContent('etc/navigation/'.$paramsTpl->get('t4-navigation').'.json'));
	//if(empty($params)) return;
	//if (!$params->navigation_oc_enabled) return;
	$navigation_settings = $paramsTpl->get('navigation-settings');
	if (!$navigation_settings->get('oc_enabled')) return;

	$oc_effect = $navigation_settings->get('oc_effect', "");
	switch ($oc_effect) {
        case 'left-reveal':
        case 'left-push':
        case 'left':
            break;
        default:
           $oc_effect = "left";
            break;
    }
	$oc_pos_name = $navigation_settings->get('oc_pos_name', 'offcanvas');
	$oc_pos_style = $navigation_settings->get('oc_pos_style', 'xhtml');
	$oc_rightside = filter_var($navigation_settings->get('oc_rightside'), FILTER_VALIDATE_BOOLEAN);

	// add css & js
	$doc->addStylesheet(T4\Helper\Path::findInTheme('vendors/js-offcanvas/_css/js-offcanvas.css', true));
	$doc->addScript(T4\Helper\Path::findInTheme('vendors/js-offcanvas/_js/js-offcanvas.pkgd.js', true));
	$doc->addScript(T4\Helper\Path::findInTheme('vendors/bodyscrolllock/bodyScrollLock.min.js', true));
	$doc->addScript(T4\Helper\Path::findInTheme('js/offcanvas.js', true));
	$oc_rightside = $navigation_settings->get('oc_rightside', '');
	if($oc_rightside){
        $oc_effect = str_replace("left", 'right', $oc_effect);
    }
	switch ($oc_effect) {
		case 'left':
		case 'right':
			$options = '{"modifiers":"'.$oc_effect.',overlay"}';
			break;
		
		default:
			$options = '{"modifiers":"'.str_replace("-",",",$oc_effect).'"}';
			break;
	}
?>
<div class="t4-offcanvas" data-offcanvas-options='<?php echo $options; ?>' id="off-canvas-<?php echo $oc_effect;?>" role="complementary" style="display:none;">
	<div class="t4-off-canvas-header">
		<h3><?php echo JText::_('T4_OFF_CANVAS_TITLE') ?></h3>
		<button type="button" class="close js-offcanvas-close" data-dismiss="modal" aria-hidden="true">×</button>
	</div>

	<div class="t4-off-canvas-body">
		<jdoc:include type="modules" name="<?php echo $oc_pos_name ?>" style="<?php echo $oc_pos_style ?>" />
	</div>
</div>
