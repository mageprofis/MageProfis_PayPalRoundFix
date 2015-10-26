<?php

class MageProfis_PayPalRoundFix_Model_Observer
{

    /**
     * 
     * @mageEvent paypal_prepare_line_items
     * @see Mage_Paypal_Model_Cart::_render
     * 
     * @param Varien_Object $event
     * @return void
     */
    public function onPaypalPreparelineItems($event)
    {
        $cart = $event->getPaypalCart();
        /* @var $cart MageProfis_PayPalRoundFix_Model_Paypal_Cart */
        if ($cart instanceof MageProfis_PayPalRoundFix_Model_Paypal_Cart)
        {
            $cart->calcFix();
        }
    }
}