<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2020 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

use Joomla\CMS\Factory;
/**
 * JTInstaller Helper
 V1.0.1
 */
class PlgFieldsJtcfImagesInstallerScriptHelper
{
	public $name = '';
	public $alias = '';
	public $extname = '';
	public $extension_type = '';
	public $plugin_folder = 'system';
	public $module_position = 'status';
	public $client_id = 1;
	public $install_type = 'install';
	public $show_message = true;
	public $autopublish = true;
	public $db = null;
	public $app = null;
	public $installedVersion;

	public function __construct(&$params)
	{
		$this->extname = $this->extname ?: $this->alias;
		$this->db = Factory::getDbo();
		$this->app = Factory::getApplication();
		$this->installedVersion = $this->getVersion($this->getInstalledXMLFile());
	}
	
	public function jtfwInstalled()
	{
	   if (is_file(JPATH_PLUGINS . '/system/jtframework/autoload.php')) return true;
	   return false;
	}
	
	/**
	 *  Checks if current version is newer than the installed one
	 *
	 *  @return  boolean  [description]
	 */
	public function isNewer()
	{
		if (!$installed_version = $this->getVersion($this->getInstalledXMLFile()))
		{
			return true;
		}

		$package_version = $this->getVersion();

		return version_compare($installed_version, $package_version, '<=');
	}
	
	
	
	public function getVersion($file = '')
	{
		$file = $file ?: $this->getCurrentXMLFile();

		if (!is_file($file))
		{
			return '';
		}

		$xml = JInstaller::parseXMLInstallFile($file);

		if (!$xml || !isset($xml['version']))
		{
			return '';
		}

		return $xml['version'];
	}
	
		
	
	/**
	 *  Preflight event
	 *
	 *  @param   string            
	 *  @param   JAdapterInstance
	 *
	 *  @return  boolean                      
	 */
	public function preflight($route, $adapter)
	{
		if (!in_array($route, array('install', 'update')))
		{
			return;
		}

		Factory::getLanguage()->load('plg_system_jtframework', JPATH_PLUGINS . '/system/jtframework/language');


		if ($this->show_message && $this->isInstalled())
		{
			$this->install_type = 'update';
		}

		if ($this->onBeforeInstall() === false)
		{
			return false;
		}
	}

	/**
	 *  Preflight event
	 *
	 *  @param   string            
	 *  @param   JAdapterInstance
	 *
	 *  @return  boolean                      
	 */
	public function postflight($route, $adapter)
	{





		if (!in_array($route, array('install', 'update')))
		{
			return;
		}

		if ($this->onAfterInstall() === false)
		{
			return false;
		}

		if ($route == 'install' && $this->autopublish)
		{
			$this->publishExtension();
		}

		if ($this->show_message)
		{
			$this->addInstalledMessage();
		}

		JFactory::getCache()->clean('com_plugins');
		JFactory::getCache()->clean('_system');
	}

	public function isInstalled()
	{
		if (!is_file($this->getInstalledXMLFile()))
		{
			return false;
		}

		$query = $this->db->getQuery(true)
			->select('extension_id')
			->from('#__extensions')
			->where($this->db->quoteName('type') . ' = ' . $this->db->quote($this->extension_type))
			->where($this->db->quoteName('element') . ' = ' . $this->db->quote($this->getElementName()));
		$this->db->setQuery($query, 0, 1);
		$result = $this->db->loadResult();

		return empty($result) ? false : true;
	}

	public function getMainFolder()
	{
		switch ($this->extension_type)
		{
			case 'plugin' :
				return JPATH_SITE . '/plugins/' . $this->plugin_folder . '/' . $this->extname;

			case 'component' :
				return JPATH_ADMINISTRATOR . '/components/com_' . $this->extname;

			case 'module' :
				return JPATH_ADMINISTRATOR . '/modules/mod_' . $this->extname;

			case 'library' :
				return JPATH_SITE . '/libraries/' . $this->extname;
		}
	}

	public function getInstalledXMLFile(){
		return $this->getXMLFile($this->getMainFolder());
	}

	public function getCurrentXMLFile(){
		return $this->getXMLFile(__DIR__);
	}

	public function getXMLFile($folder){
			return $folder . '/' . $this->extname . '.xml';

	}

	public function foldersExist($folders = array()){
		foreach ($folders as $folder)
		{
			if (is_dir($folder))
			{
				return true;
			}
		}

		return false;
	}

