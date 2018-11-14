<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();
$return = base64_encode(('index.php?option=com_jbusinessdirectory&view=managecompanyarticles'));
if($user->id == 0){
	$app = JFactory::getApplication();
	$app->redirect('index.php?option=com_users&view=login&return='.$return);
}

if(!$this->actions->get('directory.access.listings') && $this->appSettings->front_end_acl){
	$app = JFactory::getApplication();
	$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=useroptions',false), JText::_("LNG_ACCESS_RESTRICTED"), "warning");
}


JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.multiselect');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>

<style>
    .tooltip {
        border-style:none !important;
    }

    .tooltip-inner {
        background-color: rgba(0,0,0,0.55);
        max-width:600px;
        padding:2px 2px;
        text-align:center;
        border-radius:4px;
    }
</style>


<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyarticles');?>" method="post" name="adminForm" id="adminForm">

	<?php if(!empty($this->companies)){?>
		<div>
            <div class="button-row right">
        		<select name="business_id" id="business_id" class="left validate[required]">
        			<?php foreach($this->companies as $company){?>
        				<option value="<?php echo $company->id ?>"><?php echo $company->name ?></option>
        			<?php } ?>
        		</select>
        		<button type="submit" class="ui-dir-button ui-dir-button-green" onclick="Joomla.submitbutton('managecompanyarticles.addNewArticle')">
        			<span class="ui-button-text"><i class="dir-icon-plus-sign"></i> <?php echo JText::_("LNG_ADD_NEW_ARTICLE")?></span>
        		</button>
            </div>
        	<div class="clear"></div>
        </div>
    <?php }else{ ?>
		<?php JError::raiseNotice( 100, JText::_('LNG_ARTICLE_CREATION_NOT_ALLOWED') ); ?>
	<?php } ?>
	<div class="clear"></div>
	<table class="dir-table dir-panel-table table responsive-simple" id="itemList">
		<thead>
			<tr>
				<th width="70%">
					<?php echo JText::_("LNG_NAME")?>
				</th>
				<th>
					<?php echo JText::_("LNG_COMPANY")?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$nrcrt = 1;
			$i=0;
			foreach( $this->items as $item) { ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td align="left">
						<div class="row-fluid">
							<div class="item-name text-left">
								<div class="row-fluid">
									<a target="_blank" href='<?php echo JRoute::_("index.php?option=com_content&task=article.edit&catid=$item->catid&a_id=$item->id&return=$return"); ?>'
										title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>"> 
										<strong><?php echo $item->title ?></strong>
									</a>
								</div>
								
								<div class="row-fluid">
									<a target="_blank" href="<?php echo JRoute::_('index.php?option=com_content&view=article&id=' . $item->id.'&catid=' . $item->catid); ?>" 
										title="<?php echo JText::_('LNG_CLICK_TO_VIEW'); ?>" class="btn btn-xs btn-primary btn-panel"> 
										<?php echo JText::_('LNG_VIEW'); ?>
									</a>
									<a target="_blank" href="<?php echo JRoute::_("index.php?option=com_content&task=article.edit&catid=$item->catid&a_id=$item->id&return=$return"); ?>"
										title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>" class="btn btn-xs btn-success btn-panel">
										<?php echo JText::_('LNG_EDIT'); ?>
									</a>
									<a href="javascript:deleteListingArticle(<?php echo $item->id ?>)" 
										title="<?php echo JText::_('LNG_CLICK_TO_DELETE'); ?>" class="btn btn-xs btn-danger btn-panel">
										<?php echo JText::_('LNG_DELETE'); ?>
									</a>
								</div>
							</div>
						</div>
					</td>
					<td>
						<?php echo $item->company_name ?>
					</td>
				</tr>
			<?php
			$i++;
			} ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">
				</td>
			</tr>
		</tfoot>
	</table>
	<div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
		<?php echo $this->pagination->getListFooter(); ?>
		<div class="clear"></div>
	</div>
	<input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
	<input type="hidden" name="task" id="task" value="" /> 
	<input type="hidden" name="companyId" value="" />
	<input type="hidden" id="article_id" name="article_id" value="" />
	<input type="hidden" id="cid" name="cid" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHTML::_('form.token'); ?> 
</form>

<script>
function deleteListingArticle(id) {
    if (confirm(Joomla.JText._('COM_JBUSINESS_DIRECTORY_ITEMS_CONFIRM_DELETE'))) {
        jQuery("#cid").val(id);
        jQuery("#task").val("managecompanyarticles.delete");
        jQuery("#adminForm").submit();
    }
}
</script>
