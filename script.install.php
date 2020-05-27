<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2020 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;
use Joomla\CMS\Factory;

require_once __DIR__ . '/script.install.helper.php';


class PlgFieldsJtcfImagesInstallerScript extends PlgFieldsJtcfImagesInstallerScriptHelper
{
	public $name = 'Fields - jt images';
	public $alias = 'jtcfimages';
	public $extension_type = 'plugin';
    public $plugin_folder  = 'fields';
	
	
	public function onBeforeInstall()
	{
	
	if(!$this->jtfwInstalled())
		{
            $app = Factory::getApplication();
			$app->enqueueMessage(JText::_('JT framework required. Please, <a target="_blank" href="https://jtotal.org">download at jtotal.org</a> and install it before.'), 'error');
			return false;
		}
	}
}
