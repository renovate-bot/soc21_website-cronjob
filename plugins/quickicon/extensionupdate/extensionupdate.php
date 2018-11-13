<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Quickicon.Extensionupdate
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

/**
 * Joomla! update notification plugin
 *
 * @since  2.5
 */
class PlgQuickiconExtensionupdate extends CMSPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Application object.
	 *
	 * @var    \Joomla\CMS\Application\CMSApplication
	 * @since  3.7.0
	 */
	protected $app;

	/**
	 * Returns an icon definition for an icon which looks for extensions updates
	 * via AJAX and displays a notification when such updates are found.
	 *
	 * @param   string  $context  The calling context
	 *
	 * @return  array  A list of icon definition associative arrays, consisting of the
	 *                 keys link, image, text and access.
	 *
	 * @since   2.5
	 */
	public function onGetIcons($context)
	{
		if ($context !== $this->params->get('context', 'mod_quickicon') || !$this->app->getIdentity()->authorise('core.manage', 'com_installer'))
		{
			return array();
		}

		$token    = Session::getFormToken() . '=1';
		$options  = array(
			'url' => Uri::base() . 'index.php?option=com_installer&view=update&task=update.find&' . $token,
			'ajaxUrl' => Uri::base() . 'index.php?option=com_installer&view=update&task=update.ajax&' . $token,
		);

		$this->app->getDocument()->addScriptOptions('js-extensions-update', $options);

		Text::script('PLG_QUICKICON_EXTENSIONUPDATE_UPTODATE', true);
		Text::script('PLG_QUICKICON_EXTENSIONUPDATE_UPDATEFOUND', true);
		Text::script('PLG_QUICKICON_EXTENSIONUPDATE_UPDATEFOUND_MESSAGE', true);
		Text::script('PLG_QUICKICON_EXTENSIONUPDATE_UPDATEFOUND_BUTTON', true);
		Text::script('PLG_QUICKICON_EXTENSIONUPDATE_ERROR', true);
		Text::script('MESSAGE', true);
		Text::script('ERROR', true);
		Text::script('INFO', true);
		Text::script('WARNING', true);

		HTMLHelper::_('behavior.core');
		HTMLHelper::_('script', 'plg_quickicon_extensionupdate/extensionupdatecheck.min.js', array('version' => 'auto', 'relative' => true));

		return array(
			array(
				'link'  => 'index.php?option=com_installer&view=update&task=update.find&' . $token,
				'image' => 'fa fa-star-o',
				'icon'  => '',
				'text'  => Text::_('PLG_QUICKICON_EXTENSIONUPDATE_CHECKING'),
				'id'    => 'plg_quickicon_extensionupdate',
				'group' => 'MOD_QUICKICON_MAINTENANCE'
			)
		);
	}
}
