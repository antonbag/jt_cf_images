<?php
/**
 *
 * @copyright   Copyright (C) 2011 - 2020 JTotal, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

//TEMPLATE FILE

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

$value = $field->value;
if ($value == '') return;
/************************************ */


//PARAMS
$type = $fieldParams->get('jt_cf_images_mode','image');
$wrapper = $fieldParams->get('jt_cf_images_wrapper','0');
$wrapper_class = $fieldParams->get('jt_cf_images_wrapper_class','img_wrapper');
$image_class = $fieldParams->get('jt_cf_images_images_class','img-thumbnail');






/****************************/
/****************************/
/****************************/
/*FOLDER*******************/
/****************************/
/****************************/
/****************************/

if($type == 'folder'){

	
	//var_dump($value);


	$base = JPATH_SITE.'/images';
	// Get some paths from the request
	if (empty($base))
	{
		$base = JPATH_SITE.'/images';
	}

	// Corrections for windows paths
	$base = str_replace(DIRECTORY_SEPARATOR, '/', $base);
	
	jimport('joomla.filesystem.files');

	$files = JFolder::files($base, '.', false, false);

		//var_dump($files);
	$html = '';
	if($wrapper){
		$html .= '<div class="'.$wrapper_class.'">';
	}

	foreach(json_decode($value) as $key => $val){

		$cf_images = new JRegistry; 
		$cf_images->loadObject($val);


		if($cf_images->get('jtfolder','') == '') continue;
		if($cf_images->get('jtfolder','') == '-1') continue;

		$path = JPATH_SITE.'/images/'.$cf_images->get('jtfolder','');


		$files = JFolder::files($path, '.jpg|.png|.jpeg|.gif|.JPG|.JPEG|.jpeg|.webp', false, false);

		if(empty($files)) continue;

		foreach($files as $key2 => $file){
			

			$html .= '<img src="'.'images/'.$cf_images->get('jtfolder','').'/'.$file.'" alt="'.$cf_images->get('alt','jt image').'" class="'.$cf_images->get('class','img-fluid').' '.$image_class.'"/>';
		}


	    //$html .= '<img src="'.$cf_images->get('src','').'" data-caption="'.$cf_images->get('caption','').'" alt="'.$cf_images->get('alt','jt image').'" />';
	
	}

	
	if($wrapper){
		$html .= '</div>';
	}

	echo $html;

}




/****************************/
/****************************/
/****************************/
/*IMAGE*******************/
/****************************/
/****************************/
/****************************/

if($type == 'image'){


	//results
	$html = '';
	if($wrapper){
		$html .= '<div class="'.$wrapper_class.'">';
	}

	//<a href="'.$enlace_modal.'" class="modal cf_a" rel="{handler: \'iframe\', size: {x: 1024, y: 600}}"><span class="cf_span_filtrador'.$jt_cf_debug.'">'.$common_value.'</span>'.$common_name.'</a>




	foreach(json_decode($value) as $key => $val){

		$cf_images = new JRegistry; 
		$cf_images->loadObject($val);


		$html .= '<a';

		if($cf_images->get('url_on',0)){
			if($cf_images->get('target','modal') == 'modal'){
				JHtml::_('behavior.modal');
				if($cf_images->get('url','') != '') $html .= ' class="modal" href="'.$cf_images->get('url','').'" rel="{handler: \'iframe\', size: {x: 1024, y: 600}}" rel="nofollow"';
			}else{
				if($cf_images->get('url','') != '') $html .= ' href="'.$cf_images->get('url','').'" target="'.$cf_images->get('target','modal').'" rel="nofollow"';
			}
		}

		$html .= '>';

		$html .= '<img src="'.$cf_images->get('src','').'" alt="'.$cf_images->get('alt','jt image').'" class="'.$cf_images->get('class','img-fluid').' '.$image_class.'"/>';
		$html .= '</a>';


	}




	if($wrapper){
		$html .= '</div>';
	}

	echo $html;

}
