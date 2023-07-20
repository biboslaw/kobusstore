<?php

namespace WowCode\Controllers;

use WowCode\Views\CustomCheckout as Checkout;
use WowCode\Views\MiniCart as MiniCart;
use WowCode\Views\SingleProduct;

if ( ! defined( 'ABSPATH' ) ) exit;

class WooController {

  public function __construct() {

    add_action( 'woocommerce_before_checkout_form', [$this, 'addCheckoutItemsTable'] );

    // ajax function for update checkout
    add_action( 'wp_ajax_nopriv_wow_update_order_review', [ $this, 'updateCheckout' ] );
    add_action( 'wp_ajax_wow_update_order_review', [ $this, 'updateCheckout' ] );

    // ajax function for update totals
    add_action( 'wp_ajax_nopriv_wow_update_totals', [ $this, 'calculateCheckoutTotals' ] );
    add_action( 'wp_ajax_wow_update_totals', [ $this, 'calculateCheckoutTotals' ] );

    // ajax function for appy coupon
    add_action( 'wp_ajax_nopriv_wow_update_coupon', [ $this, 'applyCoupon' ] );
    add_action( 'wp_ajax_wow_wow_update_coupon', [ $this, 'applyCoupon' ] );

    // ajax function for appy coupon
    add_action( 'wp_ajax_nopriv_set_payment_method', [ $this, 'setPaymentMethod' ] );
    add_action( 'wp_ajax_wow_set_payment_method', [ $this, 'setPaymentMethod' ] );

    // ajax function for appy coupon
    add_action( 'wp_ajax_nopriv_set_shipping_method', [ $this, 'setShippingMethod' ] );
    add_action( 'wp_ajax_wow_set_shipping_method', [ $this, 'setShippingMethod' ] );

    add_action( 'woocommerce_checkout_after_order_review', [ $this, 'woocommerceCheckoutPayment' ] );
    add_action( 'woocommerce_checkout_after_order_review', [ $this, 'woocommerceCheckoutShipping' ] );
    add_action( 'woocommerce_after_checkout_form', [ $this, 'refineOrder' ] );
    add_action( 'woocommerce_after_checkout_form', [ $this, 'checkoutTotals' ] );
    add_action( 'woocommerce_after_checkout_form', [ $this, 'orderExstras' ] );
    add_action( 'woocommerce_after_checkout_form', [ $this, 'beforeFooterSection' ] );

    add_action( 'woocommerce_checkout_billing', array( $this, 'checkout_billing_email_field' ), 9, 1 );

    $this->addCustomSingleProduct();

    $this->addCustomMiniCart();
  
  }

  public function addCheckoutItemsTable() {
      
    $checkout = new Checkout;
    $checkout->renderItemsList();
      
  }

  public function woocommerceCheckoutShipping() {
      
    $checkout = new Checkout;
    $checkout->renderShipping();

  }     

  public function woocommerceCheckoutPayment() {
      
    $checkout = new Checkout;
    $checkout->renderPayment();
      
  }

  public function beforeFooterSection() {
      
    $checkout = new Checkout;
    $checkout->renderBeforeFotterSectiont();
      
  }

  public function checkoutTotals() {
      
    $checkout = new Checkout;
    $checkout->renderTotals();
      
  }

  public function orderExstras() {
      
    $checkout = new Checkout;
    $checkout->renderOrderExstras();
      
  }

  public function refineOrder() {
      
    $checkout = new Checkout;
    $checkout->renderRefineOrder();
      
  }

  public function addCustomMiniCart() {

      $mini_cart = new MiniCart();

  }

  public function addCustomSingleProduct() {

      $mini_cart = new SingleProduct();

  }

  public function updateCheckout() {

    $values = array();
    parse_str($_POST['data'], $values);

    $cart = $values['cart'];
    foreach ( $cart as $cart_key => $cart_value ){
        WC()->cart->set_quantity( $cart_key, $cart_value['qty'], false );
        WC()->cart->calculate_totals();
    }
    
    wp_die();

  }

