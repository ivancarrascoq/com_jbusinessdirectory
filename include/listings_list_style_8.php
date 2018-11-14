<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
require_once JPATH_COMPONENT_SITE.'/classes/attributes/attributeservice.php';

$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
$enableSEO = $appSettings->enable_seo;
$enablePackages = $appSettings->enable_packages;
$enableRatings = $appSettings->enable_ratings;
$enableNumbering = $appSettings->enable_numbering;
$user = JFactory::getUser();

$total_page_string = $this->pagination->getPagesCounter();
$current_page = substr($total_page_string,5,1);
$limitStart = JFactory::getApplication()->input->get('limitstart');
if(empty($limitStart) || ($current_page == 1) || $total_page_string==null ) {
    $limitStart = 0;
}
$showData = !($user->id==0 && $appSettings->show_details_user == 1);

?>

<div id="results-container" itemscope itemtype="http://schema.org/ItemList" <?php echo $this->appSettings->search_view_mode?'style="display: none"':'' ?> class="results-style-8">
    <?php
    if(!empty($this->companies)){
        $itemCount = 1;
        foreach($this->companies as $index=>$company){
            $showLogo = (!empty($company->logoLocation) && ((isset($company->packageFeatures) && in_array(SHOW_COMPANY_LOGO,$company->packageFeatures) || !$enablePackages)));
            ?>
            <?php 
        		  if(!empty($searchModules) && isset($searchModules[$index])){
        		      foreach($searchModules[$index] as $module) {
        		          ?>
        		          <div class="search-result-module">
        		          	<?php echo JModuleHelper::renderModule($module); ?>
        		          </div>
        		          <?php 
        		      } 
        		  }
        		?>
            <div class="result shadow-border <?php echo isset($company->featured) && $company->featured==1?"featured":"" ?>" style="<?php echo !empty($company->featured)?"background-color: $appSettings->listing_featured_bg":"" ?>">
                <div class="business-container row-fluid" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <span itemscope itemprop="item" itemtype="http://schema.org/Organization">
                        <?php if($showLogo){ ?>
	                        <div class="span2">
                                <div class="item-image" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
                                    <a href="<?php echo JBusinessUtil::getCompanyLink($company)?>">
                                        <?php if(isset($company->logoLocation) && $company->logoLocation!=''){?>
                                            <img title="<?php echo $this->escape($company->name)?>" alt="<?php echo $this->escape($company->name)?>" src="<?php echo JURI::root().PICTURES_PATH.$company->logoLocation ?>" itemprop="contentUrl" >
                                        <?php }else{ ?>
                                            <img title="<?php echo $this->escape($company->name)?>" alt="<?php echo $this->escape($company->name)?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" itemprop="contentUrl" >
                                        <?php } ?>
                                    </a>
                              	</div>
                              </div>
                        <?php } ?>
                      <div class="company-details <?php echo $showLogo?'span6':'span8'?>">
                            <h3 class="business-name">
                                <a href="<?php echo JBusinessUtil::getCompanyLink($company)?>"><?php echo $enableNumbering? "<span>".($index + $limitStart + 1).". </span>":""?><span itemprop="name"><?php echo $company->name ?></span></a>
                            </h3>
                            <div class="">
                            	  <ul class="company-links">
                                    <?php if($showData && !empty($company->website) && (isset($company->packageFeatures) && in_array(WEBSITE_ADDRESS,$company->packageFeatures) || !$enablePackages)){
                                        if ($appSettings->enable_link_following) {
                                            $followLink = (isset($company->packageFeatures) && in_array(LINK_FOLLOW, $company->packageFeatures) && $enablePackages) ? 'rel="follow"' : 'rel="nofollow"';
                                        }else{
                                            $followLink ="";
                                        }?>
                                        <li><a <?php echo $followLink ?> target="_blank" title="<?php echo $this->escape($company->name)?> Website" target="_blank"  onclick="registerAction(<?php echo $company->id ?>,<?php echo STATISTIC_TYPE_WEBSITE_CLICK ?>)"  href="<?php echo $this->escape($company->website) ?>"><?php echo JText::_('LNG_WEBSITE') ?></a></li>
                                    <?php } ?>
                                    <?php if($showData && !empty($company->latitude) && !empty($company->longitude) && (isset($company->packageFeatures) && in_array(GOOGLE_MAP,$company->packageFeatures) || !$enablePackages)){?>
                                            <li><a target="_blank" href="<?php echo JBusinessUtil::getDirectionURL($this->location, $company) ?>"><?php echo JText::_("LNG_GET_MAP_DIRECTIONS")?></a></li>
                                    <?php }?>
                                    <li><a href="<?php echo JBusinessUtil::getCompanyLink($company)?>"> <?php echo JText::_('LNG_MORE_INFO') ?></a></li>
                                </ul>
                            </div>
                        </div>
                      	<div class="item-info-container span4">
                        	<div class="custom-attributes-list-view">
                                <?php if(isset($company->customAttributes)){
                                    $renderedContent = AttributeService::renderAttributesFront($company->customAttributes,$enablePackages, $company->packageFeatures);
                                    echo $renderedContent;
                                } ?>
                            </div>
							 <?php if(isset($company->featured) && $company->featured==1){ ?>
        						<div class="featured-text">
        	                        <?php echo JText::_("LNG_FEATURED")?>
                                </div>
        		  	  		  <?php } ?>
                    	</div>
                    </span>
                    <span style="display:none;" itemprop="position"><?php echo $itemCount ?></span>
	                <div class="clear"></div>
	            </div>
            </div>
            <?php
            $itemCount++;
        }
    }
    ?>
</div>