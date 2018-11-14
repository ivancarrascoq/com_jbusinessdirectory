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
class JBusinessDirectoryModelManageCompanyArticles extends JModelList
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
				'id', 'ct.title','c.name', 'ua.name', 'ct.created'
			);
		}
		
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		parent::__construct($config);
	}

	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 *
	 * @since   1.6
	 */
	protected function getListQuery()	{
		
		
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		// Select all fields from the table.
		$query->select($this->getState('list.select', 'ct.*'));
		$query->from($db->quoteName('#__content').' AS ct');
		
		$query->join('INNER', $db->quoteName('#__jbusinessdirectory_company_articles').' AS ca on ct.id = ca.article_id');
		
		$query->select("c.name as company_name");
		$query->join('INNER', $db->quoteName('#__jbusinessdirectory_companies').' AS c on c.id = ca.company_id');
		
		// Join over the users for the author.
		$query->select('ua.name AS author_name')
		->join('LEFT', '#__users AS ua ON ua.id = ct.created_by');
		
		$user = JFactory::getUser();
		$query->where("c.userId = ".$user->id);
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
		    $query->where("ct.title LIKE '%".trim($db->escape($search))."%' or c.name LIKE '%".trim($db->escape($search))."%'");
		}
		
		$query->group('ct.id');
		
		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'ct.id')).' '.$db->escape($this->getState('list.direction', 'DESC')));
		
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
		parent::populateState('ct.id', 'desc');
	}
	
	
	public function getUserListings(){
	    $db = $this->getDbo();
	    $user = JFactory::getUser();
	    $query = "SELECT * FROM `#__jbusinessdirectory_companies` AS bc where bc.userId =$user->id";
	    
	    $db->setQuery($query);
	    $result =  $db->loadObjectList();
	    
	    return $result;
	}
	
	/**
	 * Removes an item
	 */
	public function delete($id)
	{
	    $row = $this->getTable("CompanyArticles");
	    return $row->delete($id[0]);
	    
	}
}