 public function applyCoupon() {

  $coupon = new \WC_Coupon( $_POST['data']['checkout_coupon'] );

  if ( $coupon->is_valid() ) {
    if ( isset( $_POST['data']['checkout_coupon'] ) && ! WC()->cart->has_discount( $_POST['data']['checkout_coupon'] ) ) {

      WC()->cart->apply_coupon( $_POST['data']['checkout_coupon'] );

      wp_send_json([
        'message' => __( 'Kupon został pomyślnie użyty.', 'astra-child' )
      ]);
      
    } else {

      wp_send_json([
        'message' => __( 'Kupon jest już używany!', 'astra-child' )
      ]);

    }
  } else {
            wp_send_json([
        'message' => __( 'Kupon nie istnieje!', 'astra-child' )
      ]);
  }

    wp_die();

 }

 public function setPaymentMethod() {

    WC()->session->set( 'chosen_payment_method', empty( $_POST['data']['payment_method'] ) ? '' : wc_clean( wp_unslash( $_POST['data']['payment_method'] ) ) );

    // Get checkout payment fragment.
		ob_start();
		woocommerce_checkout_payment();
		$woocommerce_checkout_payment = ob_get_clean();

    // Get messages if reload checkout is not true.
		$reload_checkout = isset( WC()->session->reload_checkout );
		if ( ! $reload_checkout ) {
			$messages = wc_print_notices( true );
		} else {
			$messages = '';
		}

    wp_send_json(
			array(
				'result'    => empty( $messages ) ? 'success' : 'failure',
				'messages'  => $messages,
				'reload'    => $reload_checkout,
				'fragments' => apply_filters(
					'woocommerce_update_order_review_fragments',
					array(
						'.woocommerce-checkout-payment' => $woocommerce_checkout_payment,
					)
				),
			)
		);

 }

 public function setShippingMethod() {

  $chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
  $posted_shipping_methods = isset( $_POST['data']['shipping_method'] ) ? wc_clean( wp_unslash( $_POST['data']['shipping_method'] ) ) : array();

  if ( is_array( $posted_shipping_methods ) ) {
    foreach ( $posted_shipping_methods as $i => $value ) {
      $chosen_shipping_methods[ $i ] = $value;
    }
  }

  WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );

  // Get messages if reload checkout is not true.
  $reload_checkout = isset( WC()->session->reload_checkout );
  if ( ! $reload_checkout ) {
    $messages = wc_print_notices( true );
  } else {
    $messages = '';
  }

  WC()->cart->calculate_shipping();

