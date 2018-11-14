<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');
?>

<?php if ($this->appSettings->price_list_view == 1) { ?>

    <div class="service-list">
   	<?php if (!empty($this->services_list)) { ?>
        <div class="service-section">
            <div class="service-section-name">
                <?php echo $this->services_list[0]->service_section ?>
            </div>
       		<?php $header = $this->services_list[0]->service_section; ?>
       
            <div class="service-list-container">
            <ul>
            <?php foreach ($this->services_list as $key => $service) { ?>
                
                <?php if ($header != $service->service_section) { ?>
                  	</ul>
                    </div>
                    </div>
                    <div class="service-section">
                        <div class="service-section-name">
                            <?php echo $service->service_section ?>
                        </div>
                        <?php $header = $service->service_section; ?>
                   
                        <div class="service-list-container">
                        <ul>
               <?php } ?>
                <li class="service-item">
                            <div class="row-fluid">
                                <div class="span1">
                                    <img alt="<?php echo $service->service_name ?>" class=""
                                         src="<?php echo !empty($service->service_image) ? JURI::root() . PICTURES_PATH . $service->service_image : JURI::root() . PICTURES_PATH . '/no_image.jpg' ?>"
                                         style="">
                                </div>
                                <div class="span11">
                                    <div class="service-price">
                                   		<?php echo JBusinessUtil::getPriceFormat($service->service_price, $this->appSettings->currency_id); ?>
                                    </div>
                                    <div class="service-name">
                                    	<?php echo $service->service_name ?>
                                    </div>
                                    
                                    <p><?php echo $service->service_description ?></p>
                                </div>
                            </div>
                        </li>
                   
                
            <?php } ?>
            </ul>
          </div>
         
         </div>
        <?php } else { ?>
            <h3><?php echo JText::_('LNG_NO_SERVICES'); ?></h3>
        <?php } ?>
        <br/>
    </div>
    <?php
}else { ?>
    <div class="clearfix grid6 service-list">
        <div id="grid-content" class="grid-content row-fluid grid6">
		 <?php
       	 	if(isset($this->services_list) && !empty($this->services_list)){
                $index = 0;
            	foreach($this->services_list as $index=>$service){
                    $index++;
           ?>
				<article id="post-<?php echo  $service->id ?>" class="post clearfix span2">
					<div class="post-inner">
						<figure class="post-image "   >
							 <?php if(isset($service->service_image) && $service->service_image!=''){?>
	                         	<img title="<?php echo $service->service_name?>" alt="<?php echo $service->service_name?>" src="<?php echo JURI::root().PICTURES_PATH.$service->service_image ?>" >
	                         <?php }else{ ?>
	                             <img title="<?php echo $service->service_name?>" alt="<?php echo $service->service_name?>" src="<?php echo JURI::root().PICTURES_PATH.'/no_image.jpg' ?>" >
	                         <?php } ?>
						</figure>
						 <div class="post-content">
		                     <div class="post-title"><span ><?php echo $service->service_name?></span></div>
	                         <p>
	                            <?php echo $service->service_description?>
	                          </p>
	 						<div class="price"><?php echo JBusinessUtil::getPriceFormat($service->service_price, $this->appSettings->currency_id); ?></div>
	                     </div>
					</div>
				</article>
            <?php if ($index % 5 == 0){ ?>
        </div>
        <div id="grid-content" class="grid-content row-fluid grid6 service-list">
            <?php } ?>
            <?php
            }
            }?>
       <div class="clear"></div>
    </div>
</div>
<?php } ?>


<script>
    jQuery(document).ready(function() {
        //Expand/Collapse Individual Boxes
        jQuery(".expand_heading").toggle(function(){
            jQuery(this).addClass("active");
        }, function () {
            jQuery(this).removeClass("active");
        });
        jQuery(".expand_heading").click(function(){
            jQuery(this).nextAll(".toggle_container:first").slideToggle("slow");
        });

        //Show hide 'expand all' and 'collapse all' text
        jQuery(".expand_all").toggle(function(){
            jQuery(this).addClass("expanded");
        }, function () {
            jQuery(this).removeClass("expanded");
        });

        jQuery(".expand_all").click(function () {
            if (jQuery(this).hasClass("expanded")) {
                jQuery(".toggle_container").slideDown("slow");
            }
            else {
                jQuery(".toggle_container").slideUp("slow");
            }
        });
    });
</script>
