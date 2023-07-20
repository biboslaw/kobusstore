<?php

namespace WowCode\Views;

use WowCode\Controllers\WooController as WooController;
use WowCode\Misc\Helper as Helper;

if ( ! defined( 'ABSPATH' ) ) exit;

class CustomCheckout {

//   public function __construct() {

//     $this->renderItemsList();

//   }


  public function checkoutItem($_product, $product_permalink, $product_price, $cart_item, $cart_item_key) {

    ob_start();

    ?>
        <div data-product-id="<?php echo $_product->get_id() ?>" class="wow-card-row">
            <div class="card-row__thumb">
                <a href="<?php echo $product_permalink; ?>">
                    <img src="<?php echo wp_get_attachment_url($_product->get_image_id()); ?>" class="img-fluid" 
                        width="625" height="821" alt="">
                </a>
                <?php
                   if ( $_product->is_on_sale() ) {
                        ?>
                        <div class="label">Promocja!</div>
                        <?php
                    }
                ?>
            </div>
            <div class="card-row__wrap">
                <div class="card-row__body">
                    <a href="<?php echo $product_permalink; ?>" class="card__title">
                        <?php echo $_product->get_name(); ?>
                    </a>
                    <div class="amount">
                        <?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?>
                    </div>
                </div>
                <div class="card-row__action">
                    <div class="wow-quantity">
                        <button type="button" class="quantity__btn" data-type="minus">-</button>
                        <input type="number" step="1" min="1" value="<?php echo $cart_item['quantity']; ?>" name="quantity" inputmode="numeric" class="wow-quantity__field">
                        <button type="button" class="quantity__btn" data-type="plus">+</button>
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

    return ob_get_contents();

  }

  public function renderItemsList() {

    ob_start();

    ?>
        <div class="wow-container">
            <div class="wow-cart">
                <div class="cart__list">
                    <?php

                        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                            $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                            $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                            $product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                            $this->checkoutItem($_product, $product_permalink, $product_price, $cart_item, $cart_item_key);
                        }
                    ?>
                </div>
            </div>
        </div>
    <?php

