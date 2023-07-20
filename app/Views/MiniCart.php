<?php

namespace WowCode\Views;

use WowCode\Controllers\WooController as WooController;
use WowCode\Misc\Helper as Helper;

if ( ! defined( 'ABSPATH' ) ) exit;

class MiniCart {

    function __construct() {

        add_action( 'astra_body_top', [ $this, 'renderMinicartWrapper' ] );
        add_action( 'wow-after-custom-menu', [ $this, 'renderCustomMenuIcon' ], 9999 );

        // ajax function for update minicart
        add_action( 'wp_ajax_nopriv_wow_update_minicart', [ $this, 'updateMiniCart' ] );
        add_action( 'wp_ajax_wow_wow_update_minicart', [ $this, 'updateMiniCart' ] );

    }

    public function updateMiniCart() {

        //error_log(print_r($_POST,true));

        $values = array();
        parse_str($_POST['data'], $values);

        //error_log(print_r($values,true));

        $cart = $values['cart'];
        foreach ( $cart as $cart_key => $cart_value ){
            WC()->cart->set_quantity( $cart_key, $cart_value['qty'], false );
            WC()->cart->calculate_totals();
        }

        ob_start();
        $this->miniCartList( true );
        $mini_cart_list = ob_get_clean();

        ob_start();
        $this->renderMiniCartTotals();
        $mini_cart_totals = ob_get_clean();

        wp_send_json( [
            'renderContent' => $mini_cart_list,
            'cartItems' => WC()->cart->get_cart_contents_count(),
            'miniCartTotals' => $mini_cart_totals
            ] );
        
    }

    public function renderMinicartWrapper() {

        if( is_checkout() ) return;

        ob_start();
        $packing_product_id = get_field('minicart_additional_packing', 'option');
        $packing_product = wc_get_product( $packing_product_id );

        ?>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCart" aria-labelledby="offcanvasCartLabel">
                <div class="offcanvas-body">
                    <?php
                        $this->miniCartList();
                    ?>
                    
                        <div class="suggest">
                            <div class="suggest__title">Te rzeczy mogą Ci się spodobać:</div>
                            <div class="suggest__list">
                                <?php

                                // Check rows existexists.
                                if( have_rows('minicart_extras', 'option') ):

                                    // Loop through rows.
                                    while( have_rows('minicart_extras', 'option') ) : the_row();

                                        // Load sub field value.
                                        $product_id = get_sub_field('product');
                                        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' );
                                        $product = wc_get_product( $product_id );

                                    ?>
                                        <div class="suggest__list-item">
                                            <a href="" class="card" target="_blank">
                                                <div class="card__thumb">
                                                    <img src="<?php  echo $image[0]; ?>" class="img-fluid" 
                                                        width="625" height="821" alt="">
                                                </div>
                                                <div class="card__body">
                                                    <div class="card__title">
                                                        <?php echo $product->get_name(); ?>
                                                    </div>
                                                    <div class="amount">
                                                        <?php 
                                                        if( $product->get_sale_price() ) {
                                                            ?>
                                                            <div class="amount__current">
                                                                <?php echo $product->get_sale_price() . ' zł'; ?> 
                                                            </div>
                                                            <div class="amount__old">
                                                                <?php echo $product->get_regular_price() . ' zł'; ?> 
                                                            </div>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <div class="amount__current">
                                                                <?php echo $product->get_regular_price() . ' zł'; ?> 
                                                            </div>
                                                            <?php
                                                        } 
                                                        ?>
                                                    </div>
                                                </div>
                                            </a>
                                            <div class="suggest__action">
                                                <a href="<?php echo get_site_url() . '/shop/?add-to-cart=' . $product_id ?>" class="wow-button button_sm"><?php echo __( 'Dodaj', 'astra-child'); ?></a>
                                            </div>
                                        </div>
                                    <?php
                                // End loop.
                                endwhile;

                                // Do something...
                                endif;

                                ?>
                            </div>
                        </div>
                        <div class="addition">
                            <div class="addition__thumb">
                                <img src="<?php echo ASSETS . '/images/gift.png' ?>" class="img-fluid" 
                                    width="66" height="46" alt="">
                            </div>
                            <div class="addition__body">
                                <div class="addition__title">
                                    Opakowanie prezentowe
                                </div>
                                <div class="amount">
                                    <div class="amount__current">
                                        <?php echo $packing_product->get_price() . ' zł'; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="addition__action">
                                <a href="<?php echo get_site_url() . '/shop/?add-to-cart=' . $packing_product_id ?>" class="wow-button button_sm wow-button_primary">Dodaj</a>
                            </div>
                        </div>
                
                    <?php
                        $this->renderMiniCartTotals();
                    ?>
                 </div>
            </div>
        <?php
        ob_get_contents();
    }

