<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JHtml::_('formbehavior.chosen');

$document = JFactory::getDocument();
$config = new JConfig();

//retrieving current menu item parameters
$currentMenuId = null;
$activeMenu = JFactory::getApplication()->getMenu()->getActive();
$menuItemId="";
if(isset($activeMenu)){
	$currentMenuId = $activeMenu->id ; // `enter code here`
	$menuItemId="&Itemid=".$currentMenuId;
}
else if(!empty($this->appSettings->menu_item_id)){
	$menuItemId = "&Itemid=".$this->appSettings->menu_item_id;
}
$document = JFactory::getDocument(); // `enter code here`
$app = JFactory::getApplication(); // `enter code here`
if(isset($activeMenu)) {
	$menuitem   = $app->getMenu()->getItem($currentMenuId); // or get item by ID `enter code here`
	$params = $menuitem->params; // get the params `enter code here`
} else {
	$params = null;
}

//set page title
if(!empty($params) && $params->get('page_title') != '') {
	$title = $params->get('page_title', '');
}
if(empty($title)) {
	$title = JText::_("LNG_BUSINESS_LISTINGS");
	
	$items = array();
	if(!empty($this->category->name))
		$items[] = $this->category->name;
	if(!empty($this->type))
	    $items[] = $this->type->name;
	if(!empty($this->citySearch))
		$items[] = $this->city->cityName;
	if(!empty($this->regionSearch))
		$items[] = $this->region->regionName;
	if(!empty($this->provinceSearch))
	    $items[]= $this->provinceSearch;
    if(!empty($this->countrySearch))
		$items[]= $this->country->country_name;

	if(!empty($items)){
	    $title .= " ".JText::_("LNG_IN")." ";
		$title .= implode("|",$items);
	}
}
$document->setTitle($title);

//set page meta description and keywords
$description = $this->appSettings->meta_description;
$document->setDescription($description);
$document->setMetaData('keywords', $this->appSettings->meta_keywords);

if(!empty($params) && $params->get('menu-meta_description') != '') {
	$document->setMetaData( 'description', $params->get('menu-meta_description') );
	$document->setMetaData( 'keywords', $params->get('menu-meta_keywords') );
}

if (isset($this->category)){
	if (!empty($this->category->meta_title))
		$document->setTitle($this->category->meta_title);
	if (!empty($this->category->meta_description))
		$document->setMetaData( 'description', $this->category->meta_description );
	if (!empty($this->category->meta_keywords))
		$document->setMetaData( 'keywords', $this->category->meta_keywords );
}

$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
$user = JFactory::getUser();
$enableSearchFilter = $this->appSettings->enable_search_filter;
$fullWidth = true;
$mposition = "dir-search-listing-top";
$topModules = JModuleHelper::getModules($mposition);
$mposition = "dir-search-listing";
if(!empty($this->category)){
    $mposition = "dir-search-".$this->category->alias;
}
$bottomModules = JModuleHelper::getModules($mposition);

if($enableSearchFilter || !empty($topModules) || !empty($bottomModules)){
    $fullWidth = false;
}

//add the possibility to chage the view and layout from http params
$list_layout = JRequest::getVar('list_layout');
if(!empty($list_layout)) {
	$this->appSettings->search_result_view = $list_layout;
}
$view_mode = JRequest::getVar('view_mode');
if(!empty($view_mode)) {
	$this->appSettings->search_view_mode = $view_mode;
}

$setCategory = isset($this->category)?1:0;
$categId = isset($this->categoryId)?$this->categoryId:0;


$showClear = 0;
$url = "index.php?option=com_jbusinessdirectory&view=search";

$searchResultsPositions=array(3,10);
$searchModules = array();
foreach($searchResultsPositions as $position){
    $searchModules[$position] = JModuleHelper::getModules("search-results-".$position);
}
?>

<?php if (!empty($this->params) && $this->params->get('show_page_heading', 1) && !empty($this->params->get('page_heading'))) { ?>
    <div class="page-header">
        <h1 class="title"> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
    </div>
<?php } ?>


