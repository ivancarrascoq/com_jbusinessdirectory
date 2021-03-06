<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');


class OfferUserDataService
{
    /**
     * Create user data for storing order details
     *
     * @param $data
     * @param $userData
     * @return stdClass
     */
    public static function createUserData($data, $userData)
    {
        if (empty($userData))
            $userData = new stdClass();

        if (!isset($userData->first_name))
            $userData->first_name = '';
        if (!isset($userData->last_name))
            $userData->last_name = '';
        if (!isset($userData->address))
            $userData->address = '';
        if (!isset($userData->city))
            $userData->city = '';
        if (!isset($userData->state_name))
            $userData->state_name = '';
        if (!isset($userData->country))
            $userData->country = '';
        if (!isset($userData->postal_code))
            $userData->postal_code = '';
        if (!isset($userData->phone))
            $userData->phone = '';
        if (!isset($userData->email))
            $userData->email = '';
        if (!isset($userData->conf_email))
            $userData->conf_email = '';
        
        return $userData;
    }

    /**
     * Method to initialize Offer User Data
     *
     * @param bool|false $resetUserData
     * @return null|stdClass
     */
    public static function initializeUserData($resetUserData = false)
    {
        $get = JRequest::get('get');
        $post = JRequest::get('post');
        if (count($post) == 0)
            $post = $get;

        $userData = isset($_SESSION['offerUserData']) ? $_SESSION['offerUserData'] : null;
        if (!isset($userData) || $resetUserData) {
            $userData = self::createUserData($post, $userData);
            $_SESSION['offerUserData'] = $userData;
        }

        if (!isset($userData->buyerDetails)) {
            $guestDtls = new stdClass();
            $guestDtls->first_name = "";
            $guestDtls->last_name = "";
            $guestDtls->address = "";
            $guestDtls->city = "";
            $guestDtls->county = "";
            $guestDtls->country_name = "";
            $guestDtls->postalCode = "";
            $guestDtls->phone = "";
            $guestDtls->email = "";
            $userData->buyerDetails = $guestDtls;
        }

        $_SESSION['offerUserData'] = $userData;
        return $userData;
    }

    /**
     * Method to get user data object created from session data
     *
     * @return mixed|null|stdClass
     */
    public static function getUserData()
    {
        $session = self::getJoomlaSession();
        $userData = isset($_SESSION['offerUserData']) ? $_SESSION['offerUserData'] : null;
        if (!isset($userData)) {
            $userData = self::initializeUserData();
            $_SESSION['offerUserData'] = $userData;
        }

        return $userData;
    }

    /**
     * Adds the buyer details and saves them in the session
     *
     * @param $buyerDetails array containing the buyer details
     */
    public static function addBuyerDetails($buyerDetails)
    {
        $userData = $_SESSION['offerUserData'];
        $buyerDtls = new stdClass();
        $buyerDtls->first_name = ucfirst($buyerDetails["first_name"]);
        $buyerDtls->last_name = ucfirst($buyerDetails["last_name"]);
        $buyerDtls->address = ucfirst($buyerDetails["address"]);
        $buyerDtls->city = $buyerDetails["city"];
        $buyerDtls->county = $buyerDetails["region"];
        $buyerDtls->country_name = $buyerDetails["country"];
        $buyerDtls->postalCode = strtoupper($buyerDetails["postal_code"]);
        $buyerDtls->phone = $buyerDetails["phone"];
        $buyerDtls->email = $buyerDetails["email"];

        $userData->buyerDetails = $buyerDtls;

        $_SESSION['offerUserData'] = $userData;
    }

    /**
     * Method to get the current Joomla Session
     *
     * @return JSession
     * @throws Exception
     */
    private static function getJoomlaSession()
    {
        $session = JFactory::getSession();
        if ($session->getState() !== 'active') {
            $app = JFactory::getApplication();
            $msg = "Your session has expired";
            $app->redirect('index.php?option=' . JBusinessUtil::getComponentName() . '&view=offers', $msg);
            $app->enqueueMessage("Your session has expired", 'warning');
        } else
            return $session;
    }
}