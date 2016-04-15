<?php

class ND_Alphabank_CheckoutController extends ND_Alphabank_Controller_Abstract
{
    protected $_redirectBlockType = 'alphabank/checkout_redirect';
    
    public function responseAction()
    {
        $responseParams = $this->getRequest()->getParams();         
        if($responseParams['status']==ND_Alphabank_Model_Info::PAYMENTSTATUS_CANCELED)
        {
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($responseParams['orderid']);
            $order->addStatusToHistory($order->getStatus(), Mage::helper('core')->__('Transaction is aborted by user.'));
            $order->save();
            Mage::getSingleton('core/session')->addError(Mage::helper('core')->__('Transaction is aborted by user.'));
            $this->_redirect('checkout/cart');
            return;
        }
        elseif($responseParams['status']==ND_Alphabank_Model_Info::PAYMENTSTATUS_REFUSED)
        {            
            $userMessageAry = explode("-",$responseParams['message']);
            $userMessage = (count($userMessageAry)>1)?$userMessageAry[1]:$userMessageAry[0];
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($responseParams['orderid']);
            $order->addStatusToHistory($order->getStatus(), $responseParams['message']);
            $order->save();
            Mage::getSingleton('core/session')->addError($userMessage);
            $this->_redirect('checkout/cart');
            return;
        }
        elseif($responseParams['status']==ND_Alphabank_Model_Info::PAYMENTSTATUS_CAPTURED)
        {            
            Mage::getModel('alphabank/checkout')->afterSuccessOrder($responseParams);
            //Mage::getSingleton('core/session')->addSuccess($responseParams['message']);
            $this->_redirect('checkout/onepage/success');
            return;
        }
        else
        {
            Mage::getSingleton('core/session')->addError(Mage::helper('core')->__('Trasaction Failed'));
            $this->_redirect('checkout/cart');
            return;
        }
    }
}
