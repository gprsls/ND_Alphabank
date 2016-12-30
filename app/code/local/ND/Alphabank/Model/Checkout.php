<?php

class ND_Alphabank_Model_Checkout extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'alphabank_checkout';

    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    protected $_formBlockType = 'alphabank/checkout_form';
    protected $_paymentMethod = 'checkout';
    protected $_infoBlockType = 'alphabank/payment_info';

    protected $_order;
    
    protected $_paymentUrl = '';

    public function getOrder()
    {
        if (!$this->_order) {
            $paymentInfo = $this->getInfoInstance();
            $this->_order = Mage::getModel('sales/order')
                            ->loadByIncrementId($paymentInfo->getOrder()->getRealOrderId());
        }
        return $this->_order;
    }
    
    public function getMerchantId()
    {
        $merchant_id = Mage::getStoreConfig('payment/' . $this->getCode() . '/merchant_id');            
        return $merchant_id;
    }

    public function getSecretKey()
    {
        $secret_key = Mage::getStoreConfig('payment/' . $this->getCode() . '/secret_key');        
        return $secret_key;            
    }
    
    public function getTransactionType()
    {
        $transaction_type = Mage::getStoreConfig('payment/' . $this->getCode() . '/transaction_type');        
        return $transaction_type;            
    }

    public function getLanguage()
    {
        $language = Mage::getStoreConfig('payment/' . $this->getCode() . '/gateway_language');
        return $language;
    }
    
    public function getInstallmentperiod()
    {
        $paymentInfo = $this->getInfoInstance();
        $installmentperiod = '';
        $installments = $paymentInfo->getAdditionalInformation('installments');
        if ($installments >= 2){
            $installmentperiod = $installments;
        } 
        return $installmentperiod;
    }

    public function getInstallmentOffSet()
    {
        $installmentsoffset = '';
        if ($this->getInstallmentperiod() >= 2) {
            $installmentsoffset = Mage::getStoreConfig('payment/' . $this->getCode() . '/installmentsoffset');
            if(!$installmentsoffset || $installmentsoffset < 1) {
                $installmentsoffset = '0';
            }
        }
        return $installmentsoffset;
    }
    
    public function validate()
    {           
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
            $currency_code = $paymentInfo->getOrder()->getBaseCurrencyCode();
        } else {
            $currency_code = $paymentInfo->getQuote()->getBaseCurrencyCode();
        }     
