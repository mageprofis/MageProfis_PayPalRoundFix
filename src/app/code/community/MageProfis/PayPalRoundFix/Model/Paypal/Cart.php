<?php

class MageProfis_PayPalRoundFix_Model_Paypal_Cart
extends Mage_Paypal_Model_Cart
{
    /**
     * will be trigger via event (paypal_prepare_line_items)
     * @see MageProfis_PayPalRoundFix_Model_Observer::onPaypalPreparelineItems
     * 
     * @return void
     */
    public function calcFix()
    {
        // check if version is lower than Magento CE 1.9
        if (Mage::getEdition() == Mage::EDITION_COMMUNITY
                && version_compare(Mage::getVersion(), '1.9', '<'))
        {
            return;
        }

        // round all entites
        foreach($this->_totals as $key => $value) {
            $this->_totals[$key] = round($value, 2);
        }

        // calculate grandtotal
        $tmpGrandTotal = round($this->_totals[self::TOTAL_SUBTOTAL]
                + $this->_totals[self::TOTAL_TAX]
                + $this->_totals[self::TOTAL_SHIPPING], 2);
        // calculate discount
        if ($this->_totals[self::TOTAL_DISCOUNT]) {
            $tmpGrandTotal = round($tmpGrandTotal
                    - $this->_totals[self::TOTAL_DISCOUNT], 2);
        }

        // get grandtotal from model
        $grandTotal = round($this->_salesEntity->getBaseGrandTotal(), 2);

        // check if grand total, and calc grand total have the same amount
        if ($tmpGrandTotal != $grandTotal) {
            $diff = round($grandTotal - $tmpGrandTotal, 2);
            $isOneCent = false;
            if (abs($diff) == 0.01) {
                $isOneCent = true;
            }
            // if there is a mismatch of 1 cent,
            // we are using the grandtotal as amount,
            // and clear all other fields!
            if ($isOneCent) {
                $this->_totals[self::TOTAL_SUBTOTAL] = $grandTotal;
                $this->_totals[self::TOTAL_TAX]      = 0;
                $this->_totals[self::TOTAL_SHIPPING] = 0;
                $this->_totals[self::TOTAL_DISCOUNT] = 0;
            }
        }
        return;
    }
}