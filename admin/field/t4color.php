<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

use Joomla\Registry\Registry;

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  2.5
 */
class JFormFieldT4color extends JFormField
{
    /**
     * The field type.
     *
     * @var        string
     */
    protected $type = 't4color';
    protected $layout = 'field.colors';

    protected function getInput()
    {
        $data = [];

        $data['colors'] = $this->loadColors();
        $data['id'] = $this->id;
        $data['name'] = $this->name;
        $data['value'] = $this->getValues();

        return \JLayoutHelper::render($this->layout, $data, T4PATH_ADMIN . '/layouts');
    }

    protected function loadColors()
    {
        $template = T4Admin\Admin::getTemplate(true);
        $customcolors = (array)json_decode(\T4\Helper\Path::getFileContent('etc/customcolors.json'));
        $temp_Params = new Registry($template->params);
        $t4Theme = \T4\Helper\Path::getFileContent('etc/theme/' . $temp_Params->get('typelist-theme') . '.json');
        if (!$t4Theme) {
            $t4Theme = \T4\Helper\Path::getFileContent('etc/theme/default.json');
        }

        $datas = @json_decode($t4Theme);
        $Color_Params = ['brand_color' => [], 'user_color' => []];
        foreach ($datas as $key => $value) {
            if (preg_match('/^color_([a-z]+)(_|$)/', $key, $match)) {
                $type = 'brand_color';
                $Color_Params[$type][$key] = $value;
            } elseif ($key == 'custom_colors') {
                $type = 'user_color';
                $data = json_decode($value);
                if (empty($data)) {
                    $data = $customcolors;
                } else {
                    $vals = @json_decode($datas->custom_colors);
                    if (empty($vals)) {
                        $vals = [];
                    }

                    // user color
                    foreach ($customcolors as $name => $color) {
                        $value = (!empty($vals->{$name}) && !empty($vals->{$name}->color)) ? $vals->{$name}->color : $color->color;
                        $color->color = $value;
                    }
                    $data = $customcolors;
                }
                foreach ($data as $clsColor => $color_custom) {
                    $Color_Params[$type][$clsColor] = $color_custom->color;
                }
            }
        }
        return $Color_Params;
    }

    protected function getValues()
    {
        $template = T4Admin\Admin::getTemplate(true);
        $temp_Params = new Registry($template->params);
        $t4Theme = \T4\Helper\Path::getFileContent('etc/theme/' . $temp_Params->get('typelist-theme') . '.json');
        if (!$t4Theme) {
            $t4Theme = \T4\Helper\Path::getFileContent('etc/theme/default.json');
        }

        $data = @json_decode($t4Theme);
        if (isset($data->{$this->fieldname})) {
            $customcolors = (array)json_decode(\T4\Helper\Path::getFileContent('etc/customcolors.json'));
            if(isset($customcolors[str_replace(" ","_",$data->{$this->fieldname})])){
                return $customcolors[str_replace(" ","_",$data->{$this->fieldname})];
            }
            return $data->{$this->fieldname};
        }

        return "";
    }
}
