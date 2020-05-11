<?php
$input_id = $displayData['id'];
$input_name = $displayData['name'];
$input_value = $displayData['value'];
$params = $displayData['colors'];

if(is_object($input_value)){
	$value_ip = $input_value->name;
	$data_value = $input_value->class;
}else{
	$value_ip = $data_value = $input_value;
}
$data_val = str_replace(" ", "_", $data_value);
if( array_key_exists($data_val, $params['brand_color'])) $dataVal =$params['brand_color'][$data_val];
if( array_key_exists($data_val, $params['user_color'])) $dataVal =$params['user_color'][$data_val];
if(empty($dataVal)) $dataVal = '';
?>

<div class="t4-select-color">
	<div class="color-preview">
		<span class="preview-icon" data-bgcolor="<?php echo $dataVal;?>" style="background-color: <?php echo $dataVal;?>;"></span>
		<input type="text" name="<?php echo $input_name;?>" id="<?php echo $input_id;?>" value="<?php echo $value_ip;?>" data-val="<?php echo str_replace("_", " ", $data_value);?>" data-color="<?php echo $dataVal;?>" class="t4-input t4-input-color" aria-invalid="false" readonly="" />
		<span class="toggle-icon"><i class="fal fa-angle-down"></i></span>
	</div>
	<div class="choose-color-pattern hidden">
		<ul>
			<li class="group-title"><span><?php echo \JText::_('JNONE');?></span></li>
			<li class="t4-select-pattern t4-pt-none" data-val="none" data-color="" data-name="none">
				<span class="preview-icon" data-bgcolor="transparent" style="background-color: transparent;"></span>
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
				foreach ($group_color as $name => $field_color) : ?>
					<li class="t4-select-pattern<?php echo $userColor_class;?>" data-val="<?php echo $name;?>" data-color="<?php echo $field_color;?>" data-name="<?php echo str_replace("_"," ",$name);?>">
						<span class="preview-icon" style="background-color: <?php echo $field_color; ?>;"></span>
						<span class="color-label"><?php echo str_replace("_"," ",$name);?></span>
					</li>
				<?php endforeach;?>
		<?php endforeach;?>

		</ul>
	</div>
</div>