<?php
/**
 * @package    PwtGtm
 *
 * @author     Hans Kuijpers - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-gtm
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;

/**
 * System plugin to add Google Tag Manager script to head and beginning of body
 *
 * @since   4.0.0
 */
class PlgSystemPwtgtm extends CMSPlugin
{
	/**
	 * @var    \Joomla\CMS\Application\CMSApplication
	 *
	 * @since  4.0.0
	 */
	protected $app;

	/**
	 * Get the GTM Id
	 * @return mixed|stdClass
	 *
	 * @since 4.0.0
	 */
	public function getGTMId()
	{
		return $this->params->get('pwtgtm_id', false);
	}

	/**
	 * Add GTM script to head
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function onBeforeCompileHead()
	{
		// Only for frontend
		if (!$this->app->isClient('site'))
		{
			return;
		}

		// Get the document object.
		$document = $this->app->getDocument();

		if ($document->getType() !== 'html')
		{
			return;
		}

		$gtmId = $this->getGTMId();

		if (!$gtmId)
		{
			return;
		}

		$consentScript = "
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		
		if(localStorage.getItem('consentMode') === null){
			gtag('consent', 'default', {
				'ad_storage': 'denied',
				'analytics_storage': 'denied',
				'personalization_storage': 'denied',
				'functionality_storage': 'denied',
				'security_storage': 'denied',
			});
		} else {
			gtag('consent', 'default', JSON.parse(localStorage.getItem('consentMode')));
		}
		";

		$document->getWebAssetManager()
			->addInlineScript($consentScript);

		// Google Tag Manager - party loaded in head
		$headScript = "
		<!-- Google Tag Manager -->
			(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','" . $gtmId . "');
		<!-- End Google Tag Manager -->
	";
		$document->getWebAssetManager()
			->addInlineScript($headScript);
	}

	/**
	 * Add GTM noscript directly after start body
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function onAfterRender()
	{
		// Only for frontend
		if (!$this->app->isClient('site'))
		{
			return;
		}

		// Get the document object.
		$document = $this->app->getDocument();

		if ($document->getType() !== 'html')
		{
			return;
		}

		$gtmId = $this->getGTMId();

		if (!$gtmId)
		{
			return;
		}

		// Google Tag Manager - partly loaded directly after body
		$bodyScript = "<!-- Google Tag Manager -->
<noscript><iframe src=\"//www.googletagmanager.com/ns.html?id=" . $gtmId . "\" height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
<!-- End Google Tag Manager -->
";

		$buffer = $this->app->getBody();

		$buffer = preg_replace("/<body(\s[^>]*)?>/i", "<body\\1>\n" . $bodyScript, $buffer);

		$this->app->setBody($buffer);
	}
}
