<?php
/**
 * Implements the CronjobsHelper class
 *
 * @package       Joomla.Administrator
 * @subpackage    com_cronjobs
 *
 * @copyright (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
 * @license       GPL v3
 *
 */

namespace Joomla\Component\Cronjobs\Administrator\Helper;

// Restrict direct access
defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Event\AbstractEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Cronjobs\Administrator\Cronjobs\CronOptions;
use function \defined;

/**
 * The CronjobsHelper class.
 * Provides static methods used across com_cronjobs
 *
 * @since  __DEPLOY_VERSION__
 */
class CronjobsHelper
{
	/**
	 * Private constructor to prevent instantiation
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	private function __construct()
	{
	}

	/**
	 * Returns available jobs as a CronOptions object
	 *
	 * @return CronOptions  A CronOptions object populated with jobs offered by plugins
	 * @throws Exception
	 * @since  __DEPLOY_VERSION__
	 */
	public static function getCronOptions(): CronOptions
	{
		/**@var AdministratorApplication $app */
		$app = Factory::getApplication();
		$options = new CronOptions;
		$event = AbstractEvent::create(
			'onCronOptionsList',
			[
				'subject' => $options
			]
		);

		// TODO : Implement an object for $items
		PluginHelper::importPlugin('job');
		$app->getDispatcher()->dispatch('onCronOptionsList', $event);

		return $options;
	}
}