<?php

class ND_Alphabank_Model_System_Config_Source_Language
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'el', 'label'=>Mage::helper('alphabank')->__('Greek')),
            array('value' => 'en', 'label'=>Mage::helper('alphabank')->__('English')),
            array('value' => 'fr', 'label'=>Mage::helper('alphabank')->__('French')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'el' => Mage::helper('alphabank')->__('Greek'),
            'en' => Mage::helper('alphabank')->__('English'),
            'fr' => Mage::helper('alphabank')->__('French'),
        );
    }

}
