<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HelloWorld Component
 *
 * @since  0.0.1
 */
class CowpayViewCowpay extends JViewLegacy
{

	public $params = [];

	function display($tpl = null)
	{
		// Assign data to the view
		$params  =JComponentHelper::getParams('com_cowpay');
		$this->params = $params->toArray();
		$this->msg = 'asdasdasd';
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

			return false;
		}
		// Display the view
		parent::display($tpl = null);
	}

	function ajax (){
		// Assign data to the view
		$params  =JComponentHelper::getParams('com_cowpay');
		$this->params = $params->toArray();
		$this->msg = 'asdasdasd';
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

			return false;
		}
		// Display the view
		parent::display($tpl);
	}
}