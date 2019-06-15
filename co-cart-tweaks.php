<?php
/*
 * Plugin Name: CoCart - Tweaks
 * Plugin URI:  https://cocart.xyz
 * Description: Example of using CoCart filters to extend the information sent and returned.
 * Author:      Sébastien Dumont
 * Author URI:  https://sebastiendumont.com
 * Version:     0.0.5
 * Text Domain: co-cart-tweaks
 * Domain Path: /languages/
 *
 * WC requires at least: 3.0.0
 * WC Tweaksed up to: 3.6.4
 *
 * Copyright: © 2019 Sébastien Dumont, (mailme@sebastiendumont.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! class_exists( 'CoCart_Tweaks' ) ) {
	class CoCart_Tweaks {

		/**
		 * Load the plugin.
		 *
		 * @access public
		 */
		public function __construct() {
			// Filters the size of the product image returned.
			//add_filter( 'cocart_item_thumbnail_size', array( $this, 'thumbnail_size' ) );

			// Could be used to send a logged in customer an email once they have emptied their cart.
			//add_action( 'cocart_cart_cleared', array( $this, 'send_customer_email_empty' ) );

			// Could be used to send a logged in customer an email that the item removed is a limited item.
			//add_action( 'cocart_item_removed', array( $this, 'limited_edition' ) );

			// Returns the cart contents without the cart item key as the parent array.
			//add_filter( 'cocart_return_cart_contents', array( $this, 'remove_parent_cart_item_key' ), 0 );
			//add_filter( 'cocart_return_removed_cart_contents', array( $this, 'remove_parent_cart_item_key' ), 0 );

			// Enhances the cart return.
			//add_filter( 'cocart_return_cart_contents', array( $this, 'enhance_cart_return' ), 99 );

			// This filter can be used to return additional product data i.e. sku, weight etc for all items or a specific item.
			//add_filter( 'cocart_cart_contents', array( $this, 'return_product_sku' ), 10, 4 );
			//add_filter( 'cocart_cart_contents', array( $this, 'return_product_weight' ), 15, 4 );

			// Can be used to apply a condition for a specific item should it not be allowed for a customer to add on it's own.
			//add_filter( 'cocart_ok_to_add', array( $this, 'requires_specific_item' ), 10, 3 );

			// Load translation files.
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		} // END __construct()

		/**
		 * Filters the size of the product image returned.
		 *
		 * @access public
		 * @return void
		 */
		public function thumbnail_size() {
			return 'thumbnail';
		}

		/**
		 * Send customer an email once cart has emptied.
		 *
		 * @access public
		 */
		public function send_customer_email_empty() {
			if ( is_user_logged_in() ) {
				$user_id  = get_current_user_id();
				$userdata = get_userdata( $user_id );

				$send_to = $userdata->email;
				$subject = __( 'What happened?', 'co-cart-tweaks' );
				$message = __( 'Why did you empty your cart? Anything we can do to help?', 'co-cart-tweaks' );
				$headers = array(
					'Content-Type: text/html; charset=UTF-8',
					'From: Me Myself <me@example.net>'
				);

				wp_email( $send_to, $subject, $message, $headers );
			}
		}

		/**
		 * Send customer an email once cart has emptied.
		 *
		 * @access public
		 */
		public function limited_item( $current_data ) {
			if ( is_user_logged_in() ) {
				$user_id  = get_current_user_id();
				$userdata = get_userdata( $user_id );

				$product_data = wc_get_product( $current_data['variation_id'] ? $current_data['variation_id'] : $current_data['product_id'] );
				$product_name = $product_data->get_name();

				$send_to = $userdata->email;
				$subject = sprintf( __( 'Are you sure? "%s" is a limited edition!', 'co-cart-tweaks' ), $product_name );
				$message = sprintf( __( '"%s" is a limited edition. Once it\'s gone that is it. No more! Are you sure you don\'t want to purchase it?', 'co-cart-tweaks' ), $product_name );
				$headers = array(
					'Content-Type: text/html; charset=UTF-8',
					'From: Me Myself <me@example.net>'
				);

				wp_email( $send_to, $subject, $message, $headers );
			}
		}

		/**
		 * Returns the cart contents without the cart item key as the parent array.
		 *
		 * @access public
		 * @param  array $cart_contents
		 * @return array $cart_contents
		 */
		public function remove_parent_cart_item_key( $cart_contents ) {
			$new_cart_contents = array();

			foreach ( $cart_contents as $item_key => $cart_item ) {
				$new_cart_contents[] = $cart_item;
			}

			return $new_cart_contents;
		}

		/**
		 * Enhances the cart return.
		 *
		 * 1. Places the cart content under a new array.
		 * 2. Returns the shipping status of the cart.
		 *
		 * @access public
		 * @param  array $cart_contents
		 * @return array $cart_contents
		 */
		public function enhance_cart_return( $cart_contents ) {
			$new_cart_contents = array();

			// Places the cart contents under a new array.
			$new_cart_contents['items'] = $cart_contents;

			// Returns the shipping status of the cart.
			$new_cart_contents['needs_shipping'] = WC()->cart->needs_shipping();
	
			return $new_cart_contents;
		}

		/**
		 * Returns the Product SKU for item when getting the cart.
		 *
		 * @access public
		 * @param  array  $cart_contents
		 * @param  int    $item_key
		 * @param  array  $cart_item
		 * @param  object $_product
		 * @return array  $cart_contents
		 */
		public function return_product_sku( $cart_contents, $item_key, $cart_item, $_product ) {
			$cart_contents[$item_key]['sku'] = $_product->get_sku();

			return $cart_contents;
		}

		/**
		 * Returns the Product Weight for item when getting the cart.
		 *
		 * @access public
		 * @param  array  $cart_contents
		 * @param  int    $item_key
		 * @param  array  $cart_item
		 * @param  object $_product
		 */
		public function return_product_weight( $cart_contents, $item_key, $cart_item, $_product ) {
			$cart_contents[$item_key]['weight'] = $_product->get_weight();

			return $cart_contents;
		}

		/**
		 * Stop item being added and return a custom message should a required item not be in the cart.
		 *
		 * @access public
		 * @param  bool   $status
		 * @param  string $response
		 * @param  object $product_data
		 * @return array
		 */
		public function requires_specific_item( $status, $response, $product_data ) {
			$cart_contents = isset( WC()->cart ) ? WC()->cart->get_cart() : WC()->session->cart;

			$required_product_id = '123'; // Replace with real product ID number.

			foreach ( $cart_contents as $item_key => $cart_item ) { 
				// If required product ID does not exist return false.
				if ( $cart_item['id'] != $required_product_id ) {
					$status = false;
				}
			}

			// If status is false return custom message.
			if ( ! $status ) {
				$response = __( 'This item requires a specific item to be added first to the cart.', 'co-cart-tweaks' );
			}

			return array( $status, $response );
		}

		/**
		 * Make the plugin translation ready.
		 *
		 * Translations should be added in the WordPress language directory:
		 *      - WP_LANG_DIR/plugins/co-cart-tweaks-LOCALE.mo
		 *
		 * @access public
		 * @return void
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'co-cart-tweaks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

	} // END class

} // END if class exists

new CoCart_Tweaks();
