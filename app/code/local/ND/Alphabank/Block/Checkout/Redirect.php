<?php
 
class ND_Alphabank_Block_Checkout_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $checkout = $this->getOrder()->getPayment()->getMethodInstance();
        if(!$checkout->getFormFields()) {
            Mage::getSingleton('core/session')->addError(Mage::helper('core')->__('Some of the information you have provided is incorrect, please do try again.'));
            $url = Mage::getUrl('checkout/cart');
            Mage::app()->getResponse()->setRedirect($url);
            return;
        }
        $form = new Varien_Data_Form();
        $form->setAction($checkout->getCheckoutUrl());
        $form->setId('alphabank_checkout_checkout')
            ->setName('alphabank_checkout_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);                
        foreach ($checkout->getFormFields() as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }

        $html = '<html><body>';
        $html.= $this->__('You will be redirected to Alpha e-Commerce website in a few seconds ...');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("alphabank_checkout_checkout").submit();</script>';
        $html.= '</body></html>';
        $html = str_replace('<div><input name="form_key" type="hidden" value="'.Mage::getSingleton('core/session')->getFormKey().'" /></div>','',$html);
        
        return $html;
    }
}
