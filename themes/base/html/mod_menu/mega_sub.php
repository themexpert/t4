<?php
	use Joomla\CMS\Helper\ModuleHelper;
	use T4\Helper\Col as T4Col;

	$style = '';
	if (!empty($item->mega['width'])) {
		$width = $item->mega['width'];
		if (is_numeric($width)) $width .= 'px';
		$style .= "width: $width;";
	}
	if ($style) $style = " style=\"$style\"";

	$classes_mega = '';
	if(isset($item->mega['mega_extra'])){
		$classes_mega = ' '.$item->mega['mega_extra'];
	}

	// Get all sub menu items
	$sub_items = [];

	while(1) {
		if ($idx >= count($list) -1) {
			break;
		}
		$nextItem = $list[$ids[$idx+1]];
		if ($nextItem->level == 1) break;
		$sub_items[$nextItem->id] = $nextItem;
		$idx++;
	}

?>

<div class="dropdown-menu mega-dropdown-menu<?php echo $classes_mega;?>"<?php echo $style; ?>>
	<div class="mega-dropdown-inner">
	<?php
		$doc = JFactory::getDocument();

		foreach ($item->mega['settings'] as $row): ?>
			<div class="row">
				<?php foreach ($row['contents'] as $col): ?>
					<?php if(!empty($col['type'])):?>
					<?php if(isset($col['style']) && !$col['style']) $col['style'] = 'JAxhtml';?>
					<div class="<?php echo T4Col::getCls('md', $col['col']); ?>">
						<div class="<?php echo $classes = ($col['type'] == 'items') ? 'mega-col-nav' : 'mega-col-module';?>">
							<div class="mega-inner">
								<?php if (!empty($col['title'])): ?>
								<h3 class="mega-col-title"><span><?php echo $col['title'] ?></span></h3>
								<?php endif ?>
								<?php
								switch ($col['type']) {
									case 'module':
										$modid = $col['module_id'];
										$module = ModuleHelper::getModuleById($modid);
										if (!$module->id) {
											// get direct module from db
											$db = \JFactory::getDbo();
											$query = $db->getQuery(true)
												->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params')
												->from('#__modules AS m')
												->where('m.published = 1')
												->where('m.id = ' . (int)$modid);
											$db->setQuery($query);
											$module = $db->loadObject();
										}
										if ($module) {
											echo ModuleHelper::renderModule($module, array('style'=>$col['style']));
										}
										break;
									case 'position':
										echo $doc->getBuffer('modules', $col['name'], array('style'=>$col['style']));
										break;
									case 'items':
									default:
										include 'mega_items.php';
										break;
								}
								?>
							</div>
						</div>
					</div>
				<?php endif;?>
				<?php endforeach ?>
			</div>
		<?php endforeach ?>

	</div>
</div>
