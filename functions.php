<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );
define('ASSETS', get_stylesheet_directory_uri() . '/src/assets');

require_once __DIR__ . '/vendor/autoload.php';

use WowCode\Controllers\WooController;
use WowCode\Controllers\HeaderController;

new WooController;
new HeaderController;

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );

/**
 * Enqueue scripts and styles.
 */
function pankobus_scripts() {

    wp_enqueue_style( 'wow-main-css', ASSETS . '/css/style-main.css' );

    if ( is_page_template( 'aksjomaty-milosci.php' )  ) {
        //wp_enqueue_script( 'jquery', ASSETS . '/js/jquery-3.6.1.min.js', array(), '', true );
        wp_enqueue_script('aksjomaty-js', ASSETS . '/js/main.bundle.aksjomaty.js' );
        //wp_enqueue_style( 'aksjomaty-css', ASSETS . '/css/style-aksjomaty.css' );
        wp_enqueue_style( 'landing-css', ASSETS . '/css/style-landing.css' );
        // wp_enqueue_style( 'slick-css-theme', ASSETS . '/css/slick-theme.css' );
    }

    wp_enqueue_script( 'new-design-script', ASSETS . '/js/bundle-new-design.min.js' );
    wp_enqueue_script( 'wow-modern-checkout', ASSETS . '/js/modern-checkout.min.js' );
    wp_enqueue_script( 'wow-modern-two-step-checkout', ASSETS . '/js/two-step-modern-checkout.min.js' );

    wp_enqueue_script( 'wow-main-script', ASSETS . '/js/wow-main-script.js' );
    wp_localize_script( 
        'wow-main-script', 
        'wow_ajax', 
        [
            'url'   => admin_url( 'admin-ajax.php' ),
            'checkout_nonce' => wp_create_nonce( "wow_update_checkout_nonce" ),
        ]
    );

}
add_action( 'wp_enqueue_scripts', 'pankobus_scripts' );

function pankobus_checkout_scripts() {

        wp_enqueue_style( 'new-design-css', ASSETS . '/css/style-new-design.css' );

 
}

add_action( 'wp_enqueue_scripts', 'pankobus_checkout_scripts' );

function project_dequeue_unnecessary_styles() {

    if ( is_page_template( 'aksjomaty-milosci.php' ) || is_checkout() ) {

    }

}
add_action( 'wp_print_styles', 'project_dequeue_unnecessary_styles', 11 );

if ( ! function_exists( 'woocommerce_order_review_summary' ) ) {

	/**
	 * Output the Order review table for the checkout.
	 *
	 * @param bool $deprecated Deprecated param.
	 */
	function woocommerce_order_review_summary( $deprecated = false ) {
		wc_get_template(
			'checkout/review-order-summary.php',
			array(
				'checkout' => WC()->checkout(),
			)
		);
	}
}

add_action( 'woocommerce_checkout_order_review_summary', 'woocommerce_order_review_summary', 10 );

add_filter( 'woocommerce_checkout_fields' , 'wow_remove_checkout_fields' ); 

function wow_remove_checkout_fields( $fields ) { 

    unset($fields['billing']['billing_company']); 
    unset($fields['billing']['billing_address_2']); 
    unset($fields['billing']['billing_email']); 
    unset($fields['billing']['billing_country']); 
    return $fields; 

}

// Hook in
add_filter( 'woocommerce_checkout_fields' , 'add_checkout_fields' );

// Our hooked in function – $fields is passed via the filter!
function add_checkout_fields( $fields ) {

     $fields['billing']['billing_local'] = array(
        'label'     => __('Lokal', 'woocommerce'),
        'placeholder'   => _x('Lokal', 'placeholder', 'woocommerce'),
        'required'  => false,
        'class'     => array('col-6 col-lg-2'),
        'clear'     => true
     );

     $fields['billing']['billing_building_no'] = array(
        'label'     => __('Numer', 'woocommerce'),
        'placeholder'   => _x('Numer*', 'placeholder', 'woocommerce'),
        'required'  => false,
        'class'     => array('col-6 col-lg-2'),
        'clear'     => true
     );
     $fields['billing']['billing_first_name']= [
        'placeholder'   => _x('Imię*', 'placeholder', 'woocommerce'),
        'class' => ['col-md-6']
     ];
     $fields['billing']['billing_last_name']= [
        'placeholder'   => _x('Nazwisko*', 'placeholder', 'woocommerce'),
        'class' => ['col-md-6']
     ];
     $fields['billing']['billing_country'] = array(
        'type' => 'text',
        'label'     => __('Kraj', 'astra-child'),
        'placeholder'   => _x('Kraj', 'placeholder', 'astra-child'),
        'required'  => false,
        'class'     => array('form-row-wide'),
        'clear'     => true
     );
     $fields['billing']['billing_address_1']= [
        'class'   => ['col-md-12 col-lg-8'],
        'label'     => __('Ulica', 'astra-child'),
        'placeholder'   => _x('Ulica', 'placeholder', 'astra-child'),
     ];
     $fields['billing']['billing_postcode']= [
        'placeholder'   => _x('Kod pocztowy*', 'placeholder', 'woocommerce'),
        'class' => [ 'col-sm-5 col-lg-4' ]
     ];
     $fields['billing']['billing_city']= [
        'placeholder'   => _x('Miejscowość*', 'placeholder', 'woocommerce'),
        'class' => [ 'col-sm-7 col-lg-8' ]
     ];
     $fields['billing']['billing_phone']= [
        'placeholder'   => _x('Telefon*', 'placeholder', 'woocommerce'),
     ];
	 $fields['order']['order_comments']['label'] = __('Komentarz do zamówienia', 'astra-child');
	 $fields['order']['order_comments']['label_class'] = ['test'];
	 $fields['order']['order_comments']['class'] = [ 'woo-order-comments' ];
	 $fields['order']['order_comments']['placeholder'] = __('Uwagi do zamówienia, np. informacje o dostarczeniu przesyłki, prośba o wystawienie FV na firmę (wpisać NIP i adres)', 'astra-child');


     $fields[ 'billing' ][ 'billing_first_name' ][ 'priority' ] = 10;
     $fields[ 'billing' ][ 'billing_last_name' ][ 'priority' ] = 20;
     $fields[ 'billing' ][ 'billing_country' ][ 'priority' ] = 30;
     $fields[ 'billing' ][ 'billing_address_1' ][ 'priority' ] = 40;
     $fields[ 'billing' ][ 'billing_building_no' ][ 'priority' ] = 50;
     $fields[ 'billing' ][ 'billing_local' ][ 'priority' ] = 60;
     $fields[ 'billing' ][ 'billing_postcode' ][ 'priority' ] = 70;
     $fields[ 'billing' ][ 'billing_city' ][ 'priority' ] = 80;

     return $fields;
}

