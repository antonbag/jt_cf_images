<?php
/**
 * GMapFP! Openstreet Field Project
 * Version J3_1
 * Creation date: Novembre 2019
 * Author: Fabrice4821 - https://creation-web.pro
 * Author email: webmaster@gmapfp.org
 *
 * @copyright   Copyright (C) 2011 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

$value = $field->value;
if ($value == '') return;
/************************************ */


//params
//var_dump($fieldParams);
$wrapper = $fieldParams->get('jt_cf_images_wrapper','0');
$wrapper_class = $fieldParams->get('jt_cf_images_wrapper_class','img_wrapper');
$image_class = $fieldParams->get('jt_cf_images_images_class','img-thumbnail');


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