    public function renderMiniCartTotals() {

        $totals = Helper::calculateCheckoutTotals();
        $checkout_page_id = wc_get_page_id( 'checkout' );
        $checkout_page_url = $checkout_page_id ? get_permalink( $checkout_page_id ) : '';

        ?>
            <div class="offcanvas__cart-summary">
                <table class="wow-table-summary">
                    <tr>
                        <th class="pe-4">
                            Suma
                        </th>
                        <td>
                            <strong class="wow-table-summary-amount"><?php echo $totals['regular']. ' zł'; ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <th class="pe-4">
                            Dostawa
                        </th>
                        <td>
                            <strong class="wow-table-summary-amount"><?php echo WC()->cart->get_cart_shipping_total(); ?></strong>
                        </td>
                    </tr>
                    <tr class="text-primary">
                        <th class="pe-4 fw-bold">
                            Rabat
                        </th>
                        <td>
                            <strong class="wow-table-summary-amount wow-totals-discount">-<?php echo $totals['discount'] . ' zł'; ?></strong>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="offcanvas__cart-foot">
                <div class="summary">
                    Razem do zapłaty <strong class="summary-amount ms-2"><?php echo WC()->cart->get_total(); ?></strong>
                </div>
                <div class="offcanvas__cart-action">
                    <a href="<?php echo $checkout_page_url ?>" type="submit" class="wow-button wow-button_primary button_lg">
                        <svg class="svg svg-cart-2" width="20" height="22" viewBox="0 0 159 176">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M159,38c0,39,0,78,0,117c-0.318,0.305-0.907,0.603-0.916,0.916  c-0.289,10.328-12.154,20.301-21.965,19.134c-37.746,0.001-75.493,0.001-113.24,0.001c-2.057-0.437-4.101-0.109-6.273-0.702
    C7.866,171.967,3.426,165.581,0,158c0-40.333,0-80.667,0-121c5.125-6.938,9.894-14.18,15.483-20.719C20.338,10.602,23.06,2.759,31,0
    c32.333,0,64.666,0,97,0c3.086,1.172,5.439,3.425,7.322,5.918C143.328,16.526,151.126,27.292,159,38z M79.723,47.968
    c-20.323,0-40.646,0.073-60.966-0.078c-3.096-0.022-3.842,0.759-3.831,3.843c0.122,33.148,0.065,66.297,0.085,99.445
    c0.004,6.147,2.779,8.833,9.005,8.834c36.646,0.007,73.293,0.006,109.939,0.001c6.372-0.001,10.031-3.563,10.035-9.876
    c0.019-32.648-0.055-65.298,0.099-97.946c0.017-3.573-0.966-4.337-4.399-4.308C119.701,48.056,99.711,47.968,79.723,47.968z
    M22.879,32.044c2.21-0.704,4.239,0.006,6.159,0.002c25.138-0.047,50.276-0.063,75.414-0.015c9.784,0.018,19.569,0.184,29.351,0.398
    c0.687,0.016,1.188,0.201,1.637-0.398c0.812-1.084-0.414-1.332-0.811-1.865c-2.516-3.382-5.285-6.595-7.561-10.127
    c-1.875-2.91-4.018-4.168-7.598-4.149c-26.635,0.143-53.272,0.177-79.907-0.041c-3.526-0.029-5.746,0.985-7.641,3.682
    C28.966,23.741,25.9,27.876,22.879,32.044z M136,176c-0.029-0.325,0.011-0.642,0.119-0.95c-37.746,0.001-75.493,0.001-113.24,0.001
    c0.107,0.308,0.148,0.624,0.121,0.949C60.667,176,98.334,176,136,176z M47.083,94.945c7.787,11.214,19.215,16.52,32.44,16.674
    c8.853,0.103,17.403-2.868,24.576-8.646c9.884-7.962,14.278-18.642,14.806-30.99c0.173-4.05-4.08-7.805-7.615-7.693
    c-4.471,0.14-7.215,3.008-7.221,8.013c-0.002,1.881-0.629,3.49-1.063,5.208c-2.708,10.713-11.842,20.399-27.604,18.468
    c-9.794-1.201-20.536-11.016-20.433-22.921c0.031-3.584-1.254-6.85-4.947-7.968c-6.1-1.847-11.044,1.552-10.158,8.13
    C40.079,81.917,42.819,88.806,47.083,94.945z"/>
                        </svg>
                        Przejdź do&nbsp;kasy
                    </a>
                </div>
            </div>            
        <?php
    }