/**
 * Display field value on the order edit page
 */
 
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );

function my_custom_checkout_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('Lokal').':</strong> ' . get_post_meta( $order->get_id(), '_billing_local', true ) . '</p>';
    echo '<p><strong>'.__('Kraj').':</strong> ' . get_post_meta( $order->get_id(), '_billing_country', true ) . '</p>';
    echo '<p><strong>'.__('Numer').':</strong> ' . get_post_meta( $order->get_id(), '_billing_building_no', true ) . '</p>';
}

function uwc_new_address_one_placeholder( $fields ) {
    $fields['address_1']['placeholder'] = _x('Ulica*', 'placeholder', 'woocommerce');

    return $fields;
}
add_filter( 'woocommerce_default_address_fields', 'uwc_new_address_one_placeholder' );

add_filter( 'wbu_qtybtn_plus', function() {
    return '<a href="" class="wbu-qty-button wbu-btn-inc quantity__btn">+</a>';
} );

add_filter( 'wbu_qtybtn_minus', function() {
    return '<a href="" class="wbu-qty-button wbu-btn-sub quantity__btn">-</a>';
} );

add_filter( 'woocommerce_quantity_input_classes', function( $classes ) {

    array_push( $classes, 'quantity__field' );

    return $classes;
} );

if( function_exists('acf_add_options_page') ) {
    
    acf_add_options_page(array(
        'page_title'    => 'General Settings',
        'menu_title'    => 'General Settings',
        'menu_slug'     => 'general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
    
}

function cc_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
  }
  add_filter('upload_mimes', 'cc_mime_types');

  add_filter( 'astra_apply_flex_based_css', '__return_false' );

function inspect_styles() {
    
    global $wp_styles;
    if( is_front_page() || is_product() ) {
        wp_deregister_style( 'astra-theme-css' );
        wp_deregister_style( 'global-styles' );
        wp_deregister_style( 'woocommerce-layout' );        
    }

    if( is_checkout() ) {
        wp_deregister_style( 'woocommerce-layout' );   
        wp_deregister_style( 'global-styles' );
        wp_deregister_style( 'astra-addon-css' );
        wp_deregister_style( 'woocommerce-general' );
        wp_deregister_style( 'woocommerce-inline' );
        // wp_deregister_style( 'astra-theme-css' );
    }

}
add_action( 'wp_print_styles', 'inspect_styles' );

add_filter('woocommerce_checkout_fields', 'addBootstrapToCheckoutFields',999 );
function addBootstrapToCheckoutFields($fields) {
    foreach ($fields as &$fieldset) {
        foreach ($fieldset as &$field) {

            //if( !$field['type'] == 'textarea' ) {

                $field['class'][] = 'wow-form-row'; 
                $field['input_class'][] = 'form-control';
                $field['label_class'][] = 'wow-form-label';

            //}
        }
 
    }
    return $fields;
}

add_action( 'woocommerce_form_field', function( $field, $key, $args, $value ) {
    $field = preg_replace( '/<span class="woocommerce-input-wrapper">/', '', $field );
    // $field = preg_replace( '/<p/', '<div', $field );
    $field = preg_replace( '/<\/span>/', '', $field );
    $field = preg_replace( '/screen-reader-text/', 'wow-form-label', $field );
    //$field = preg_replace( '/screen-reader-text/', 'form-label', $field );
    // $field = preg_replace( '/<\/p>/', '</div>', $field );
    error_log(print_r($field,true));
    return $field;
}, 10, 4 ); 