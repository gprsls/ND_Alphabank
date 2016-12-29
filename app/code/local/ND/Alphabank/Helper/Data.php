<?php

class ND_Alphabank_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getInstallments()
    {
        $installments = Mage::getStoreConfig('payment/alphabank_checkout/installments',Mage::app()->getStore());          
        return $installments;
    }

    public function getOrderTotal()
    {
        $orderTotal = Mage::getSingleton('checkout/session')->getQuote()->getGrandTotal();         
        return $orderTotal;
    }
}
