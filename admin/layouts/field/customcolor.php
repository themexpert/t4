<?php 
$id = $displayData['id'];
$inputName = $displayData['name'];
$inputValue = $displayData['value'];
$data = $displayData['colors'];

JFactory::getDocument()->addScript(T4PATH_ADMIN_URI . '/assets/js/custom-color.js');
$script = "\nvar T4Admin = window.T4Admin || {}; ";
$script .= "\nT4Admin.customcolors = ". json_encode($data) . "; ";
JFactory::getDocument()->addScriptDeclaration($script);
$edit_class = " can-edit";
$decs = JText::_('T4_FIELD_CUSTOM_COLOR_MASTER_DESC');

?>
<div class="t4-custom-color-wrap">
	<!-- <p class="description"><?php echo $decs; ?></p> -->
	
	<div class="custom-color-items">
		<div class="custom-color-list">
			<?php if(!empty($data)):?>
			<?php foreach($data as $colorName => $colorData):?>
					<div class="control-group" data-name="<?php echo $colorData['name']; ?>" data-class="<?php echo $colorName; ?>"  data-color="<?php echo $colorData['color']; ?>">
						<div class="control-label<?php echo $edit_class;?>">
							<label id="<?php echo $colorName;?>"><?php echo $colorData['name']; ?></label>
							<div class="edit-label">
								<input type="text" name="edit_<?php echo $colorName;?>" value="<?php echo $colorData['name']; ?>"/>
								<div class="edit-actions">
									<span class="color-save"><i class="fal fa-check"></i></span><span class="color-cancel"><i class="fal fa-times"></i></span>
								</div>
							</div>
							<div class="colors-actions">
								<ul class="colors-actions-list">
									<li><a class="color-move" href="#" data-tooltip="Move"><i class="fal fa-arrows fa-fw"></i></a></li>
									<?php if($colorData['status'] == 'loc') :?>
									<li><a class="color-del" href="#" data-tooltip="Delete"><i class="fal fa-trash-alt fa-fw"></i></a></li>
									<?php else:?>
										<?php if($colorData['status'] != 'org'):?>
										<li><a class="color-del" href="#" data-tooltip="Restore" style="display: none;"><i class="fal fa-undo fa-fw"></i></a></li>
										<?php endif?>
									<?php endif?>
								</ul>
							</div>
							<!-- <span class="t4-param-reset" style="display: none;"><i class="fal fa-undo"></i></span> -->
						</div>
						<div class="controls">
							<input type="text" class="custom-color-item minicolors rgba" name="<?php echo $colorName;?>" value="<?php echo $colorData['color']; ?>" /> 
						</div>
					</div>
			<?php endforeach ?>
			<?php endif ?>
		</div>
		
	</div>
	
	<div class="add-more-custom-colors">
		<span class="t4-btn btn-action btn-primary" data-action="custom.addcolor"><i class="fal fa-plus"></i><?php echo JText::_('T4_FIELD_CUSTOM_ADD_NEW_COLOR') ?></span>		
	</div>

	<div class="custom-colors-form hide">
		<div class="control-group">
			<div class="control-label"><label><?php echo JText::_('T4_FIELD_COLOR_ADD_NAME_LABEL') ?></label></div>
			<div class="controls"><input id="color-name" class="custom-input" name="color-name" type="text" /></div>
		</div>

		<div class="control-group">
			<div class="control-label"><label><?php echo JText::_('T4_FIELD_COLOR_ADD_LABEL') ?></label></div>
			<div class="controls"><input id="custom-color" class="custom-input minicolors rgba" name="custom-color" type="text" value="" default="#FFFFFF" /></div>
		</div>

		<div class="custom-colors-actions">
			<span class="t4-btn btn-action btn-primary" data-action="custom.savecolor"><?php echo JText::_('T4_ADD_LABEL') ?></span>
			<span class="t4-btn btn-action" data-action="custom.cancel"><?php echo JText::_('JCANCEL') ?></span>
		</div>
	</div>
</div>
<input type="hidden" id="<?php echo $id; ?>" class="t4-custom-colors" name="<?php echo $inputName; ?>" value="<?php echo htmlspecialchars($inputValue); ?>">	
