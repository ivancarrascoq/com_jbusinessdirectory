<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');
require_once( JPATH_COMPONENT_ADMINISTRATOR.'/library/category_lib.php');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');
/**
 * List Model.
 *
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 */
class JBusinessDirectoryModelManageCompanies extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'bc.id',
				'registrationCode', 'bc.registrationCode',
				'address', 'bc.address',
				'type', 'ct.name',
				'viewCount', 'bc.viewCount',
				'contactCount', 'bc.contactCount',
				'state', 'bc.state',
				'approved', 'bc.approved'
			);
		}
		
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		parent::__construct($config);
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'ManageCompany', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Overrides the getItems method to attach additional metrics to the list.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getItems()
	{
		// Load the list items.
		$items = parent::getItems();

		
		// If empty or an error, just return.
		if (empty($items)) {
			return array();
		} else {
			foreach($items as $company) {

				$company->active = true;

				$company->features = explode(",",$company->features);
					
			    $company->checklist = JBusinessUtil::getCompletionProgress($company, 1);
			    $company->progress = 0;

			    if(count($company->checklist) > 0) {
                    // calculate percentage of completion
                    $count = 0;
                    $completed = 0;
                    foreach ($company->checklist as $key => $val) {
                        if ($val->status)
                            $completed++;
                        $count++;
                    }
                    $company->progress = (float)($completed / $count);
                }

                $company->progress = round($company->progress, 4);
			}
		}
		
		if($this->appSettings->enable_packages){
		  $items = JBusinessUtil::processPackages($items);
		}
		
		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateBusinessListingsTranslation($items);
		}
		
		return $items;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		$app = JFactory::getApplication();
		$value = $app->input->get('limit', $app->getCfg('list_limit', 0), 'uint');
		$this->setState('list.limit', $value);
		// Create a new query object.
		$db = $this->getDbo();
		
		$query = "SELECT bc.*,GROUP_CONCAT(distinct ct.name) as typeName, clm.id as claimId,
                     GROUP_CONCAT(distinct inv.start_date,'|',inv.start_trial_date,'|',inv.state,'|',inv.package_id  separator '#|') as orders,
                     GROUP_CONCAT(DISTINCT pf.feature) as features
   					FROM `#__jbusinessdirectory_companies` AS bc
					left join `#__jbusinessdirectory_company_category` cc on bc.id=cc.companyId
					LEFT JOIN `#__jbusinessdirectory_company_types` AS ct ON bc.typeId=ct.id
					LEFT JOIN `#__jbusinessdirectory_company_claim` AS clm ON bc.id=clm.companyId
					LEFT JOIN `#__jbusinessdirectory_packages` pk on bc.package_id=pk.id
                    left join #__jbusinessdirectory_package_fields pf on pk.id=pf.package_id
					LEFT JOIN `#__users` u on bc.userId=u.id
					left join `#__jbusinessdirectory_orders` inv on inv.company_id = bc.id
                    ";
		
		$where = " where 1 ";
		
		$user = JFactory::getUser();
		$where.=' and bc.userId ='.$user->id;
		
		$groupBy = " group by bc.id";

		// Add the list ordering clause.
		$orderBy = " order by ". $db->escape($this->getState('list.ordering', 'bc.id')).' '.$db->escape($this->getState('list.direction', 'ASC'));
		
		$query = $query.$where;
		$query = $query.$groupBy;
		$query = $query.$orderBy;
		
		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');
		
		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);

		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);	

		// List state information.
		parent::populateState('bc.id', 'desc');
	}
	
	
	function getCompanyTypes(){
		$companiesTable = $this->getTable("Company");
		return $companiesTable->getCompanyTypes();
	}

	function getTotal(){
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$user = JFactory::getUser();
			$companiesTable = $this->getTable("Company");
			$this->_total = $companiesTable->getTotalListings($user->id);
		}
		return $this->_total;
	}
		
	function getStates(){
		$states = array();
		$state = new stdClass();
		$state->value = 0;
		$state->text = JTEXT::_("LNG_INACTIVE");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 1;
		$state->text = JTEXT::_("LNG_ACTIVE");
		$states[] = $state;
	
		return $states;
	}
	
	function getStatuses(){
		$statuses = array();
		$status = new stdClass();
		$status->value = COMPANY_STATUS_CLAIMED;
		$status->text = JTEXT::_("LNG_NEEDS_CLAIM_APROVAL");
		$statuses[] = $status;
		$status = new stdClass();
		$status->value = COMPANY_STATUS_CREATED;
		$status->text = JTEXT::_("LNG_NEEDS_CREATION_APPROVAL");
		$statuses[] = $status;
		$status = new stdClass();
		$status->value = COMPANY_STATUS_DISAPPROVED;
		$status->text = JTEXT::_("LNG_DISAPPROVED");
		$statuses[] = $status;
		$status = new stdClass();
		$status->value = COMPANY_STATUS_APPROVED;
		$status->text = JTEXT::_("LNG_APPROVED");
		$statuses[] = $status;
	
		return $statuses;
	}
}
