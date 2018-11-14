<?php
/**
 # JBusinessDirectory
 # author CMSJunkie
 # copyright Copyright (C) 2017 cmsjunkie.com. All Rights Reserved.
 # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Websites: http://www.cmsjunkie.com
 # Technical Support:  Forum - http://www.cmsjunkie.com/forum/j-businessdirectory/?p=1
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Routing class of com_jbusinessdirectory
 *
 * @since  3.3
 */
class JBusinessDirectoryRouter extends JComponentRouterView
{
    protected $noIDs = false;
    
    /**
     * Content Component router constructor
     *
     * @param   JApplicationCms  $app   The application object
     * @param   JMenu            $menu  The menu object to work with
     */
    public function __construct($app = null, $menu = null){
        
        $params = JComponentHelper::getParams('com_jbusinessdirectory');
        $this->noIDs = true;
        
        $search = new JComponentRouterViewconfiguration('search');
        $this->registerView($search);
        
        $listing = new JComponentRouterViewconfiguration('companies');
        $listing->setKey('companyId')->setParent($search,'id');
        $this->registerView($listing);
        
        $offers = new JComponentRouterViewconfiguration('offers');
        $this->registerView($offers);
        
        $offer = new JComponentRouterViewconfiguration('offer');
        $offer->setKey('offerId')->setParent($offers,'id');
        $this->registerView($offer);
        
        $events = new JComponentRouterViewconfiguration('events');
        $this->registerView($events);
        
        $event = new JComponentRouterViewconfiguration('event');
        $event->setKey('eventId')->setParent($events,'id');
        $this->registerView($event);
      
        $this->registerView(new JComponentRouterViewconfiguration('billingdetails'));
        $this->registerView(new JComponentRouterViewconfiguration('businessuser'));
        $this->registerView(new JComponentRouterViewconfiguration('catalog'));
        $this->registerView(new JComponentRouterViewconfiguration('categories'));
        $this->registerView(new JComponentRouterViewconfiguration('invoice'));
        $this->registerView(new JComponentRouterViewconfiguration('managecompany'));
        $this->registerView(new JComponentRouterViewconfiguration('orders'));
        $this->registerView(new JComponentRouterViewconfiguration('payment'));
        $this->registerView(new JComponentRouterViewconfiguration('packages'));
        $this->registerView(new JComponentRouterViewconfiguration('useroptions'));
        $this->registerView(new JComponentRouterViewconfiguration('conferencesessions'));
        $this->registerView(new JComponentRouterViewconfiguration('conferences'));
        $this->registerView(new JComponentRouterViewconfiguration('speakers'));
        parent::__construct($app, $menu);
        
        $this->attachRule(new JComponentRouterRulesMenu($this));
        
      
        $this->attachRule(new JComponentRouterRulesStandard($this));
        //$this->attachRule(new JComponentRouterRulesNomenu($this));

        //JLoader::register('JBusinessDirectoryRouterRulesLegacy', __DIR__ . '/include/legacyrouter.php');
        //$this->attachRule(new JBusinessDirectoryRouterRulesLegacy($this));
    }
    
    
    /**
     * Method to get the segment(s) for an article
     *
     * @param   string  $id     ID of the article to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getCompaniesSegment($id, $query){
        
        if (!strpos($id, ':'))
        {
            $db = JFactory::getDbo();
            $dbquery = $db->getQuery(true);
            $dbquery->select($dbquery->qn('alias'))
            ->from($dbquery->qn('#__jbusinessdirectory_companies'))
            ->where('id = ' . $dbquery->q($id));
            $db->setQuery($dbquery);
            
            $id .= ':' . $db->loadResult();
        }

        if ($this->noIDs)
        {
            list($void, $segment) = explode(':', $id, 2);
            return array($void => $segment);
        }
       
        return array((int) $id => $id);
    }
    
    /**
     * Method to get the segment(s) for an article
     *
     * @param   string  $id     ID of the article to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getOfferSegment($id, $query)
    {
        
        if (!strpos($id, ':'))
        {
            $db = JFactory::getDbo();
            $dbquery = $db->getQuery(true);
            $dbquery->select($dbquery->qn('alias'))
            ->from($dbquery->qn('#__jbusinessdirectory_company_offers'))
            ->where('id = ' . $dbquery->q($id));
            $db->setQuery($dbquery);
            
            $id .= ':' . $db->loadResult();
        }
        
        if ($this->noIDs)
        {
            list($void, $segment) = explode(':', $id, 2);
            return array($void => $segment);
        }
        
        return array((int) $id => $id);
    }
    
    
    /**
     * Method to get the segment(s) for an article
     *
     * @param   string  $id     ID of the article to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getEventSegment($id, $query)
    {
        
        if (!strpos($id, ':'))
        {
            $db = JFactory::getDbo();
            $dbquery = $db->getQuery(true);
            $dbquery->select($dbquery->qn('alias'))
            ->from($dbquery->qn('#__jbusinessdirectory_company_events'))
            ->where('id = ' . $dbquery->q($id));
            $db->setQuery($dbquery);
            
            $id .= ':' . $db->loadResult();
        }
        
        if ($this->noIDs)
        {
            list($void, $segment) = explode(':', $id, 2);
            return array($void => $segment);
        }
        
        return array((int) $id => $id);
    }
    
    
    /**
     * Method to get the segment(s) for an article
     *
     * @param   string  $segment  Segment of the article to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getCompaniesId($segment, $query)
    {
        if ($this->noIDs)
        {
            $db = JFactory::getDbo();
            $dbquery = $db->getQuery(true);
            $dbquery->select($dbquery->qn('id'))
            ->from($dbquery->qn('#__jbusinessdirectory_companies'))
            ->where('alias = ' . $dbquery->q($segment));
            $db->setQuery($dbquery);
            
            return (int) $db->loadResult();
        }
        
        return (int) $segment;
    }
    
    /**
     * Method to get the segment(s) for an article
     *
     * @param   string  $segment  Segment of the article to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getOfferId($segment, $query)
    {
        if ($this->noIDs)
        {
            $db = JFactory::getDbo();
            $dbquery = $db->getQuery(true);
            $dbquery->select($dbquery->qn('id'))
            ->from($dbquery->qn('#__jbusinessdirectory_company_offers'))
            ->where('alias = ' . $dbquery->q($segment));
            $db->setQuery($dbquery);
            
            return (int) $db->loadResult();
        }
        
        return (int) $segment;
    }
    
    
    /**
     * Method to get the segment(s) for an article
     *
     * @param   string  $segment  Segment of the article to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getEventId($segment, $query)
    {
        if ($this->noIDs)
        {
            $db = JFactory::getDbo();
            $dbquery = $db->getQuery(true);
            $dbquery->select($dbquery->qn('id'))
            ->from($dbquery->qn('#__jbusinessdirectory_company_events'))
            ->where('alias = ' . $dbquery->q($segment));
            $db->setQuery($dbquery);
            
            return (int) $db->loadResult();
        }
        
        return (int) $segment;
    }
    
}

/**
 * Content router functions
 *
 * These functions are proxys for the new router interface
 * for old SEF extensions.
 *
 * @param   array  &$query  An array of URL arguments
 *
 * @return  array  The URL arguments to use to assemble the subsequent URL.
 *
 * 
 */
function jBusinessDirectoryBuildRoute(&$query)
{
    $app = JFactory::getApplication();
    $router = new JBusinessDirectoryRouter($app, $app->getMenu());
    
    return $router->build($query);
}

/**
 * Parse the segments of a URL.
 *
 * This function is a proxy for the new router interface
 * for old SEF extensions.
 *
 * @param   array  $segments  The segments of the URL to parse.
 *
 * @return  array  The URL attributes to be used by the application.
 *
 * @since   3.3
 * 
 */
function jBusinessDirectoryParseRoute($segments)
{
    $app = JFactory::getApplication();
    $router = new JBusinessDirectoryRouter($app, $app->getMenu());
    
    return $router->parse($segments);
}
