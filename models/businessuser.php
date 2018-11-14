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

class JBusinessDirectoryModelBusinessUser extends JModelLegacy
{ 
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Populate state
	 * @param unknown_type $ordering
	 * @param unknown_type $direction
	 */
	protected function populateState($ordering = null, $direction = null){
		$app = JFactory::getApplication('administrator');	
	}
	
	
	function loginUser(){
	    
		$app    = JFactory::getApplication();
		$input  = $app->input;
		$method = $input->getMethod();
		
		// Populate the data array:
		$data = array();
		
		$data['return']    = base64_decode($app->input->post->get('return', '', 'BASE64'));
		$data['username']  = $input->$method->get('username', '', 'USERNAME');
		$data['password']  = $input->$method->get('password', '', 'RAW');
		$data['secretkey'] = $input->$method->get('secretkey', '', 'RAW');
		
		// Check for a simple menu item id
		if (is_numeric($data['return']))
		{
		    if (JLanguageMultilang::isEnabled())
		    {
		        $db = JFactory::getDbo();
		        $query = $db->getQuery(true)
		        ->select('language')
		        ->from($db->quoteName('#__menu'))
		        ->where('client_id = 0')
		        ->where('id =' . $data['return']);
		        
		        $db->setQuery($query);
		        
		        try
		        {
		            $language = $db->loadResult();
		        }
		        catch (RuntimeException $e)
		        {
		            return;
		        }
		        
		        if ($language !== '*')
		        {
		            $lang = '&lang=' . $language;
		        }
		        else
		        {
		            $lang = '';
		        }
		    }
		    else
		    {
		        $lang = '';
		    }
		    
		    $data['return'] = 'index.php?Itemid=' . $data['return'] . $lang;
		}
		else
		{
		    // Don't redirect to an external URL.
		    if (!JUri::isInternal($data['return']))
		    {
		        $data['return'] = '';
		    }
		}
		
		// Set the return URL if empty.
		if (empty($data['return']))
		{
		    $data['return'] = 'index.php?option=com_users&view=profile';
		}
		
		// Set the return URL in the user state to allow modification by plugins
		$app->setUserState('users.login.form.return', $data['return']);
		
		// Get the log in options.
		$options = array();
		$options['remember'] = $this->input->getBool('remember', false);
		$options['return']   = $data['return'];
		
		// Get the log in credentials.
		$credentials = array();
		$credentials['username']  = $data['username'];
		$credentials['password']  = $data['password'];
		$credentials['secretkey'] = $data['secretkey'];
		
		// Perform the log in.
		if (true !== $app->login($credentials, $options))
		{
		    // Login failed !
		    // Clear user name, password and secret key before sending the login form back to the user.
		    $data['remember'] = (int) $options['remember'];
		    $data['username'] = '';
		    $data['password'] = '';
		    $data['secretkey'] = '';
		    $app->setUserState('users.login.form.data', $data);
		    return false;
		}
		
		// Success
		if ($options['remember'] == true)
		{
		    $app->setUserState('rememberLogin', true);
		}
		
		$app->setUserState('users.login.form.data', array());
		return true;
	}
}

?>