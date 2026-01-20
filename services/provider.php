<?php

declare(strict_types=1);

/**
 * @package    PwtGtm
 *
 * @author     Hans Kuijpers - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-gtm
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use PWT\Plugin\System\Pwtgtm\Extension\Pwtgtm;

return new class implements ServiceProviderInterface {
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   6.0.0
	 */
	public function register(Container $container): void
	{
		$container->set(
			PluginInterface::class,
			function (Container $container): PluginInterface {
				$subject = $container->get(DispatcherInterface::class);
				$config  = (array) PluginHelper::getPlugin('system', 'pwtgtm');
				$plugin  = new Pwtgtm($subject, $config);

				$plugin->setApplication(Factory::getApplication());

				return $plugin;
			}
		);
	}
};