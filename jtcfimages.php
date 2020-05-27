<?php
/**
 *
 * @copyright   Copyright (C) 2015- 2020 JTotal, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use JTFramework\JTlicense;

JLoader::import('components.com_fields.libraries.fieldsplugin', JPATH_ADMINISTRATOR);
require_once  JPATH_PLUGINS . '/system/jtframework/autoload.php';

//JT framework
use JTFramework\Fa5;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

class PlgFieldsJTCFimages extends FieldsPlugin
{

	public function onContentPrepareForm(JForm $form, $data)
	{
		Fa5::getFaCDN();
		parent::onContentPrepareForm($form, $data);
		
	}

	/**
	 *
	 * @param [type] $field
	 * @param DOMElement $parent
	 * @param JForm $form
	 * @return void
	 */
	public function onCustomFieldsPrepareDom($field, DOMElement $parent, JForm $form)
	{

	


		$fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form);

		if (!$fieldNode)
		{
			return $fieldNode;
		}

		$readonly = false;

		if (!FieldsHelper::canEditFieldValue($field))
		{
			$readonly = true;
		}

		$fieldNode->setAttribute('type', 'subform');
		$fieldNode->setAttribute('multiple', 'true');
		//$fieldNode->setAttribute('label', JText::_('JT_CF_IMAGES_TITLE'));
		$fieldNode->setAttribute('layout', 'joomla.form.field.subform.'.$field->fieldparams->get('jt_cf_images_form_layout','repeatable-table'));

	

		$mode = $field->fieldparams->get('jt_cf_images_mode','image');
		$formFile = JPath::clean(JPATH_SITE.'/plugins/fields/jtcfimages/params/jt_cf_images_'.$mode.'.xml');

		if (file_exists($formFile))
		{
			// Attempt to load the xml file.
			if ( ! $xml = simplexml_load_file($formFile))
			{
				throw new Exception(Text::_('JERROR_LOADFILE_FAILED'));
			}
		}




		// Build the form source
		$fieldsXml = new SimpleXMLElement('<form/>');
		//$fields    = $fieldsXml->addChild('fields');

		// Get the form settings
		//$formFields = $field->fieldparams->get('fields');
		

		/*
		// Add the fields to the form
		foreach ($formFields as $index => $formField)
		{
			$child = $fields->addChild('field');
			$child->addAttribute('name', $formField->fieldname);
			$child->addAttribute('type', $formField->fieldtype);
			$child->addAttribute('readonly', $readonly);

			if (isset($formField->fieldfilter))
			{
				$child->addAttribute('filter', $formField->fieldfilter);
			}
		}

		$fieldNode->setAttribute('formsource', $fieldsXml->asXML());
		*/


		$fieldNode->setAttribute('formsource', $xml->asXML());

		// Return the node
		return $fieldNode;
	}


	public function onContentBeforeSave($context, $item, $isNew, $data = array())
	{

		if ($context != 'com_fields.field' || !isset($item->type) || $item->type != 'jtcfimages')
		{
			return true;
		}

		$valida = JTlicense::_checkLicense();

		if(!$valida){
			$fieldparams = json_decode($item->fieldparams);
			$fieldpaarams->jt_cf_images_mode = 'image';
	
			$item->set('fieldparams',json_encode($fieldparams));
		}
	}

	public function onContentAfterSave($context, $item, $isNew, $data = array())
	{
	
 
		return true;
	}







}
