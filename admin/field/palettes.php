<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

require_once 't4color.php';
/**
 * Form Field class for the Joomla Framework.
 *
 * @since  2.5
 */
use Joomla\CMS\Layout\LayoutHelper;
class JFormFieldPalettes extends JFormFieldT4color
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'palettes';
	protected $layout = 'field.palettes';
	
	protected function getInput(){
	    $dataInput['id'] = $this->id;
	    $dataInput['name'] = $this->name;
	    $dataInput['value'] = $this->value;
	    
	    $data['colors'] = $this->loadColors();
	    $data['input'] =  $dataInput;
	    $data['palettes'] =  $this->getPalettes();
	    $data['fields'] =  $this->getFields();
		return LayoutHelper::render($this->layout, $data, T4PATH_ADMIN .'/layouts');
	}


	protected function getPalettes() {
		// get data structure
		$fields = $this->getFields();
		// get base palettes
		$basepalettes = (array) json_decode(T4\Helper\Path::getFileContent('etc/palettes.json', false), true);
		// local palettes
		$file = T4PATH_LOCAL . '/etc/palettes.json';
		$userpalettes = is_file($file) ? (array) json_decode(file_get_contents($file), true) : [];

		$keys = array_unique(array_merge(array_keys($userpalettes),array_keys($basepalettes)));
		$palettes = [];
		$fields[] = ['name' => 'title'];
		foreach ($keys as $key) {
			$base = !empty($basepalettes[$key]) ? $basepalettes[$key] : [];
			$local = !empty($userpalettes[$key]) ? $userpalettes[$key] : [];
			$status = '';
			$palette = [];
			$ovr = $loc = $org = false;
			foreach ($fields as $field) {
				$name = $field['name'];
				if (!empty($base[$name]) && !empty($local[$name]) && $base[$name] != $local[$name]) {
					$value = $local[$name];
					$ovr = true;
				} else if (!empty($base[$name])) {
					$value = $base[$name];
					$org = true;
				} else if (!empty($local[$name])) {
					$value = $local[$name];
					$loc = true;
				}
				$palette[$name] = $value;
			}

			$status = $ovr || ($loc && $org) ? 'ovr' : ($loc ? 'loc' : 'org');
			$palette['status'] = $status;
			$palette['class'] = $key;

			$palettes[$key] = $palette;
		}
		return $palettes;
	}

	/**
	 * Method to get the field label markup for a spacer.
	 * Use the label text or name from the XML element as the spacer or
	 * Use a hr="true" to automatically generate plain hr markup
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   11.1
	 */
	protected function getLabel(){
		return null;
	}


	protected function getFields(){
		static $fields = null;
		if ($fields === null) {
			$fields = [];

			foreach ($this->element->xpath('color') as $option)
			{
				$value = (string) $option['value'];
				$name = (string) $option['name'];
				$text  = (string) $option['title'];
				$tmp = array(
						'value'    => $value,
						'name'    => $name,
						'title'     => JText::_($text),
						'class'    => (string) $option['class'],
				);
				
				// Add the option object to the result set.
				$fields[] = $tmp;
			}
		}

		return $fields;		
	}
		public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$doc = JFactory::getDocument();
		$doc->addScript(T4PATH_ADMIN_URI . '/assets/js/palettes.js');
		return parent::setup($element, $value, $group);
	}
}
