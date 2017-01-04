<?php

class ND_Alphabank_Block_Checkout_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {        
        parent::_construct();
        $this->setTemplate('alphabank/checkout/form.phtml');
    }
    
    public function getCcAvailableTypes()
    {
        return array(
            'VI'=>'VISA', // VISA (VI)
            'MC'=>'MasterCard', // MasterCard (MC)
            'DC'=>'Diners Club', // Diners Club (DC)       
        );
        /*$types = $this->_getConfig()->getCcTypes();
        if ($method = $this->getMethod()) {
            $availableTypes = $method->getConfigData('alphabankcctypes');
            if ($availableTypes) {
                $availableTypes = explode(',', $availableTypes);
                foreach ($types as $code=>$name) {
                    if (!in_array($code, $availableTypes)) {
                        unset($types[$code]);
                    }
                }
            }
        }
        return $types;*/
    }
    public function getMaxInstallments(){
        $installments = Mage::helper('alphabank/data')->getInstallments();
        $orderTotal = Mage::helper('alphabank/data')->getOrderTotal();
        function string_to_array($string) {
            $exploded_string = explode(",",$string);    // explode with ,
            $installments_array = array();
            foreach ($exploded_string as $value) {
                $result = explode(':',$value);  // explode with :
                $installments_array[$result[0]] = $result[1];
            }
            return $installments_array;
        }
        $installments = string_to_array($installments);
        $max_installments = "";
        foreach($installments as $threshold => $threshold_value) {  
            if($orderTotal >= $threshold) {
                $max_installments = $threshold_value;
            }
            if($max_installments > 12) {
                $max_installments = 12;
            }
        }
        return $max_installments;
    }
    public function getInstallmentsOptions($max_installments){
        $i = 1;
        $installments_options = array();
        while($i <= $max_installments){
            $installments_options[] = $i;
            $i++;
        }

        return $installments_options;
    }
}