<div class="row-fluid">
<?php if(!$fullWidth){?>
	<div class="span3">
    	<?php if(!empty($topModules)) { ?>
    		<div class="search-modules">
    			<?php 
    			foreach($topModules as $module) {
    				echo JModuleHelper::renderModule($module);
    			} ?>
    		</div>
    	<?php } ?>	
        <?php if($enableSearchFilter){?>
        	<?php require "search_filter.php" ?>
		<?php } ?>
					
    	 <?php if (!empty($bottomModules)) { ?>
                <div class="search-modules">
                    <?php
                        foreach ($bottomModules as $module) {
                            echo JModuleHelper::renderModule($module);
                        }
                    ?>
				</div>
        <?php } ?>
	</div>
<?php }?>
<div id="search-results" class="search-results <?php echo $fullWidth ?'search-results-full span12':'search-results-normal span9' ?> ">
	<div class="search-header">
		<?php require "search_filter_params.php" ?>
			
		<div class="clear"></div>
		
		<?php if(isset($this->category) && $this->appSettings->show_cat_description && !empty($this->category->description)) { ?>
			<div class="category-container">
				<?php if(!empty($this->category->imageLocation)) { ?>
					<div class="categoy-image"><img alt="<?php echo $this->category->name?>" src="<?php echo JURI::root().PICTURES_PATH.$this->category->imageLocation ?>"></div>
				<?php } ?>
				<h3><?php echo $this->category->name?></h3>
				<div>
					<div id="category-description" class="dir-cat-description">
						<div class="intro-text">
							<?php echo JBusinessUtil::truncate(JHTML::_("content.prepare", $this->category->description),300) ?>
							<?php if(strlen(strip_tags($this->category->description))>strlen(strip_tags(JBusinessUtil::truncate(JHTML::_("content.prepare", $this->category->description),300)))){?>
								<a class="cat-read-more" href="javascript:void(0)" onclick="jQuery('#category-description').toggleClass('open')">
									<?php echo JText::_("LNG_MORE") ?> </a>
							<?php } ?>
						</div>
						<div class="full-text">
							<?php echo JHTML::_("content.prepare", $this->category->description) ?>
							<a class="cat-read-more" href="javascript:void(0)" onclick="jQuery('#category-description').toggleClass('open')">
									<?php echo JText::_("LNG_LESS") ?> </a>
						</div>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		<?php } else if(!empty($this->country) && $this->appSettings->show_cat_description && false) { ?>
			<div class="category-container">
				<?php if(!empty($this->country->logo)) { ?>
					<div class="categoy-image"><img alt="<?php echo $this->country->country_name?>" src="<?php echo JURI::root().PICTURES_PATH.$this->country->logo ?>"></div>
				<?php } ?>
				<h3><?php echo $this->country->country_name?></h3>
				<div>
					<?php echo JHTML::_("content.prepare", $this->country->description);?>
				</div>
				<div class="clear"></div>
			</div>
		<?php } ?>
	
		<?php 
			jimport('joomla.application.module.helper');
			// this is where you want to load your module position
		    $modules = JModuleHelper::getModules("listing-search");
			?>
			<?php if(isset($modules) && count($modules)>0) { ?>
				<div  class="search-modules">
					<?php 
					$fullWidth = false;
					foreach($modules as $module) {
						echo JModuleHelper::renderModule($module);
					} ?>
					<div class="clear"></div>
				</div>
		<?php } ?>
	
		<div id="search-details">
			<div>
				<div class="search-options">
					<div class="search-options-item">
    					<div class="sortby"><?php echo JText::_('LNG_ORDER_BY');?>: </div>
    					<select name="orderBy" class="chosen shadow-input" onchange="changeOrder(this.value)">
    						<?php echo JHtml::_('select.options', $this->sortByOptions, 'value', 'text',  $this->orderBy);?>
    					</select>
					</div>
					<?php if($this->appSettings->search_result_view != 5) { ?>
						<div class="search-options-item view-mode shadow-input">
							<label><?php echo JText::_('LNG_VIEW')?></label>
							<a id="grid-view-link" class="grid" title="Grid" href="javascript:showGrid()"><?php echo JText::_("LNG_GRID") ?></a>
							<a id="list-view-link" class="list active" title="List" href="javascript:showList()"><?php echo JText::_("LNG_LIST") ?></a>
						</div>
						
						<?php if($this->appSettings->show_search_map) { ?>
							<div class="search-options-item view-mode shadow-input">
								<a id="map-link" class="map <?php echo $this->appSettings->map_auto_show != 1 ? 'active' : '' ?>" title="Grid" href="javascript:showMap(true)">
									<?php echo JText::_("LNG_SHOW_MAP") ?></a>
							</div>
						<?php } ?>
						
						<div id="filter-button" class="search-options-item view-mode" style="display: none">
							<label><?php echo JText::_('LNG_MORE_FILTERS')?></label>
							<i class="dir-icon-filter"></i><i class="dir-icon-window-close-o"></i>
						</div>
					<?php } ?>
					<div class="clear"></div>
				</div>
				
				<div class="search-keyword">
					<div class="result-counter"><?php echo $this->pagination->getResultsCounter()?></div> 
					<?php if( !empty($this->customAtrributesValues) || !empty($this->categoryId) || !empty($this->typeSearch) || !empty($this->searchkeyword) || !empty($this->citySearch) || !empty($this->countrySearch) || !empty($this->regionSearch) || !empty($this->zipCode)) {
						$searchText="";
						if(!empty($this->searchkeyword) || !empty($this->customAtrributesValues)){
							echo "<strong>".JText::_('LNG_FOR')."</strong> ";
		
							$searchText.= !empty($this->searchkeyword)? $this->searchkeyword :"";
							
							if(!empty($this->searchkeyword) && !empty($this->customAtrributesValues)){
								$searchText .=", ";
							}
							
							if( !empty($this->customAtrributesValues) ) {
								foreach($this->customAtrributesValues as $attribute) {
									$searchText.= !empty($attribute)?$attribute->name.", ":"";
								}
							}
							
							$searchText = trim(trim($searchText), ",");
							
							$searchText .=" ";
						}
	
						if(!empty($this->citySearch) || !empty($this->countrySearch) || !empty($this->regionSearch)|| !empty($this->provinceSearch)|| !empty($this->areaSearch) || !empty($this->zipCode)) {
							$searchText.= "<strong>".JText::_('LNG_INTO')."</strong>".' ';
							$searchText.= !empty($this->zipCode)?$this->zipCode.", ":"";
							$searchText.= !empty($this->citySearch)?$this->city->cityName.", ":"";
                            $searchText.= !empty($this->regionSearch)?$this->region->regionName.", ":"";
                            $searchText.= !empty($this->areaSearch)?$this->areaSearch.", ":"";
                            $searchText.= !empty($this->provinceSearch)?$this->provinceSearch.", ":"";
							$searchText.= !empty($this->countrySearch)?$this->country->country_name.", ":"";
							$searchText = trim(trim($searchText), ",");
							$searchText.=" ";
						} 
	
						$searchText.= !empty($this->category->name)?"<strong>".JText::_('LNG_IN')."</strong>".' '.$this->category->name." ":"";
						$searchText.= !empty($this->type->name)?"<strong>".JText::_('LNG_IN')."</strong>".' '.$this->type->name.", ":"";
						$searchText = trim(trim($searchText), ",");
	
						echo $searchText;
						echo '';
					} ?>
				</div>
					<?php if ($this->appSettings->enable_search_letters == 1) {
						//require_once JPATH_COMPONENT_SITE . '/include/letterfilter.php';
					}?>
				<div class="clear"></div>
			</div>
			<div id="search-module" class="search-module">
    			<?php 
    			     $modules = JModuleHelper::getModules("search-middle");
    			     foreach($modules as $module) {
    			         echo JModuleHelper::renderModule($module);
    			     }
    			?>
    			<div class="clear"></div>
    		</div>
		</div>
			
	</div>
	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory'.$menuItemId) ?>" method="<?php echo $this->appSettings->submit_method ?>" name="adminForm" id="adminForm">
		<div id="listing-more-filter" class="listing-filter">
			<div class="filter-actions" onclick="jQuery('#adminForm').submit();">
				<div class="filter-action">
					<i class="dir-icon-filter"></i><br/>
					<?php echo JText::_('LNG_FILTER')?>
				</div>
				<div class="filter-action" onclick="resetMoreFilter()">
					<i class="dir-icon-window-close-o"></i><br/>
					<?php echo JText::_('LNG_CLEAR')?>
				</div>
			</div>
			<div>
				<ul>
                    <?php
                     $moreFilters = JBusinessUtil::getMoreSearchFilterOptions();
                     foreach ($moreFilters as $filterKey => $filter){ ?>
                         <li>
                             <input class="" type="checkbox" name="<?php echo $filter->value; ?>" id="<?php echo $filter->value; ?>" value="1" onclick="checkMoreFilterRule('<?php echo $filter->value; ?>')" <?php echo isset($this->moreFilters[$filter->value])?"checked":"" ?>>
                             <label class="checkbox-label" for=""><?php echo $filter->text;?> </label>
                         </li>
                    <?php } ?>
				</ul>
			</div>	
		</div>
	
		<?php if($this->appSettings->search_result_view != 5 && $appSettings->show_search_map) { ?>
			<div id="companies-map-container">
				<?php require JPATH_COMPONENT_SITE.'/include/search-map.php' ?>
			</div>
		<?php } ?>

		<?php 
		require_once JPATH_COMPONENT_SITE.'/include/listings_grid_view.php';
		if($this->appSettings->search_result_view == 1) {
			require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_1.php';
		} else if($this->appSettings->search_result_view == 2) {
			require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_2.php';
		} else if($this->appSettings->search_result_view == 3) {
			require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_3.php';
		} else if($this->appSettings->search_result_view == 4) {
			require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_4.php';
		} else if($this->appSettings->search_result_view == 5) {
			require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_5.php';
		} else if($this->appSettings->search_result_view == 6) {
			require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_6.php';
		} else if($this->appSettings->search_result_view == 7) {
			require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_7.php';
		} else if($this->appSettings->search_result_view == 8) {
		    require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_8.php';
		} else {
			require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_1.php';
		} ?>

		<div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
			<?php echo $this->pagination->getListFooter(); ?>
			<div class="clear"></div>
		</div>
		<input type='hidden' name='task' value='searchCompaniesByName'/>
		<input type='hidden' name='option' value='com_jbusinessdirectory'/>
		<input type='hidden' name='controller' value='search' />
		<input type='hidden' name='categories' id="categories-filter" value='<?php echo !empty($this->categories)?$this->categories:"" ?>' />
		<input type='hidden' name='view' value='search' />
		<input type='hidden' name='categoryId' id='categoryId' value='<?php echo !empty($this->categoryId)?$this->categoryId:"0" ?>' />
		<input type='hidden' name='searchkeyword' id="searchkeyword" value='<?php echo !empty($this->searchkeyword)?$this->searchkeyword:'' ?>' />
		<input type='hidden' name='letter' id="letter" value='<?php echo !empty($this->letter)?$this->letter:'' ?>' />
		<input type='hidden' name="categorySearch" id="categorySearch" value='<?php echo !empty($this->categorySearch)?$this->categorySearch: '' ?>' />
		<input type='hidden' name='citySearch' id='city-search' value="<?php echo !empty($this->citySearch)?$this->escape($this->citySearch): "" ?>" />
        <input type='hidden' name='regionSearch' id='region-search' value="<?php echo !empty($this->regionSearch)?$this->escape($this->regionSearch): "" ?>" />
        <input type='hidden' name='areaSearch' id='area-search' value="<?php echo !empty($this->areSaearch)?$this->escape($this->areSaearch): "" ?>" />
        <input type='hidden' name='provinceSearch' id='province-search' value="<?php echo !empty($this->provinceSearch)?$this->escape($this->provinceSearch): "" ?>" />
		<input type='hidden' name='countrySearch' id='country-search' value='<?php echo !empty($this->countrySearch)?$this->countrySearch: '' ?>' />
		<input type='hidden' name='typeSearch' id='type-search' value='<?php echo !empty($this->typeSearch)?$this->typeSearch: '' ?>' />
		<input type='hidden' name='zipcode' id="zipcode" value="<?php echo !empty($this->zipCode)?$this->escape($this->zipCode): "" ?>" />
		<input type='hidden' name='radius' id="radius" value='<?php echo !empty($this->radius)?$this->radius: '' ?>' />
		<input type='hidden' name='featured' id="featured" value='<?php echo !empty($this->featured)?$this->featured: '' ?>' />
		<input type='hidden' name='filter-by-fav' id="filter-by-fav" value='<?php echo !empty($this->filterByFav)?$this->filterByFav: '' ?>' />
		<input type='hidden' name='filter_active' id="filter_active" value="<?php echo !empty($this->filterActive)?$this->filterActive: '' ?>" />
		<input type='hidden' name='selectedParams' id='selectedParams' value="<?php echo !empty($this->selectedParams["selectedParams"])?$this->escape($this->selectedParams["selectedParams"]):"" ?>" />
		<input type='hidden' name='form_submited' id="form_submited" value="1" />
        <input type='hidden' name='moreParams' id='moreParams' value="<?php echo !empty($this->moreFilters)?$this->escape(implode(';',$this->moreFilters).';'):"" ?>" />
        <input type='hidden' name='orderBy' id='orderBy' value="<?php echo !empty($this->orderBy)?$this->orderBy:"" ?>" />


        <input type='hidden' name='preserve' id='preserve' value='<?php echo !empty($this->preserve)?$this->preserve: '' ?>' />
		
		<?php if(!empty($this->customAtrributes)){ ?>
			<?php foreach($this->customAtrributes as $key=>$val){?>
				<input type='hidden' class="attribute-search-class" name='attribute_<?php echo $key?>' value='<?php echo $val ?>' />
			<?php } ?>
		<?php } ?>
		
	</form>
	<div class="clear"></div>
