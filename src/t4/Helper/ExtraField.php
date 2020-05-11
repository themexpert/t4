<?php
namespace T4\Helper;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Session\Session;

class ExtraField {

	public static function extendForm($form, $data) {

		$form_name = $form->getName();
		// Extend extra field
		$template = TemplateStyle::getDefault();

		if ($template) {

			// make it compatible with AMM
			if ($form_name == 'com_advancedmodules.module') $form_name = 'com_modules.module';

			$tplpath  = JPATH_ROOT . '/templates/' . $template;
			$formpath = $tplpath . '/etc/form/';
			\JForm::addFormPath($formpath);

			$extended = $formpath . $form_name . '.xml';
			if (is_file($extended)) {
				JFactory::getLanguage()->load('tpl_' . $template, JPATH_SITE);
				$form->loadFile($form_name, false);
			}

			// load extra fields for specified module in format com_modules.module.module_name.xml
			if ($form_name == 'com_modules.module') {
				$module = isset($data->module) ? $data->module : '';
				if (!$module) {
					$jform = JFactory::getApplication()->input->get ("jform", null, 'array');
					$module = $jform['module'];
				}
				$extended = $formpath . $module . '.xml';

				if (is_file($extended)) {
					JFactory::getLanguage()->load('tpl_' . $template, JPATH_SITE);
					$form->loadFile($extended, false);
				}
			}

			//extend extra fields
			self::contentExtraFields($form, $data, $tplpath);
		}

		// Extended by T4
		$extended = T4PATH_ADMIN . '/form/' . $form_name . '.xml';
		if (is_file($extended)) {
			JFactory::getLanguage()->load('plg_system_' . T4_PLUGIN, JPATH_ADMINISTRATOR);
			$form->loadFile($extended, false);
		}

	}


	public static function contentExtraFields($form, $data, $tplpath){
		
		if ($form->getName() == 'com_categories.categorycom_content' || $form->getName() == 'com_content.article') {

			// check for extrafields overwrite
			$path = $tplpath . '/etc/extrafields';
			if (!is_dir ($path)) return ;

			$files = \JFolder::files($path, '.xml');
			if (!$files || !count($files)){
				return ;
			}

			$extras = array();
			foreach ($files as $file) {
				$extras[] = \JFile::stripExt($file);
			}
			if (count($extras)) {

				if ($form->getName() == 'com_categories.categorycom_content'){
					
					//load languages
					JFactory::getLanguage()->load('plg_system_' . T4_PLUGIN, JPATH_ADMINISTRATOR);

					$_xml =
						'<?xml version="1.0"?>
						<form>
							<fields name="params">
								<fieldset name="t4_extrafields_params" label="T4_EXTRA_FIELDS_GROUP_LABEL" description="T4_EXTRA_FIELDS_GROUP_DESC">
									<field name="t4_extrafields" type="list" default="" show_none="true" label="T4_EXTRA_FIELDS_LABEL" description="T4_EXTRA_FIELDS_DESC">
										<option value="">JNONE</option>';
									
									foreach ($extras as $extra) {
										$_xml .= '<option value="' . $extra . '">' . ucfirst($extra) . '</option>';
									}

									$_xml .= '
									</field>
								</fieldset>
							</fields>
						</form>
						';
					$xml = simplexml_load_string($_xml);
					$form->load ($xml, false);

				} else {
					
					$app   = JFactory::getApplication();
					$input = $app->input;
					$fdata = empty($data) ? $input->post->get('jform', array(), 'array') : (is_object($data) ? $data->getProperties() : $data);
					
					if(!empty($fdata['catid']) && is_array($fdata['catid'])) { // create new
						$catid = end($fdata['catid']);
					} else { // edit
						$catid = ($fdata['catid']);
					}

					if($catid){
						$categories = \JCategories::getInstance('Content', array('countItems' => 0 ));
						$category = $categories->get($catid);
						$params = $category->params;
						if(!$params instanceof \JRegistry) {
							$params = new \JRegistry;
							$params->loadString($category->params);
						}

						if($params instanceof \JRegistry){
							$extrafile = $path . '/' . $params->get('t4_extrafields') . '.xml';
							if(is_file($extrafile)){
								\JForm::addFormPath($path);
								$form->loadFile($params->get('t4_extrafields'), false);
							}
						}
					}
				}
			}
		}
	}
}