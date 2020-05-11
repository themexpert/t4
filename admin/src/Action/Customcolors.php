<?php
namespace T4Admin\Action;

use Joomla\CMS\Factory as JFactory;

class Customcolors {
	public static function doSave () {
		$input = JFactory::getApplication()->input->post;
		$data =  $input->getRaw('data');
		if (empty($data)) {
			return ['error' => 'Missing params'];
		}
		$userColorFile = T4PATH_LOCAL . '/etc/customcolors.json';
		$dir = dirname($userColorFile);
		if (!is_dir($dir)) \JFolder::create($dir);
		$colors = is_file($userColorFile) ? json_decode(file_get_contents($userColorFile), true) : (array) json_decode(\T4\Helper\Path::getFileContent('etc/customcolors.json', false), true);
		$dataColor = [];
		if(!isset($data['class'])){
			foreach ($data as $colorClass) {
				if(isset($colors[$colorClass])){
					$dataColor[$colorClass] = $colors[$colorClass];
				}else{
					$dataColor[$colorClass] = self::getBaseUserColors($colorClass);
				}
			}
			$output = ["ok" => 1,"status"=>"order"];
		}else {
			$name = $data['class'];
    	$dataColor  = $colors;
    	if(!isset($data['color'])){
    		$data['color'] = (isset($colors[$name]['color'])) ? $colors[$name]['color'] : self::getBaseUserColors($colorClass)['color'];
    	}
  		$dataColor[$name] = $data;
  		$output = ["ok" => 1,"status"=>"add"];
		}
		\JFile::write ($userColorFile, json_encode($dataColor));
		return $output;
	}
	public static function doRemove () {
		$name = JFactory::getApplication()->input->post->getVar('name');
		$name = str_replace(" ", "_", $name);
		if (!$name) {
			return ['error' => \JText::_('T4_CUSTOM_COLOR_MISSING_PARAMS')];
		}
    $userColorFile = T4PATH_LOCAL . '/etc/customcolors.json';
    $colors = is_file($userColorFile) ? json_decode(file_get_contents($userColorFile), true) : [];
    if (isset($colors[$name])) {
        unset($colors[$name]);
        // write to file 
        if (!\JFile::write ($userColorFile, json_encode($colors))) {
            $output = ['error' => \JText::_('T4_CUSTOM_COLOR_DELETE_ERROR')];
        } else {
        	$output['status'] = 'loc';
        	$colors = self::getBaseUserColors($name);
       		if(isset($colors['color'])){
       			$output['status'] = 'org';
            $output['color'] = $colors['color'];
       		}
          $output['ok'] = 1;
        }
    }else {
        $output = ['error' => \JText::_('T4_CUSTOM_COLOR_DELETE_NOTFOUND_ERROR')];
    }

    return $output;
	}
	public static function getBaseUserColors($name){
		// get base custom colors
		$baseUsercolors = (array) json_decode(\T4\Helper\Path::getFileContent('etc/customcolors.json', false), true);
		$colors = (isset($baseUsercolors[$name])) ? $baseUsercolors[$name] : "";
		return $colors;
	}
	
}