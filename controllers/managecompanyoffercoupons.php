<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'models');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'offercoupons.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'offercoupons.php');

class JBusinessDirectoryControllerManageCompanyOfferCoupons extends JBusinessDirectoryControllerOfferCoupons {

	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct() {
		parent::__construct();
		$this->log = Logger::getInstance();
	}

	/**
	 * Removes an item
	 */
	public function delete() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('id', array(), 'array');
	
		if (!is_array($cid) || count($cid) < 1) {
			JError::raiseWarning(500, JText::_('COM_JBUSINESSDIRECTORY_NO_COUPON_SELECTED'));
		}
		else {
			// Get the model.
			$model = $this->getModel("ManageCompanyOfferCoupon");
			
			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);
	
			// Remove the items.
			if (!$model->delete($cid)) {
				$this->setMessage($model->getError());
			} else {
				$this->setMessage(JText::plural('COM_JBUSINESSDIRECTORY_N_COUPONS_DELETED', count($cid)));
			}
		}
	
		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=managecompanyoffercoupons');
	}
}