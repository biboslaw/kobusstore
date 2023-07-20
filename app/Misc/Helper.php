<?php

namespace WowCode\Misc;

if ( ! defined( 'ABSPATH' ) ) exit;

class Helper {
    public static function calculateCheckoutTotals() {

        $totals = [];
        $regular_price_total = 0;
        $discount_proce_total = 0;
    
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
          $regular_price_total += $cart_item['data']->get_regular_price() * $cart_item['quantity'];
          
          if( $cart_item['data']->get_sale_price() ) {
            $discount_proce_total += ( $cart_item['data']->get_regular_price() - $cart_item['data']->get_sale_price()) * $cart_item['quantity'];
          }
        }
    
        $totals['regular'] = $regular_price_total;
        $totals['discount'] = $discount_proce_total;
        $totals['total'] = WC()->cart->get_total();
        $totals['shipping'] = WC()->cart->get_cart_shipping_total();
    
    
        if( isset( $_POST['data']['checkout_ajax'] ) && $_POST['data']['checkout_ajax'] == 'true' ) {
          wp_send_json( $totals );
        } else {
          return $totals;
        }
    }

    public static function calculateFreeShipping($zone_name = 'Poland') {

        $min_free_shipping = get_field( 'free_shipping_minimum_amount', 'option' );

        $current_total = WC()->cart->get_cart_contents_total();

        return round( ( $current_total * 100 ) / $min_free_shipping, 2);
        
    }

    public static function calculateHowManyLeftToFreeShipping( ) {
        $min_free_shipping = get_field( 'free_shipping_minimum_amount', 'option' );

        $current_total = WC()->cart->get_cart_contents_total();
        return ($min_free_shipping - $current_total) > 0 ? round( $min_free_shipping - $current_total, 2 ) : 0 ;
    }
}
