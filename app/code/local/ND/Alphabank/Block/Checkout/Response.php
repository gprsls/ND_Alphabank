<?php
class ND_Alphabank_Block_Checkout_Response extends Mage_Core_Block_Template
{
    /**
     *  Return Error message
     *
     *  @return	  string
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('alphabank/checkout/response.phtml');
    }
    
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
