<?php 

class ND_Alphabank_Block_Payment_Info extends Mage_Payment_Block_Info_Cc
{
    /**
     * Don't show CC type for non-CC methods
     *
     * @return string|null
     */
    public function getCcTypeName()
    {
        return parent::getCcTypeName();
    }

    /**
     * Prepare Alphabank-specific payment information
     *
     * @param Varien_Object|array $transport
     * return Varien_Object
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $payment = $this->getInfo();
        $vpcInfo = Mage::getModel('alphabank/info');
        if (!$this->getIsSecureMode()) {
            $info = $vpcInfo->getPaymentInfo($payment, true);
        } else {
            $info = $vpcInfo->getPublicPaymentInfo($payment, true);
        }
        return $transport->addData($info);
    }
}