</div>

</div>

<div id="login-notice" style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		<div class="dialogContent">
			<h3 class="title"><?php echo JText::_('LNG_INFO') ?></h3>
	  		<div class="dialogContentBody" id="dialogContentBody">				
				<p>
					<?php echo JText::_('LNG_YOU_HAVE_TO_BE_LOGGED_IN') ?>
				</p>
				<p>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=login&return='.base64_encode($url)); ?>"><?php echo JText::_('LNG_CLICK_LOGIN') ?></a>
				</p>
			</div>
		</div>
	</div>
</div>

<?php 
if($this->appSettings->search_result_view == 3) {
	require_once JPATH_COMPONENT_SITE.'/include/listings_list_style_3_util.php';
}

$showNotice = ($this->appSettings->enable_reviews_users && $user->id ==0);
?>

<script>
jQuery(document).ready(function() {
	<?php if($this->appSettings->enable_ratings){?>
        renderSearchAverageRating();
	<?php } ?>

	<?php 
		$load = JRequest::getVar("geo-latitude");
		if(empty($load)){
			$load = JRequest::getVar("latitude");
		}
		$geolocation = JRequest::getVar("geolocation");
		if($geolocation && empty($load) && empty($this->form_submited)){ ?>
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(addCoordinatesToUrl);
			}
	<?php } ?>

	jQuery(".chosen").chosen({width:"165px", disable_search_threshold: 5, inherit_select_classes: true});
	
	jQuery('.button-toggle').click(function() {  
		if(!jQuery(this).hasClass("active")) {
			//jQuery(this).addClass('active');
		}
		jQuery('.button-toggle').not(this).removeClass('active'); // remove buttonactive from the others
	});

	jQuery('#filter-button').click(function() {  
		jQuery(this).toggleClass("active");
		jQuery(".listing-filter").toggleClass("open");

		if(jQuery(this).hasClass("active")){
			jQuery('html, body').animate({
			    scrollTop: jQuery("#listing-more-filter").offset().top
			}, 1000);
		}
	});

	if (jQuery("#moreParams").val().length > 0){
        jQuery(this).toggleClass("active");
        jQuery(".listing-filter").toggleClass("open");
    }

	setTimeout(showMap, 2000);

	<?php if ($this->appSettings->search_view_mode == 1 && $this->appSettings->search_result_view != 5) { ?>
		showGrid();
	<?php } else { ?>
		showList();
	<?php }?>

	//disable all empty fields to have a nice url
    <?php if($this->appSettings->submit_method=="get"){?>
	    jQuery('#adminForm').submit(function() {
	    	jQuery(':input', this).each(function() {
	            this.disabled = !(jQuery(this).val());
	        });
	
	    	jQuery('#adminForm select').each(function() {
		    	if(!(jQuery(this).val()) || jQuery(this).val()==0){
	            	jQuery(this).attr('disabled', 'disabled');
		    	}
	        });
	    });

     <?php }?>

    collapseSearchFilter();
 	if(window.innerWidth<400){
 		jQuery(".search-filter").css("display","none");
 	}
 	applyReadMore();
    setCategoryStatus(<?php echo isset($this->category)?'true':'false' ?>, <?php echo isset($this->categoryId)?$this->categoryId:0; ?>);


    jQuery("#filter-switch").click(function(){
     	//jQuery("#search-filter").toggleClass("open");
     	jQuery("#search-filter").slideToggle(500);   
     	if (jQuery('#search-filter').height()<10){
     		jQuery(this).html("<?php echo JText::_("LNG_HIDE_FILTER")?>")
     	}else{
     		jQuery(this).html("<?php echo JText::_("LNG_SHOW_FILTER")?>")
     	}
    });
    
});
</script>