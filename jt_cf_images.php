<?php
/**
 *
 * @copyright   Copyright (C) 2011 - 2020 JTotal, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::import('components.com_fields.libraries.fieldsplugin', JPATH_ADMINISTRATOR);

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

class PlgFieldsJT_CF_images extends FieldsPlugin
{
	public function onCustomFieldsPrepareDom($field, DOMElement $parent, JForm $form)
	{

	
		$formFile = JPath::clean(JPATH_SITE.'/plugins/fields/jt_cf_images/params/jt_cf_images_repeat.xml');

		if (file_exists($formFile))
		{
			// Attempt to load the xml file.
			if ( ! $xml = simplexml_load_file($formFile))
			{
				throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
			}
		}

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
		$fieldNode->setAttribute('label', JText::_('JT_CF_IMAGES_TITLE'));
		$fieldNode->setAttribute('layout', 'joomla.form.field.subform.'.$field->fieldparams->get('jt_cf_images_form_layout','repeatable-table'));


		



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




	public function onContentAfterSave($context, $item, $isNew, $data = array())
	{
	
 
		return true;
	}







}
