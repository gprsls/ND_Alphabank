<?php

class ND_Alphabank_Block_Info extends Mage_Payment_Block_Info_Cc
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('alphabank/info.phtml');
    }

    public function toPdf()
    {
        $this->setTemplate('alphabank/pdf/info.phtml');
        return $this->toHtml();
    }

}