    public function miniCartList( $ajax_request = false ) {
        ?>
            <div class="wow-minicart-contents">
                <div class="offcanvas-header">
                    <div class="offcanvas-header__row">
                        <h5 class="heading mb-0" id="offcanvasCartLabel">Twój koszyk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                            <svg class="svg" width="20" height="20">
                                <use xlink:href="spritemap.svg#sprite-cancel-alt"></use>
                            </svg>
                        </button>
                    </div>
                    <div class="delivery-bar">
                        <div class="delivery-bar__title">Brakuje ci <strong><?php echo Helper::calculateHowManyLeftToFreeShipping() ?></strong> do <strong>DARMOWEJ</strong> 🎉 dostawy!</div>
                        <div class="delivery-bar__body">
                            <div class="delivery-bar__progress">
                                <div class="progress">
                                    <div class="progress__value" style="width: <?php echo Helper::calculateFreeShipping(); ?>%"></div>
                                </div>
                            </div>
                            <svg class="svg svg-delivery" width="39" height="31">
                                <use xlink:href="spritemap.svg#sprite-delivery"></use>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="offcanvas-body offcanvas-body-cart">
                    <div class="offcanvas__cart-list">
                        <form action="" class="wow-mini-cart-form">
                            <?php
                                foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                                    $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                                    $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                                    $product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                                    $this->miniCartItem($_product, $product_permalink, $product_price, $cart_item, $cart_item_key);
                                }
                            ?>
                        </form>
                    </div>
                </div>
            </div>
        <?php
    }

    public function miniCartItem($_product, $product_permalink, $product_price, $cart_item, $cart_item_key) {

        global $woocommerce;

        ?>
            <div class="wow-card-row">
                <div class="card-row__thumb">
                    <a href="#">
                        <img src="<?php echo wp_get_attachment_url($_product->get_image_id()); ?>" class="img-fluid" 
                            width="625" height="821" alt="">
                    </a>
                    <div class="label">Promocja!</div>
                </div>
                <div class="card-row__wrap">
                    <div class="card-row__body">
                        <a href="#" class="card__title">
                            <?php echo $_product->get_name(); ?>
                        </a>
                        <div class="amount">
                            <?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?>
                        </div>
                    </div>
                    <div class="card-row__action">
                        <div class="wow-quantity">
                            <button type="button" class="quantity__btn" data-type="minus">
                                -
                            </button>
                            <input type="number" class="wow-quantity__field" step="1" min="1" name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]" value="<?php echo esc_attr( $cart_item['quantity'] ); ?>" data-product-qty />
                            <button type="button" class="quantity__btn" data-type="plus">
                                +
                            </button>
                        </div>
                    </div>
                    <div class="card-row__cancel">
                        <a href="#" class="button-icon">
                            <span class="visually-hidden">Usuń</span>
                            <svg class="svg" width="34" height="34">
                                <use xlink:href="spritemap.svg#sprite-cancel"></use>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        <?php
    }

    public function renderCustomMenuIcon() {

        ob_start();
        ?>
        <div class="navbar__action">
            <?php 
                if( !is_checkout() ) {
                    ?>
                        <a class="button-icon" data-bs-toggle="offcanvas" href="#offcanvasCart" role="button" aria-controls="offcanvasCart">
                            <svg class="svg" width="29" height="36" viewBox="256.278 0 447.443 560">
                                <path d="M671.497,122.178c-0.58-7.187-6.607-12.635-13.91-12.635h-67.929C589.542,49.149,540.394,0,480,0
                                S370.458,49.149,370.341,109.542h-67.928c-7.187,0-13.215,5.448-13.91,12.635l-32.225,363.171c0,0.464,0,0.812,0,1.275
                                c0,40.455,37.209,73.376,82.881,73.376h281.681c45.671,0,82.881-32.921,82.881-73.376c0-0.464,0-0.812,0-1.275L671.497,122.178z
                                M480,27.82c45.092,0,81.722,36.63,81.838,81.722H398.162C398.278,64.45,434.908,27.82,480,27.82z M620.841,532.063H339.16
                                c-30.139,0-54.713-20.054-55.061-44.976l31.066-349.725h55.177v48.802c0,7.65,6.26,13.91,13.91,13.91
                                c7.651,0,13.911-6.26,13.911-13.91v-48.802h163.676v48.802c0,7.65,6.26,13.91,13.91,13.91s13.91-6.26,13.91-13.91v-48.802h55.177
                                l31.066,349.84C675.554,512.01,650.979,532.063,620.841,532.063z"/>
                            </svg>
                            <span class="count wow-minicart-items-counter"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                        </a>
                    <?php
                }
            ?>
            <a href="#" class="button-icon button-icon_nav">
                <svg>
                    <use xlink:href="#menu"></use>
                    <use xlink:href="#menu"></use>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 56" id="menu">
                        <path d="M48.33,45.6H18a14.17,14.17,0,0,1,0-28.34H78.86a17.37,17.37,0,0,1,0,34.74H42.33l-21-21.26L47.75,4"></path>
                    </symbol>
                </svg>
            </a>
        </div>
    
        <?php

        ob_get_contents();
    }

}