//        if($paymentInfo->getLang()!='') {
//            $paymentInfo->setAdditionalInformation('lang',$paymentInfo->getLang());
//            //Mage::throwException($paymentInfo->getLang());
//        }
        return true;
    }

    public function getOrderPlaceRedirectUrl()
    {
        $url = Mage::getUrl('alphabank/' . $this->_paymentMethod . '/redirect');
        if(!$url) {
            $url = 'https://alpha.test.modirum.com/vpos/shophandlermpi';
        }
        return $url;
    }
    
    public function isTestMode()
    {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/test_mode'); 
    }
    
    public function getCheckoutUrl()
    {
        if($this->isTestMode()) {
            $url = Mage::getStoreConfig('payment/' . $this->getCode() . '/api_test_url');        
            if(!$url) {
                $url = 'https://alpha.test.modirum.com/vpos/shophandlermpi';
            }
        }
        else {                    
            $url = Mage::getStoreConfig('payment/' . $this->getCode() . '/api_url');        
            if(!$url) {
                $url = 'https://alpha.modirum.com/vpos/shophandlermpi';
            }
        }
        return $url;
    }
    
    public function getFormFields()
    {        
        $form_data = "";
        $form_data_array = array();
        $fieldsArr = array();              
        $paymentInfo = $this->getInfoInstance();
        $shippingAddress = $this->getOrder()->getShippingAddress();
        $billingAddress = $this->getOrder()->getBillingAddress();    
        $additional_information = $paymentInfo->getAdditionalInformation();
        $fieldsArr['mid']  = $this->getMerchantId();                                    
            $form_data_array[1] = $fieldsArr['mid'] ;                                                   //Req
        $fieldsArr['lang'] = $this->getLanguage();  
            $form_data_array[2] = $fieldsArr['lang'];                                                   //Opt
        $fieldsArr['deviceCategory'] = "";                         
            $form_data_array[3] = $fieldsArr['deviceCategory'];                                         //Opt
        $fieldsArr['orderid'] = $paymentInfo->getOrder()->getRealOrderId();                            
            $form_data_array[4] = $fieldsArr['orderid'];                                                //Req
        $fieldsArr['orderDesc'] = 'Order by '.$billingAddress->getFirstname().' '.$billingAddress->getLastname();
            $form_data_array[5] = $fieldsArr['orderDesc'];                                              //Opt
        $fieldsArr['orderAmount'] = $this->getOrder()->getGrandTotal();                    
            $form_data_array[6] = $fieldsArr['orderAmount'];                                            //Req
        $fieldsArr['currency'] = $paymentInfo->getOrder()->getOrderCurrencyCode();                        
            $form_data_array[7] = $fieldsArr['currency'];                                               //Req
        $fieldsArr['payerEmail'] = $billingAddress->getEmail();                            
            $form_data_array[8] = $fieldsArr['payerEmail'];                                             //Req
        $fieldsArr['payerPhone'] = $billingAddress->getTelephone();                            
            $form_data_array[9] = $fieldsArr['payerPhone'];                                             //Opt        
        $fieldsArr['billCountry'] = $billingAddress->getCountryId();                    
            $form_data_array[10] = $fieldsArr['billCountry'];                                           //Opt
        $fieldsArr['billState'] = ($billingAddress->getRegionId()!='')?$billingAddress->getRegionId():$billingAddress->getRegion();
            $form_data_array[11] = $fieldsArr['billState'];                                             //Opt
        $fieldsArr['billZip'] = $billingAddress->getPostcode();                            
            $form_data_array[12] = $fieldsArr['billZip'];                                               //Opt
        $fieldsArr['billCity'] = $billingAddress->getCity();                        
            $form_data_array[13] = $fieldsArr['billCity'];                                              //Opt
        $fieldsArr['billAddress'] = (count($billingAddress->getStreet()))?implode(", ",$billingAddress->getStreet()):'';
            $form_data_array[14] = $fieldsArr['billAddress'];                                           //Opt
        $fieldsArr['weight'] = '';                            
            $form_data_array[15] = $fieldsArr['weight'];                                                //Opt
        $fieldsArr['dimensions'] = '';                        
            $form_data_array[16] = $fieldsArr['dimensions'];                                            //Opt
        $fieldsArr['shipCountry'] = $shippingAddress->getCountryId();                    
            $form_data_array[17] = $fieldsArr['shipCountry'];                                           //Opt
        $fieldsArr['shipState'] = ($shippingAddress->getRegionId()!='')?$shippingAddress->getRegionId():$shippingAddress->getRegion();
            $form_data_array[18] = $fieldsArr['shipState'];                                             //Opt
        $fieldsArr['shipZip'] = $shippingAddress->getPostcode();                            
            $form_data_array[19] = $fieldsArr['shipZip'];                                               //Opt
        $fieldsArr['shipCity'] = $shippingAddress->getCity();                        
            $form_data_array[20] = $fieldsArr['shipCity'];                                              //Opt
        $fieldsArr['shipAddress'] = (count($shippingAddress->getStreet()))?implode(", ",$shippingAddress->getStreet()):''; 
            $form_data_array[21] = $fieldsArr['shipAddress'];                                           //Opt
        $fieldsArr['addFraudScore'] = '';            
            $form_data_array[22] = $fieldsArr['addFraudScore'];                                         //Opt
        $fieldsArr['maxPayRetries'] = '';            
            $form_data_array[23] = $fieldsArr['maxPayRetries'];                                         //Opt
        $fieldsArr['reject3dsU'] = '';                    
            $form_data_array[24] = $fieldsArr['reject3dsU'];                                            //Opt
        $fieldsArr['payMethod'] = '';                        
            $form_data_array[25] = $fieldsArr['payMethod'];                                             //Opt
        $fieldsArr['trType'] = $this->getTransactionType();                            
            $form_data_array[26] = $fieldsArr['trType'];                                                //Opt
        $fieldsArr['extInstallmentoffset'] = $this->getInstallmentOffSet();    
            $form_data_array[27] = $fieldsArr['extInstallmentoffset'];                                  //Opt
        $fieldsArr['extInstallmentperiod'] = $this->getInstallmentperiod();    
            $form_data_array[28] = $fieldsArr['extInstallmentperiod'];                                  //Opt
        $fieldsArr['extRecurringfrequency'] = '';    
            $form_data_array[29] = $fieldsArr['extRecurringfrequency'];                                 //Opt
        $fieldsArr['extRecurringenddate'] = '';
            $form_data_array[30] = $fieldsArr['extRecurringenddate'];                                   //Opt
        $fieldsArr['blockScore'] = '';                    
            $form_data_array[31] = $fieldsArr['blockScore'];                                            //Opt
        $fieldsArr['cssUrl'] = '';                            
            $form_data_array[32] = $fieldsArr['cssUrl'];                                                //Opt
        $fieldsArr['confirmUrl'] = Mage::getUrl('alphabank/' . $this->_paymentMethod . '/response', array('_secure' => true));
            $form_data_array[33] = $fieldsArr['confirmUrl'];                                            //Req
        $fieldsArr['cancelUrl'] = Mage::getUrl('alphabank/' . $this->_paymentMethod . '/response', array('_secure' => true));
            $form_data_array[34] = $fieldsArr['cancelUrl'];                                             //Req
        $fieldsArr['var1'] = '';                                
            $form_data_array[35] = $fieldsArr['var1'];            
        $fieldsArr['var2'] = '';                                
            $form_data_array[36] = $fieldsArr['var2'];            
        $fieldsArr['var3'] = '';                                
            $form_data_array[37] = $fieldsArr['var3'];            
        $fieldsArr['var4'] = '';                                
            $form_data_array[38] = $fieldsArr['var4'];            
        $fieldsArr['var5'] = '';                                
            $form_data_array[39] = $fieldsArr['var5'];            
        $form_secret = $this->getSecretKey();                                    
            $form_data_array[40] = $form_secret;                                                        //Req
        
        $form_data = implode("", $form_data_array);
        
        $digest = base64_encode(sha1($form_data,true));
        
        $fieldsArr['digest'] = $digest;        
        
        $debugData = array(
            'request' => $fieldsArr
        );
        $this->_debug($debugData);   
        
        return $fieldsArr;
    }
    
    /**
     * Get debug flag
     *
     * @return string
     */
    public function getDebug()
    {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/debug');
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }
    
    public function getRedirectBlockType()
    {
        return $this->_redirectBlockType;
    }

    public function assignData($data)
    {
        //Mage::throwException(implode(',',$data));
        
        // Call parent assignData
        $result = parent::assignData($data);

        // Get Mage_Payment_Model_Info instance from quote 
        $info = $this->getInfoInstance();

        // Add some arbitrary post data to the Mage_Payment_Model_Info instance 
        // so it is saved in the DB in the 'additional_information' field
        $info->setAdditionalInformation('installments', $data->getInstallments());
        return $result;
    }
    /**
     * Return payment method type string
     *
     * @return string
     */
    public function getPaymentMethodType()
    {
        return $this->_paymentMethod;
    }
    
    public function afterSuccessOrder($response)
    {
        $debugData = array(
            'response' => $response
        );
        $this->_debug($debugData);
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($response['orderid']);
        $paymentInst = $order->getPayment()->getMethodInstance();        
        $paymentInst->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($response['txId'])
                ->setTransactionId($response['txId'])
                ->setAdditionalInformation(ND_Alphabank_Model_Info::PAYMENT_REFERENCE,$response['paymentRef'])
                ->setAdditionalInformation(ND_Alphabank_Model_Info::PAYMENT_METHOD,$response['payMethod'])
                ->setAdditionalInformation(ND_Alphabank_Model_Info::RISK_SCORE,$response['riskScore'])
                ->setAdditionalInformation(ND_Alphabank_Model_Info::MESSAGE,$response['message']);
        
        $order->sendNewOrderEmail();                
        if ($order->canInvoice()) {
            $invoice = $order->prepareInvoice();
            
            $invoice->register()->capture();
            Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
        }
        $transaction = Mage::getModel('sales/order_payment_transaction');
        $transaction->setTxnId($response['txId']);
        $order->getPayment()->setAdditionalInformation(ND_Alphabank_Model_Info::PAYMENT_REFERENCE,$response['paymentRef'])
                            ->setAdditionalInformation(ND_Alphabank_Model_Info::PAYMENT_METHOD,$response['payMethod'])
                            ->setAdditionalInformation(ND_Alphabank_Model_Info::RISK_SCORE,$response['riskScore'])
                            ->setAdditionalInformation(ND_Alphabank_Model_Info::MESSAGE,$response['message']);
        $transaction->setOrderPaymentObject($order->getPayment())
                    ->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE);
        $transaction->save();
        $order_status = Mage::helper('core')->__('Payment is successful.');
    
        $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PROCESSING, $order_status);
        $order->save();        
    }
}
