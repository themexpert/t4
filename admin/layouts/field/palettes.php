<?php
use T4Admin\RowColumnSettings;

$palettes = $displayData['palettes'];
$id = $displayData['input']['id'];
$name = $displayData['input']['name'];
$value = $displayData['input']['value'];
$fields = $displayData['fields'];
$params = $displayData['colors'];
$userColor_class = '';
if (!$palettes) return '';

?>
<div class='pattern-list t4-color-pattern'>
	<div class="pattern none">
			<div class="pattern-inner">
				<div class="pattern-header">
					<h4 class="pattern-title"><?php echo JText::_('T4_PATTERN_TITLE_NONE');?></h4>
				</div>
				<p><?php echo JText::_('T4_PATTERN_DEFAULT');?></p>
			</div>
		</div>
	<?php foreach ($palettes as $pname => $palette) : ?>
		<div class="pattern <?php echo $pname;?>" <?php echo $dataSeting = RowColumnSettings::getSettings((object)$palette); ?>>
			<div class="pattern-inner">
				<div class="pattern-header">
					<h4 class="pattern-title"><?php echo $palette['title'];?></h4>
					<div class="pattern-actions">
						<ul class="pattern-actions-list">
							<li><a class="pt-color-edit" href="#" data-tooltip="<?php echo JText::_('JACTION_EDIT');?>"><i class="fal fa-edit"></i></a></li>
							<li><a class="pt-color-create" href="#" data-tooltip="<?php echo JText::_('COLOR_PATTERN_CLONE');?>"><i class="fal fa-copy fa-fw"></i></a></li>
							<?php $orgHidden = ($palette['status'] == 'org') ? ' class="hidden"' : ''; ?>
							<?php if ($palette['status'] == 'loc') : ?>
							<li><a class="pt-color-del" href="#" data-tooltip="<?php echo JText::_('JACTION_DELETE');?>"><i class="fal fa-trash-alt fa-fw"></i></a></li>
							<?php else: ?>
							<li<?php echo $orgHidden;?>><a class="pt-color-del" href="#" data-tooltip="<?php echo JText::_('JACTION_RESTORE');?>"><i class="fal fa-redo"></i></a></li>
							<?php endif ?>
						</ul>
					</div>
				</div>
				<ul class="color-list">
					<?php foreach ($fields as $field): ?>
						<?php
						$dataVal = ''; 
							if (isset($palette[$field['name']])){
								$color_name = $palette[$field['name']];
							}else{
								$color_name = "";
							}
							preg_match("/#/", $color_name, $hex_color, PREG_OFFSET_CAPTURE);
							if(!empty($hex_color[0])){
								$dataVal = $color_name;
							}else{
								if( array_key_exists($color_name, $params['brand_color'])) $dataVal =$params['brand_color'][$color_name];
								if( array_key_exists($color_name, $params['user_color'])) $dataVal =$params['user_color'][$color_name];
								if(empty($dataVal)) $dataVal = '';
							}
						?>
							<li><span class="<?php echo $field['name']; ?>" data-title="<?php echo str_replace('_', ' ', $field['title']);?>" style="background: <?php echo $dataVal;?>;">&nbsp;</span></li>
					<?php endforeach ?>
				</ul>
			</div>
		</div>
	<?php endforeach;?>
</div>
<div class="t4-palettes-modal" style="display: none;">
	<div class="t4-modal-overlay"></div>
	<div class="t4-modal t4-pattern-row" data-target="#">
		<div class="t4-modal-header">
	    <span class="t4-modal-header-title"><i class="fal fa-cog"></i>Palettes Settings</span>
	    <a href="#" class="action-t4-modal-close"><span class="fal fa-times"></span></a>
	  </div>
		  <div class="t4-modal-inner t4-patterns-inner">
		  	<div class="t4-modal-content">
		  		<div class="row">
						<div class="config_pattern col-7">
							<div class="control-group title">
								<div class="control-label">
									<label><?php echo JText::_('T4_PATTERN_TITLE'); ?>
								</div>
								<div class="controls">
									<input 	type="text" name="title" class="t4-pattern title" data-attrname="title" value="" data-val="" placeholder="<?php echo JText::_('T4_PATTERN_TITLE_PLACEHOLDER'); ?>" />
								</div>
								</label>
							</div>
						<?php foreach ($fields as $field) : ?>
							<div class="control-group <?php echo $field['name']; ?>">
								<div class="control-label">
									<label><?php echo $field['title']; ?></label>
								</div>

								<div class="controls">

									<div class="select-color-palette">
										<div class="color-preview">
											<span class="preview-icon"></span>
											<input 	type="text" name="<?php echo $field['name']; ?>" class="t4-pattern <?php echo $field['class']; ?>" data-attrname="<?php echo $field['name']; ?>" value="<?php echo $field['value']; ?>" data-val="" readonly="" />
											<span class="toggle-icon"><i class="fal fa-angle-down"></i></span>
										</div>

										<div class="choose-color-pattern hidden">
											<ul>
												<li class="group-title"><span><?php echo \JText::_('JNONE');?></span></li>
												<li class="t4-select-pattern t4-pt-none" data-val="none" data-color="" data-name="none">
														<span class="preview-icon" data-bgcolor="transparent" style="background: transparent;"></span>
														<span class="color-label"><?php echo \JText::_('JNONE');?></span>
													</li>
												<?php foreach ($params as $name_color => $group_color) :?>
													<li class="group-title"><span><?php echo str_replace("_"," ",$name_color);?></span></li>
													<?php 
														if($name_color == "user_color"){
															$userColor_class = ' user-color';
														}else{
															$userColor_class = '';
														}
													?>
													<?php foreach ($group_color as $name => $field_color) : ?>
														<li class="t4-select-pattern<?php echo $userColor_class;?>" data-val="<?php echo $name;?>" data-color="<?php echo $field_color;?>" data-name="<?php echo str_replace("_", " ",$name);?>">
															<span class="preview-icon" data-bgcolor="<?php echo $field_color; ?>" style="background: <?php echo $field_color; ?>;"></span>
															<span class="color-label"><?php echo str_replace("_", " ",$name);?></span>
														</li>
													<?php endforeach;?>
												<?php endforeach;?>
											</ul>
										</div>
									</div>
								</div><!-- // Controls -->

							</div>
							
						<?php endforeach;?>
						</div>
						<div class="t4-pattern-preview col-5">
							<?php echo JText::_('COLOR_PATTERN_EXAMPLE');?>
						</div>
					</div>
		  	</div>
		  </div>
      <div class="t4-modal-footer">
        <a href="#" class="btn btn-secondary btn-xs t4-patterns-cancel"><span class="fal fa-times"></span> Cancel</a>
        <a href="#" class="btn btn-success btn-xs t4-patterns-apply" data-flag="mega-setting"><span class="fal fa-check"></span> Apply</a>
      </div>
	</div>
</div>
<input id="<?php echo $id?>" class="t4-layout t4-input-color_pattern" name="<?php echo $displayData['input']['name'] ?>" value="<?php echo htmlspecialchars($value);?>" data-attrname="color_pattern" type="hidden" hidden="hidden" />