	public function publishExtension()
	{
		switch ($this->extension_type)
		{
			case 'plugin' :
				$this->publishPlugin();

			case 'module' :
				$this->publishModule();
		}
	}

	public function publishPlugin()
	{
		$query = $this->db->getQuery(true)
			->update('#__extensions')
			->set($this->db->quoteName('enabled') . ' = 1')
			->where($this->db->quoteName('type') . ' = ' . $this->db->quote('plugin'))
			->where($this->db->quoteName('element') . ' = ' . $this->db->quote($this->extname))
			->where($this->db->quoteName('folder') . ' = ' . $this->db->quote($this->plugin_folder));
		$this->db->setQuery($query);
		$this->db->execute();
	}

	public function publishModule()
	{
		// Get module id
		$query = $this->db->getQuery(true)
			->select('id')
			->from('#__modules')
			->where($this->db->quoteName('module') . ' = ' . $this->db->quote('mod_' . $this->extname))
			->where($this->db->quoteName('client_id') . ' = ' . (int) $this->client_id);
		$this->db->setQuery($query, 0, 1);
		$id = $this->db->loadResult();

		if (!$id)
		{
			return;
		}

		// check if module is already in the modules_menu table (meaning is is already saved)
		$query->clear()
			->select('moduleid')
			->from('#__modules_menu')
			->where($this->db->quoteName('moduleid') . ' = ' . (int) $id);
		$this->db->setQuery($query, 0, 1);
		$exists = $this->db->loadResult();

		if ($exists)
		{
			return;
		}

		// Get highest ordering number in position
		$query->clear()
			->select('ordering')
			->from('#__modules')
			->where($this->db->quoteName('position') . ' = ' . $this->db->quote($this->module_position))
			->where($this->db->quoteName('client_id') . ' = ' . (int) $this->client_id)
			->order('ordering DESC');
		$this->db->setQuery($query, 0, 1);
		$ordering = $this->db->loadResult();
		$ordering++;

		// publish module and set ordering number
		$query->clear()
			->update('#__modules')
			->set($this->db->quoteName('published') . ' = 1')
			->set($this->db->quoteName('ordering') . ' = ' . (int) $ordering)
			->set($this->db->quoteName('position') . ' = ' . $this->db->quote($this->module_position))
			->where($this->db->quoteName('id') . ' = ' . (int) $id);
		$this->db->setQuery($query);
		$this->db->execute();

		// add module to the modules_menu table
		$query->clear()
			->insert('#__modules_menu')
			->columns(array($this->db->quoteName('moduleid'), $this->db->quoteName('menuid')))
			->values((int) $id . ', 0');
		$this->db->setQuery($query);
		$this->db->execute();
	} 

	public function addInstalledMessage()
	{
		JFactory::getApplication()->enqueueMessage(
			JText::sprintf(
				JText::_($this->install_type == 'update' ? 'JT_THE_EXTENSION_HAS_BEEN_INSTALLED_SUCCESSFULLY' : 'JT_THE_EXTENSION_HAS_BEEN_UPDATED_SUCCESSFULLY'),
				'<strong>' . JText::_($this->name) . '</strong>',
				'<strong>' . $this->getVersion() . '</strong>',
				$this->getFullType()
			)
		);
	}

	public function getPrefix()
	{
		switch ($this->extension_type)
		{
			case 'plugin';
				return JText::_('plg_' . strtolower($this->plugin_folder));

			case 'component':
				return JText::_('com');

			case 'module':
				return JText::_('mod');

			case 'library':
				return JText::_('lib');

			default:
				return $this->extension_type;
		}
	}

	public function getElementName($type = null, $extname = null)
	{
		$type = is_null($type) ? $this->extension_type : $type;
		$extname = is_null($extname) ? $this->extname : $extname;

		switch ($type)
		{
			case 'component' :
				return 'com_' . $extname;

			case 'module' :
				return 'mod_' . $extname;

			case 'plugin' :
			default:
				return $extname;
		}
	}


	public function getFullType()
	{
		return JText::_('JT_' . strtoupper($this->getPrefix()));
	}

	/**
	 *  Helper method triggered before installation
	 *
	 *  @return  bool
	 */
	public function onBeforeInstall()
	{

	}

	/**
	 *  Helper method triggered after installation
	 */
	public function onAfterInstall()
	{

	}
}
