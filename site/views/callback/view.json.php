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
class CowpayViewCallback extends JViewLegacy
{

	public $params = [];

	function display($tpl = null)
	{
		// Assign data to the view
		$input = JFactory::getApplication()->input;
//		$input = JFactory::getApplication()->db;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->clear();
		$fields = array(
			// $db->quoteName('status') . ' = ' . $db->quote($input->post->get('data')['payment_status']),
			$db->quoteName('status') . ' = ' . $db->quote($input->post->get('data')['3']),
		);     
		$conditions = array( 
			// $db->quoteName('reference_id') . ' = ' . $db->quote($input->post->get('data')['cowpay_reference_id'])
			$db->quoteName('reference_id') . ' = ' . $db->quote($input->post->get('data')['5'])
		);

		$query->update($db->quoteName('#__cowpay'))
			->set($fields)
			->where($conditions);

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			$e->getMessage();
		}

		echo new JResponseJson(['status'=>true]);
		return null;
	}
}