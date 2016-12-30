<?php 

class ND_Alphabank_Block_Payment_Info extends Mage_Payment_Block_Info // Mage_Payment_Block_Info_Cc
{
      /**
     * Prepare Alphabank-specific payment information
     *
     * @param Varien_Object|array $transport
     * return Varien_Object
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $info = $this->getInfo();
        $transport->addData(array(
            Mage::helper('payment')->__('Number of Installments') => $info->getAdditionalInformation('installments'),
        ));
        return $transport;
    }
}
