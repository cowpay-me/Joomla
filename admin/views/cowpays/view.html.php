<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cowpay
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorlds View
 *
 * @since  0.0.1
 */
class CowpayViewCowpays extends JViewLegacy
{

	function display($tpl = null)
	{
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}
// Set the toolbar
		$this->addToolBar();
		// Display the template
		parent::display($tpl);
	}

	protected function addToolBar()
	{
		JToolbarHelper::title(JText::_('COM_COWPAY_MANAGER_COWPAYS'));
//		JToolbarHelper::addNew('cowpay.add');
//		JToolbarHelper::editList('cowpay.edit');
//		JToolbarHelper::deleteList('', 'cowpays.delete');
		JToolbarHelper::preferences('com_cowpay');

	}
}