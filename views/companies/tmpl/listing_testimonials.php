<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');
JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/owl.carousel.min.css');
JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/owl.theme.min.css');
JHTML::_('script',  'components/com_jbusinessdirectory/assets/js/owl.carousel.min.js');
?>


<div class="row-fluid">
    <div class="span12">
        <div id="testimonial-slider" class="owl-carousel owl-theme">
            <?php foreach ($this->companyTestimonials as $testimonial){?>
                <div class="item testimonial">
                    <div class="testimonial-content">
                        <p class="description">
                            <?php echo $testimonial->testimonial_description; ?>
                        </p>
                        <h3 class="testimonial-title"><?php echo $testimonial->testimonial_title; ?></h3>
                        <small class="post"><?php echo (!empty($testimonial->testimonial_title) && !empty($testimonial->testimonial_name))?" / ":"";
                            echo $testimonial->testimonial_name; ?></small>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery("#testimonial-slider").owlCarousel({
            items:1,
            itemsDesktop:[1000,2],
            itemsDesktopSmall:[980,1],
            itemsTablet:[767,1],
            pagination:false,
            navigation:true,
            navigationText:["",""],
            slideSpeed:1000,
            autoPlay:true
        });
    });
</script>