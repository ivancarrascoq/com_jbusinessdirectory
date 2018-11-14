<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

class JBusinessDirectoryControllerBusinessUser extends JControllerLegacy
{
	
	function __construct()
	{
		parent::__construct();
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	}

	function checkUser(){
		
		$user = JFactory::getUser();
		$filterParam = "";
		$filter_package = JFactory::getApplication()->input->get("filter_package");
		
		if(!empty($filter_package)){
			$filterParam ="&filter_package=".$filter_package;
		}
		
		if($user->id == 0 && $this->appSettings->allow_user_creation==0){
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=businessuser'.$filterParam, false));
		}else{
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompany&showSteps=true&layout=edit'.$filterParam, false));
		}
		
		return;
	}
	
	function loginUser(){
		
	    $this->checkToken('post');

		$app    = JFactory::getApplication();
	    $input  = $app->input;
	    $method = $input->getMethod();

		$filterParam = "";
		$filter_package = JFactory::getApplication()->input->get("filter_package");

		if(!empty($filter_package)){
			$filterParam ="&filter_package=".$filter_package;
		}
	    
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
	        $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&showOnlyLogin=1&view=businessuser'.$filterParam, false));
	    }
	    
	    // Success
	    if ($options['remember'] == true)
	    {
	        $app->setUserState('rememberLogin', true);
	    }
	    
	    $app->setUserState('users.login.form.data', array());
	    $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompany&showSteps=true&layout=edit'.$filterParam, false));
	   
	}
	
	function addUser(){
		
		$filterParam = "";
		$filter_package = JFactory::getApplication()->input->get("filter_package");
		
		if(!empty($filter_package)){
		    $filterParam ="&filter_package=".$filter_package;
		}
		
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$post = JRequest::get('post');
		if($appSettings->captcha){
			$namespace="jbusinessdirectory.contact";
			$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
			if(!$captcha->checkAnswer($captchaAnswer)){
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=businessuser'.$filterParam, false));
				return;
			}
		}
		
		
		// Check for request forgeries.
		$this->checkToken();
		
		// If registration is disabled - Redirect to login page.
		if (JComponentHelper::getParams('com_users')->get('allowUserRegistration') == 0)
		{
		    $this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
		    
		    return false;
		}
		
		$app   = JFactory::getApplication();
		JModelLegacy::addIncludePath(JPATH_ROOT . '/components/com_users/models', 'Registration');
		JForm::addFormPath(JPATH_ROOT . '/components/com_users/models/forms');
		JForm::addFieldPath(JPATH_ROOT . '/components/com_users/fields');
		$model = JModelLegacy::getInstance('Registration', 'UsersModel', array('ignore_request' => true));
		
		// Get the user data.
		$requestData = $this->input->post->get('jform', array(), 'array');
		
		var_dump($requestData);		
		// Validate the posted data.
		$form = $model->getForm();
		
		if (!$form)
		{
		    JError::raiseError(500, $model->getError());
		    
		    return false;
		}
		
		$data = $post;
		
		// Attempt to save the data.
		$return = $model->register($data);
		
		// Check for errors.
		if ($return === false)
		{
		    // Save the data in the session.
		    $app->setUserState('com_users.registration.data', $data);
		    
		    // Redirect back to the edit screen.
		    $this->setMessage($model->getError(), 'error');
		    $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=businessuser'.$filterParam, false));
		    
		    return false;
		}
		
		// Flush the data from the session.
		$app->setUserState('com_users.registration.data', null);
		
		// Redirect to the profile screen.
		if ($return === 'adminactivate')
		{
		    $this->setMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY'));
		    $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&showOnlyLogin=1&view=businessuser'.$filterParam, false));
		}
		elseif ($return === 'useractivate')
		{
		    $this->setMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE'));
		    $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&showOnlyLogin=1&view=businessuser'.$filterParam, false));
		}
		else
		{
		    $this->setMessage(JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS'));
		    $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompany&showSteps=true&layout=edit'.$filterParam, false));
		}
		
		return true;
	}
}