    return ob_get_contents();

  }

  public function renderShipping() {

    $packages = WC()->shipping()->get_packages();
    $first    = true;
    $acf_shipping_icons = get_field( 'shipping_metod_icon', 'option' );

    ob_start();
	foreach ( $packages as $i => $package ) {
		$chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';
		$product_names = array();

		if ( count( $packages ) > 1 ) {
			foreach ( $package['contents'] as $item_id => $values ) {
				$product_names[ $item_id ] = $values['data']->get_name() . ' &times;' . $values['quantity'];
			}
			$product_names = apply_filters( 'woocommerce_shipping_package_details_array', $product_names, $package );
		}
     
        ?>
        <div class="cart__delivery section">
            <h3 class="wow-heading heading_top">Sposób dostawy</h3>
            <div class="wow-form">
                <div class="wow-form-row">
                <?php

                foreach ( $package['rates'] as $method ) {
                    ?>
                    <div class="form-check">
                        <input class="wow-form-check-input wow-shipping-input" type="radio" name="delivery-type" value="<?php echo esc_attr( $method->id ); ?>" id="delivery-kurier_<?php echo esc_attr( $method->id ); ?>" <?php echo checked( $method->id, $chosen_method, false ); ?>>
                        <label class="wow-payment-label wow-shipping-label" for="delivery-kurier_<?php echo esc_attr( $method->id ); ?>">
                            <div class="form-check-logo mb-3">
                                <?php
                                    foreach( $acf_shipping_icons as $icon ) {
                                        if( $method->instance_id == $icon['method_id'] ) {
                                            ?>
                                                <img src="<?php echo $icon['icon'] ?>" class="img-fluid" width="162" height="98" alt="Inpost">
                                            <?php
                                        }
                                    }
                                ?>
                                
                            </div>
                           <?php echo wc_cart_totals_shipping_method_label( $method ) ?>
                        </label>
                    </div>
                    <?php
                }
                ?>
                </div>
            </div>
        </div>
        <?php

		$first = false;
	}

    ob_get_contents();
    
  }

  public function renderPayment() {

    $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
    WC()->payment_gateways()->set_current_gateway( $available_gateways );

    ob_start();
    ?>
    <div class="cart__payment section">
        <h3 class="wow-heading heading_top">Metoda płatności</h3>
        <div class="wow-form">
            <div class="wow-form-row wow-payment-on-line">
                <div class="form-check">
                    <input class="wow-form-check-input" type="radio" name="payment-type" id="payment-online" checked>
                    <label class="wow-payment-label" for="payment-online">
                        Płatność on-line
                    </label>
                </div>
                <div class="form-payments">
                    <div>
                        <img src="<?php echo ASSETS . '/images/payment/blik.png' ?>" class="img-fluid" width="100" height="56" alt="Blik">
                    </div>
                    <div>
                        <img src="<?php echo ASSETS . '/images/payment/przelewy24.png' ?>" class="img-fluid" width="150" height="52" alt="Przelewy24">
                    </div>
                    <div>
                        <img src="<?php echo ASSETS . '/images/payment/visa.png' ?>" class="img-fluid" width="149" height="46" alt="Visa">
                    </div>
                    <div>
                        <img src="<?php echo ASSETS . '/images/payment/paypal.png' ?>" class="img-fluid" width="163" height="42" alt="PayPal">
                    </div>
                </div>

                <div class="wow-form-row wow-payment-gateways">
                    <?php
                        $counter = 0;
                        foreach ( $available_gateways as $gateway ) {

                            if( $gateway->id === 'bacs' ) {
                                continue;
                            }
                            ?>
                            <div>
                                <div class="form-check">
                                    <input class="wow-form-check-input wow-payment-check-class wow-single-gateway-<?php echo $counter; ?>" type="radio" name="payment-type-check" id="payment-<?php echo $gateway->id ?>" <?php checked( $gateway->chosen, true ); ?> value="<?php echo $gateway->id ?>">
                                    <label class="wow-payment-label" for="payment-<?php echo $gateway->id ?>">
                                        <?php
                                            echo $gateway->get_title();
                                        ?>
                                    </label>
                                    <div class="form-payments wow-form-payments-image">
                                        <?php
                                            echo $gateway->get_icon();
                                        ?>
                                    </div>

                                </div>
                            </div>
                            <?php
                            $counter++;
                        }
                    ?>
                </div>
            </div>
            <div class="wow-form-row">
                <div class="form-check">
                    <input class="wow-form-check-input wow-payment-check-class" type="radio" name="payment-type" id="payment-bank-transfer" value="bacs">
                    <label  class="wow-payment-label" for="payment-bank-transfer">
                        Przelew tradycyjny na konto
                    </label>
                </div>
            </div>
        </div>
    </div>
    <?php
    ob_get_contents();
  }

  public function renderBeforeFotterSectiont() {
    ob_start();
    ?>
        <section id="kontakt" class="section">
            <div class="container">
                <div class="contact">
                    <div class="avatar">
                        <img src="<?php echo ASSETS . '/images/kobus.jpg' ?>" class="avatar-img" width="256" height="256" alt="">
                    </div>
                    <h3 class="wow-heading">Masz problem <br> z zamówieniem?</h3>
                    <p class="paragraph">
                        Zapraszamy do bezpośredniego kontaktu z nami
                    </p>
                    <a href="mailto:sklep@pankobus.pl" class="contact__mail">
                        sklep@pankobus.pl
                    </a>
                </div>
                
            </div>
        </section>
    <?php
    ob_get_contents();
  }

  public function renderTotals() {

    $woocommerce_cart = WC();
    //$woo_controller = new WooController;
    $totals = Helper::calculateCheckoutTotals();
    
    ob_start();
    ?>
	<div class="cart__action section">
		<h3 class="wow-heading heading_top text-end">
			Podsumowanie koszyka
		</h3>
		<div class="cart__action-row">
			<div class="cart__action-col">
				<div class="cart__action-desc">
					Twoje dane osobowe będą użyte do przetworzenia twojego zamówienia,
					obsługi twojej wizyty na naszej stronie oraz dla innych celów 
					o których mówi nasza <a href="#">polityka prywatności</a>.
				</div>
				<div class="wow-form-row mt-5">
					<div class="form-check">
						<input class="wow-form-check-input wow-terms-checkbox" type="checkbox" name="rule" value="rule" id="rule" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ) ?>>
						<label class="wow-payment-label" for="rule">
							Przeczytałem/am i akceptuję regulamin
						</label>
					</div>
				</div>
			</div>
			<div class="cart__action-col">
				<div class="text-end">
					<table class="table-summary wow-table-totals">
						<tr>
							<th class="pe-4">
								Suma
							</th>
							<td>
								<strong class="table-summary-amount wow-totals-regular"><?php echo $totals['regular']. ' zł'; ?></strong>
							</td>
						</tr>
						<tr>
							<th class="pe-4">
								Dostawa
							</th>
							<td>
								<strong class="table-summary-amount wow-totals-shippment"><?php echo WC()->cart->get_cart_shipping_total(); ?></strong>
							</td>
						</tr>
						<tr class="text-primary">
							<th class="pe-4 fw-bold">
								Rabat
							</th>
							<td>
								<strong class="table-summary-amount wow-totals-discount">-<?php echo $totals['discount'] . ' zł'; ?></strong>
							</td>
						</tr>
					</table>
					<div>
						<div class="summary py-2 mt-3">
							Razem do zapłaty <strong class="summary-amount table-summary-amount wow-totals-total ms-2"><?php echo WC()->cart->get_total(); ?></strong>
						</div>
					</div>
					<button type="submit" class="wow-button wow-button_primary button_lg mt-4 wow-place-order">
						<svg class="svg svg-cart-2" width="20" height="22" viewBox="0 0 159 176" enable-background="new 0 0 159 176" xml:space="preserve">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M159,38c0,39,0,78,0,117c-0.318,0.305-0.907,0.603-0.916,0.916
                        c-0.289,10.328-12.154,20.301-21.965,19.134c-37.746,0.001-75.493,0.001-113.24,0.001c-2.057-0.437-4.101-0.109-6.273-0.702
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
						Zamawiam <br class="d-md-none">z obowiązkiem zapłaty
					</button>
				</div>
			
				<div class="mt-4 pt-2">
					<div class="text-end">
						<button class="wow-link js-toggle-button wow-coupon-btn" data-noscroll="true">
							Posiadasz kod rabatowy? ▾
						</button>
					
						<div class="discount mt-4">
							<div class="discount__field">
								<input type="text" placeholder="Wpisz kod rabatowy..." class="discount__input">
								<span class="wow-coupon-input-info feedback feedback_invalid"></span>
							</div>
							<a href="#" class="wow-apply-coupon wow-button">Użyj</a>
						</div>
				
					</div>
					
				</div>
				
			</div>
		</div>
	</div>
    <?php
    ob_get_contents();
  }

  public function renderOrderExstras() {

    ob_start();
    ?>
    <div class="cart__others section">
        <h3 class="wow-heading heading_top">Warto dodać do zamówienia</h3>
        <div class="swiper js-swiper swiper_products" data-swiper-slides="4">
            <div class="swiper-wrapper">
                <?php

                // Check rows existexists.
                if( have_rows('order_extras', 'option') ):

                    // Loop through rows.
                    while( have_rows('order_extras', 'option') ) : the_row();

                        // Load sub field value.
                        $product_id = get_sub_field('extra_product');
                        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' );
                        $product = wc_get_product( $product_id );

                        ?>

                        <div class="swiper-slide">
                            <a href="<?php echo get_site_url() . '/zamowienie/?add-to-cart=' . $product_id ?>" class="card">
                                <div class="card__thumb">
                                    <img src="<?php  echo $image[0]; ?>" class="img-fluid" 
                                        width="625" height="821" alt="">
                                    <div class="label">Promocja!</div>
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
                        </div>

                        <?php

                    // End loop.
                    endwhile;

                // No value.
                else :
                    // Do something...
                endif;

                ?>
  
                <div class="swiper-slide">
                    <div class="box">
                        <div class="box__icon">
                            <svg class="svg" width="88" height="68">
                                <use xlink:href="spritemap.svg#sprite-delivery"></use>
                            </svg>
                        </div>
                        <div class="box__title">
                            Darmowa wysyłka <br>
                            już od 299 zł
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
    <?php
    ob_get_contents();
  }

  public function renderRefineOrder() {
    ob_start();
    ?>
    <div class="cart-extra-wrap">
        <div class="cart__extra section">
            <h3 class="wow-heading heading_top"><span class="d-block d-md-inline text-primary">Nowość!</span> Uszlachetnij swoje zamówienie</h3>
            <div class="extra-order">
                <?php
                    if( have_rows('refine_products', 'option') ):

                        // Loop through rows.
                        while( have_rows('refine_products', 'option') ) : the_row();

                            // Load sub field value.
                            $product_id = get_sub_field('product');
                            $product = wc_get_product( $product_id );
                            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' );

                            ?>

                            <div class="extra-order__item">
                                <div class="extra">
                                    <div class="extra__body">
                                        <div class="extra__thumb">
                                            <img src="<?php  echo $image[0]; ?>" 
                                        width="58" height="58" alt="">
                                        </div>
                                        <div class="extra__title">
                                            <?php echo $product->get_name(); ?>
                                        </div>
                                        <a href="#" class="extra__link" data-bs-toggle="modal" data-bs-target="#<?php echo $product->get_slug() ?>">Jak wygląda?</a>
                                    </div>
                                    <div class="extra__foot">
                                        <div class="extra__price">
                                            <?php echo $product->get_price() . ' zł'; ?> 
                                        </div>
                                        <a href="<?php echo get_site_url() . '/zamowienie/?add-to-cart=' . $product_id ?>" class="wow-button">Dodaj</a>
                                    </div>
                                </div>
                            </div>

                            <?php

                        // End loop.
                        endwhile;
                    endif;
                ?>
            </div>
        </div>
        <?php
            if( have_rows('refine_products', 'option') ):

                // Loop through rows.
                while( have_rows('refine_products', 'option') ) : the_row();

                    // Load sub field value.
                    $product_id = get_sub_field('product');
                    $product = wc_get_product( $product_id );

                    ?>

                    <div class="modal fade" id="<?php echo $product->get_slug() ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><?php echo $product->get_name(); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&#10006;</button>
                                </div>
                                <div class="modal-body">
                                    <img src="<?php echo get_sub_field('additional_image') ? get_sub_field('additional_image') :  ASSETS . '/images/photo-gift.jpg' ?>" class="img-fluid" alt="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php

                // End loop.
                endwhile;

            // No value.
            else :
                // Do something...
            endif;
        ?>
    </div>
    <?php
    ob_get_contents();
  }

}