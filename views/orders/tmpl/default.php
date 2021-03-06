<?php // no direct access
/**
* @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();
if($user->id == 0){
	$app = JFactory::getApplication();
	$return = base64_encode('index.php?option=com_jbusinessdirectory&view=orders');
	$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.$return,false));
}

?>
<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=orders');?>" method="post" name="adminForm" id="adminForm">
	<div id="orders-holder">
		<?php foreach($this->orders as $order){?>
			<div class="order-container">
				<div class="order-details">
					<h3> <?php echo ($order->order_id) ?></h3>
					<h4> <?php echo ($order->businessName) ?></h4>
                    <?php
                    switch ($order->state) {
                        case 0:
                            echo JText::_("LNG_NOT_PAID");
                            break;
                        case 1:
                            echo JText::_("LNG_PAID");
                            break;
                        case 2:
                            echo JText::_("LNG_CANCELED");
                            break;
                    } ?> | <?php echo JBusinessUtil::getPriceFormat($order->amount) ?> | <?php echo $order->created ?>
				</div>

				<div class="order-options">
					<?php if($order->state == 0){?>
						<button type="button" class="ui-dir-button" onclick="payOrder(<?php echo $order->id ?>, <?php echo $order->company_id ?>)">
								<span class="ui-button-text"><?php echo JText::_("LNG_PAY_NOW")?></span>
						</button>
					<?php }?>
					<button type="button" class="ui-dir-button ui-dir-button-green" onclick="showInvoice(<?php echo $order->id; ?>)">
							<span class="ui-button-text"><?php echo JText::_("LNG_DETAILS")?></span>
					</button>
				</div>
				<div class="clear"></div>
			</div>
		<?php } ?>
		<div class="clear"></div>
		<div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
			<?php echo $this->pagination->getListFooter(); ?>
			<div class="clear"></div>
		</div>
	</div>
	<?php echo JHTML::_('form.token'); ?>
</form>

<form id="payment-form" name="payment-form" method="post" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=billingdetails&layout=edit') ?>">
	<input type="hidden" name="orderId" id="orderId" value="" />
    <input type="hidden" name="companyId" id="companyId" value="" />
</form>




<div id="invoice" class="invoice" style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		
		<div class="dialogContent">
			<iframe id="invoiceIfr" src="about:blank">
			
			</iframe>
		</div>
	</div>
</div>



<script>
// starting the script on page load
	jQuery(document).ready(function(){
	});		
	
	function showInvoice(invoice){
		var baseUrl = "<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=invoice&tmpl=component'); ?>";
		baseUrl = baseUrl + "&invoiceId="+invoice;
		jQuery("#invoiceIfr").attr("src",baseUrl);
		jQuery.blockUI({ message: jQuery('#invoice'), css: {width: 'auto', top: '5%', left:"0", position:"absolute"} });
		jQuery('.blockOverlay').click(jQuery.unblockUI); 
		jQuery('.blockUI.blockMsg').center();
		jQuery(document).scrollTop( jQuery("#invoice").offset().top );
		jQuery("html, body").animate({ scrollTop: 0}, "slow");
	}

	function payOrder(orderId,companyId){
		jQuery("#orderId").val(orderId);
        jQuery("#companyId").val(companyId);
		jQuery("#payment-form").submit();
	}
</script>