<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath('administrator/components/com_jbusinessdirectory/tables');

class PaymentService{
	
	/**
	 * Create all active payment processors that are displyed on front based on database details
	 * 
	 * @param boolean $onlyFrontEnd
	 */
	public static function getPaymentProcessors($onlyFrontEnd = true){
		$paymentProcessors = array();
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_payment_processors where status=1 and displayfront =1 order by ordering ";
		$db->setQuery($query);
		$paymentProcessorsDetails =  $db->loadObjectList();
		
		foreach($paymentProcessorsDetails as $paymentProcessorsDetail){
			$query = "SELECT * FROM #__jbusinessdirectory_payment_processor_fields where processor_id=$paymentProcessorsDetail->id";
			$db->setQuery($query);
			$fields =  $db->loadObjectList();
			foreach($fields as $field){
				if(!empty($field->column_name))
					$paymentProcessorsDetail->{$field->column_name}= $field->column_value;
			}
			
			$processorFactory = new ProcessorFactory();
			$processor = $processorFactory->getProcessor($paymentProcessorsDetail->type);
			$processor->initialize($paymentProcessorsDetail);
			$paymentProcessors[] = $processor;
		}
		return $paymentProcessors;
	}
	
	/**
	 * Retreive processor details from database
	 * 
	 * @param string $type
	 * @return unknown
	 */
	
	static function  getPaymentProcessorDetails($type){
		$db =JFactory::getDBO();
		$processor = new stdClass();

		if($type!=-1){

			$query = " SELECT * FROM #__jbusinessdirectory_payment_processors a
			           left join #__jbusinessdirectory_payment_processor_fields b on a.id = b.processor_id
					   where a.type='$type' order by b.id asc";
			$db->setQuery( $query );
			$fields = $db->loadObjectList();
			if(!empty($fields)){
				$processor =  $fields[0];
			
				foreach($fields as $field){
					$processor->fields[$field->column_name]= $field->column_value;
					if(!empty($field->column_name))
						$processor->{$field->column_name}= $field->column_value;
				}
			}
			
			
		}
		return $processor;
	}
	
	public static function getPaymentDetails($orderId){
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_payments where order_id='$orderId' and transaction_id IS NOT NULL order by payment_id desc";
		$db->setQuery($query);
		$paymentDetails = $db->loadObject();
		return $paymentDetails;
	}
	
	/**
	 * Create payment processor
	 * 
	 * @param string $type
	 */
	public static function createPaymentProcessor($type){
		$processorFactory = new ProcessorFactory();
		$processor = $processorFactory->getProcessor($type);
		
		$initData = self::getPaymentProcessorDetails($type);
		$processor->initialize($initData);
		
		return $processor;
	}
	
	/**
	 * Add a payment into the databse
	 * @param object $paymentDetails
	 */
	public static function addPayment($paymentDetails){
		$payments = JTable::getInstance('Payments','Table', array());
		//dump($confirmationsPayments);
		//dump($paymentDetails);
		if (!$payments->bind($paymentDetails)){
			JError::raiseWarning('error',$payments->getError());
			return false;
		}
		
		if (!$payments->check()){
			JError::raiseWarning('error',$payments->getError());
			return false;
		}
		
		if (!$payments->store()){
			JError::raiseWarning('error',$payments->getError());
			return false;
		}
		
		return true;
	}
	
	/**
	 * Add a payment into the databse
	 * @param object $paymentDetails
	 */
	public static function updatePayment($paymentDetails){
		$log = Logger::getInstance();
		$log->LogDebug("Update Payment Details: ".serialize($paymentDetails));
		
		$payments = JTable::getInstance('Payments','Table');
		return $payments->updatePaymentStatus($paymentDetails->order_id, $paymentDetails->amount, $paymentDetails->transaction_id,
				 $paymentDetails->payment_method, $paymentDetails->response_code, $paymentDetails->response_message,$paymentDetails->transactionTime, $paymentDetails->payment_status);

	}
	
	public static function updateOrderDetails($paymentDetails){ 
		$log = Logger::getInstance();
		$orderTable = JTable::getInstance("Order", "JTable");
		$orderTable->load($paymentDetails->order_id);
		
		$orderTable->transaction_id = $paymentDetails->transactionId;
		$orderTable->amount_paid = $paymentDetails->amount;
		$orderTable->paid_at = date("Y-m-d h:m:s");
		$orderTable->state = 1;

		if(!$orderTable->store()){
			$log->LogError("Error updating order. Order ID: ".$paymentDetails->order_id);
		}
		
		$log->LogDebug("Order has been successfully updated. Order ID: ".$paymentDetails->order_id);		
	}
}


?>