  // Get checkout payment fragment.
  ob_start();
  woocommerce_checkout_payment();
  $woocommerce_checkout_payment = ob_get_clean();
  wp_send_json(

    array(
      'result'    => empty( $messages ) ? 'success' : 'failure',
      'messages'  => $messages,
      'reload'    => $reload_checkout,
      'fragments' => apply_filters(
        'woocommerce_update_order_review_fragments',
        array(
          '.woocommerce-checkout-payment' => $woocommerce_checkout_payment,
        )
      ),
    )
  );

 }

 public function add_custom_single_product_display() {
  echo 'test galerii';
 }

 		/**
		 * Add Custom Email Field.
		 *
		 * @return void
		 */
		public function checkout_billing_email_field() {
			$lost_password_url  = get_site_url() . '/my-account/lost-password/';
			$current_user_name  = wp_get_current_user()->display_name;
			$current_user_email = wp_get_current_user()->user_email;
			$is_allow_login     = 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder' );

			?>
				<div class="ast-customer-info" id="customer_info">
					<div class="ast-customer-info__notice woocommerce-error"></div>
					<div class="woocommerce-billing-fields-custom">
					<?php
					?>
						<div class="ast-checkout-form-heading">
							<h3>
								<?php echo __( 'Dane kontaktowe', 'astra-child' ); ?>
							</h3>
							<?php 
              error_log(print_r(is_user_logged_in(),true));
              error_log(print_r($is_allow_login,true));
              if ( !is_user_logged_in() && $is_allow_login ) { ?>
								<div class="woocommerce-billing-fields__customer-login-label">
                  <?php 
                    echo '<a href="javascript:" id="ast-customer-login-url" class="wow-link">' . __( 'Masz już konto? Zaloguj się', 'astra-child' ) .  '</a>';
                  ?>
                </div>
							<?php } ?>
						</div>
						<div class="woocommerce-billing-fields__customer-info-wrapper">
						<?php
						if ( ! is_user_logged_in() ) {
							do_action( 'astra_checkout_login_field_before' );
								woocommerce_form_field(
									'billing_email',
									array(
										'type'         => 'email',
										'class'        => array( 'form-row-fill' ),
										'required'     => true,
										'label'        => __( 'E-mail', 'astra-child' ),
										'placeholder'  => __( 'E-mail *', 'astra-child' ),
										'autocomplete' => 'email username',
										'default'      => isset( $_COOKIE['ast_modern_checkout_useremail'] ) ? esc_attr( sanitize_email( $_COOKIE['ast_modern_checkout_useremail'] ) ) : '',
                    'label_class' => ['wow-form-label'],
                    'input_class' => ['form-control'],
                    'class' => 'wow-form-row'
									)
								);

							if ( ASTRA_WITH_EXTENDED_FUNCTIONALITY && 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
								?>
									<div id="ast-customer-login-section" style="display:none">
										<div class="ast-customer-login-inner-wrap">
										<?php
											woocommerce_form_field(
												'billing_password',
												array(
													'type' => 'password',
													'class' => array( 'form-row-fill', 'ast-password-field' ),
													'required' => true,
													'label' => __( 'Hasło', 'astra-child' ),
													'placeholder' => __( 'Hasło', 'astra-child' ),
                          'label_class' => ['wow-form-label'],
                          'input_class' => ['form-control'],
                          'class' => 'wow-form-row'
												)
											);
											do_action( 'astra_checkout_login_field_after' );
										?>
										<div class="ast-customer-login-actions">
											<input type="button" name="ast-customer-login-btn" class="wow-button ast-customer-login-section__login-button" id="ast-customer-login-section__login-button" value="<?php echo esc_attr( __( 'Zaloguj', 'astra-addon' ) ); ?>">
										</div>
									<?php
									do_action( 'astra_checkout_login_after' );
									if ( 'yes' === get_option( 'woocommerce_enable_guest_checkout', false ) ) {
										echo "<p class='ast-login-section-message'>" . esc_html( __( 'Logowanie jest opcjonalne', 'astra-child' ) ) . '</p>';
									}
									?>
										</div>
									</div>
							<?php } ?>
							<?php
							if ( 'yes' === get_option( 'woocommerce_enable_signup_and_login_from_checkout' ) ) {
								?>
									<div class="ast-create-account-section" hidden>
									<?php if ( 'yes' === get_option( 'woocommerce_enable_guest_checkout' ) ) { ?>
											<p class="form-row form-row-wide create-account">
												<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
													<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" type="checkbox" name="createaccount" value="1" /> <span><?php esc_html_e( 'Create an account?', 'astra-addon' ); ?></span>
												</label>
											</p>
										<?php } ?>
										<div class="create-account">
										<?php

										if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) {
											woocommerce_form_field(
												'account_username',
												array(
													'type' => 'text',
													'class' => array( 'form-row-fill' ),
													'id'   => 'account_username',
													'required' => true,
													'label' => __( 'Account username', 'astra-addon' ),
													'placeholder' => __( 'Account username', 'astra-addon' ),
												)
											);
										}
										if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) {
											woocommerce_form_field(
												'account_password',
												array(
													'type' => 'password',
													'id'   => 'account_password',
													'class' => array( 'form-row-fill' ),
													'required' => true,
													'label' => __( 'Create account password', 'astra-addon' ),
													'placeholder' => __( 'Create account password', 'astra-addon' ),
												)
											);
										}
										?>
										</div>
									</div>
							<?php } ?>
							<?php
						} else {
								$welcome_content = sprintf( /* translators: %1$s: username, %2$s emailid */ apply_filters( 'astra_addon_logged_in_customer_info_text', esc_html__( ' Welcome Back %1$s (%2$s)', 'astra-addon' ) ), esc_attr( $current_user_name ), esc_attr( $current_user_email ) );
							?>
								<div class="ast-logged-in-customer-info"> <?php echo esc_html( $welcome_content ); ?>
									<div><input type="hidden" class="ast-email-address" id="billing_email" name="billing_email" value="<?php echo esc_attr( $current_user_email ); ?>"/></div>
								</div>
						<?php } ?>
						</div>
					</div>
				</div>
			<?php
		}

}