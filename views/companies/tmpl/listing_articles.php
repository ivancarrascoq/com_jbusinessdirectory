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

$plugin = JPluginHelper::getPlugin('content', 'business');
$categoryParam="";
// Check if plugin is enabled
if ($plugin)
{
    // Get plugin params
    $pluginParams = new JRegistry($plugin->params);
    
    $category_id = $pluginParams->get('category_id');
    if(!empty($category_id)){
        $categoryParam ="&id=$category_id";
    }
}

?>

<div class="row-fluid">
    <div class="span12">
        <div id="listing-articles" class="listing-articles">
        	<?php $index = 0;?>
            <?php foreach ($this->companyArticles as $article){?>
				<?php $index ++;?>
                <div class="item article">
                	<a target="_blank" onclick="registerAction(<?php echo $this->company->id ?>,<?php echo STATISTIC_TYPE_ARTICLE_CLICK ?>,<?php echo $article->id ?>)" href="<?php echo JRoute::_('index.php?option=com_content&view=article&id=' . $article->id.'&catid=' . $article->catid); ?>"><?php echo $article->title?></a>
                </div>
                <?php if($index>=4){?>
                	<a class="right" target="_blank" href="<?php echo JRoute::_('index.php?option=com_content&view=category&business_id='.$this->company->id.$categoryParam); ?>"><?php echo JText::_("LNG_MORE")?></a>
                	<?php break;?>
                <?php }?>
            <?php } ?>
        </div>
    </div>
</div>
