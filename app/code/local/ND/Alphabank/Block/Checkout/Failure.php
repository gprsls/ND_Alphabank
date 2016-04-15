<?php

class ND_Alphabank_Block_Checkout_Failure extends Mage_Core_Block_Template
{
    /**
     *  Return Error message
     *
     *  @return	  string
     */
    public function getErrorMessage ()
    {
        $msg = Mage::getSingleton('checkout/session')->getAlphaErrorMessage();
        Mage::getSingleton('checkout/session')->unsMigsErrorMessage();
        return $msg;
    }

    /**
     * Get continue shopping url
     */
    public function getContinueShoppingUrl()
    {
        return Mage::getUrl('checkout/cart');
    }
}
