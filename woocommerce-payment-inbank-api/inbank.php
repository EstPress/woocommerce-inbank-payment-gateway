<?php
/*
Plugin Name: Woocommerce Inbank payment (API)
Description: Adds Inbank payment method through their API.
Version: 1.0
Author: EstPress
Author URI: https://estpress.com
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function woocommerce_payment_inbank_api_addmethod( $methods ) {
    $methods[] = 'WC_Gateway_Inbank_API';
    return $methods;
}
add_action( 'woocommerce_payment_gateways', 'woocommerce_payment_inbank_api_addmethod' );

function woocommerce_payment_inbank_init() {
    require_once( dirname( __FILE__ ) . '/classes/class-wc-gateway-inbank-api.php' );
}
add_action('plugins_loaded', 'woocommerce_payment_inbank_init');

function woocommerce_payment_inbank_api_enqueue_scripts() {
    // if (get_option('woocommerce_inbank_settings')['enableApi'] == 'yes') {
        wp_enqueue_style( 'payment_method_inbank_style', plugins_url( '/assets/css/payment.css', __FILE__ ), array(), NULL, '' );
    // }
}
add_action( 'wp_enqueue_scripts', 'woocommerce_payment_inbank_api_enqueue_scripts' );
