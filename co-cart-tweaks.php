<?php
/*
 * Plugin Name: CoCart - Tweaks
 * Plugin URI:  https://cocart.xyz
 * Description: Example of using CoCart filters to extend the information sent and returned.
 * Author:      Sébastien Dumont
 * Author URI:  https://sebastiendumont.com
 * Version:     0.0.15
 * Text Domain: co-cart-tweaks
 * Domain Path: /languages/
 *
 * WC requires at least: 3.6.0
 * WC Tested up to: 3.9.3
 *
 * Copyright: © 2020 Sébastien Dumont, (mailme@sebastiendumont.com)
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

			// This filter can also be used to return the line total and subtotal for each item in cart with X amount of decimals.
			//add_filter( 'cocart_cart_contents', array( $this, 'return_price_decimals' ), 15, 4 );

			// This filter can also be used to return the stock status, stock quantity and a color based on the stock status.
			//add_filter( 'cocart_cart_contents', array( $this, 'return_stock_status' ), 15, 4 );

			// Can be used to apply a condition for a specific item should it not be allowed for a customer to add on it's own.
			//add_filter( 'cocart_ok_to_add', array( $this, 'requires_specific_item' ), 10, 3 );

			// Returns the shipping method contents without the shipping method item key as the parent array.
			//add_filter( 'cocart_available_shipping_methods', array( $this, 'remove_parent_shipping_method_item_key'), 0 );
			
			// This filter could be used for example, to remove the free shipping method should the cart have X amount of items.
			//add_filter( 'cocart_available_shipping_methods', array( $this, 'no_free_shipping' ), 99, 1 );

			// This filter allows you to adjust the product data returned.
			//add_filter( 'cocart_prepare_product_object', array( $this, 'add_extra_product_data' ), 10, 2 );

			// This filer allows you to change the empty response when the cart is empty. - Requires v2.0.8
			//add_filter( 'cocart_return_empty_cart', array( $this, 'go_back_to_shop' ) );

			// Enable prerelease updates for CoCart Pro.
			//add_filter( 'cocart_pro_allow_prereleases', function() { return true; });


			// Filters below this line require version 2.1 of CoCart in order to use them.

			// Disable debug logging for specific events.
			//add_filter( 'cocart_logging', array( $this, 'disable_logs' ), 0, 3 );

			// This filter allows you to override the product name and title.
			//add_filter( 'cocart_product_name', array( $this, 'override_product_name' ), 10, 3 );
			//add_filter( 'cocart_product_title', array( $this, 'override_product_name' ), 10, 3 );

			// This filter allows you to override the product quantity.
			//add_filter( 'cocart_add_to_cart_quantity', array( $this, 'override_product_quantity' ), 10, 5 );


			// Override the cookie name.
			//add_filter( 'cocart_cookie', function() { return 'cocart_demo'; });

			// Enables all cross origin header requests.
			//add_filter( 'cocart_disable_all_cors', function() { return false; });

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
			$user_id  = get_current_user_id();

			if ( $user_id > 0 ) {
				$userdata = get_userdata( $user_id );
				$send_to = $userdata->email;
				$subject = __( 'What happened?', 'co-cart-tweaks' );
				$message = __( 'Why did you empty your cart? Anything we can do to help?', 'co-cart-tweaks' );
				$headers = array(
					'Content-Type: text/html; charset=UTF-8',
					'From: Me Myself <me@example.net>'
				);

				wp_mail( $send_to, $subject, $message, $headers );
			}
		}

		/**
		 * Send customer an email once cart has emptied.
		 *
		 * @access public
		 */
		public function limited_item( $current_data ) {
			$user_id  = get_current_user_id();

			if ( $user_id > 0 ) {
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

				wp_mail( $send_to, $subject, $message, $headers );
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
		 * 1. Return the cart hash.
		 * 2. Places the cart content under a new array called items.
		 * 3. Returns the item count of all items.
		 * 4. Returns the shipping status of the cart.
		 * 5. Returns the payment status of the cart.
		 *
		 * @access public
		 * @param  array $cart_contents
		 * @return array $new_cart_contents
		 */
		public function enhance_cart_return( $cart_contents ) {
			$new_cart_contents = array();

			// Get Cart.
			$cart = WC()->cart;

			// Cart hash.
			$new_cart_contents['cart_hash'] = $cart->get_cart_hash();

			// Places the cart contents under a new array.
			$new_cart_contents['items'] = $cart_contents;

			// Returns item count of all items.
			$new_cart_contents['items_counted'] = $cart->get_cart_contents_count();

			// Returns the shipping status of the cart.
			$new_cart_contents['needs_shipping'] = $cart->needs_shipping();

			// Returns the payment status of the cart.
			$new_cart_contents['needs_payment'] = $cart->needs_payment();
	
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
		 * Returns the line total and subtotal with two decimals for each item in the cart.
		 *
		 * 1. Line subtotal example shows how to apply the decimals without the price format.
		 * 2. Line total example shows how to apply the decimals with price format, stripped of HTML and character decoded.
		 *
		 * @access public
		 * @param  array  $cart_contents
		 * @param  int    $item_key
		 * @param  array  $cart_item
		 * @param  object $_product
		 */
		public function return_price_decimals( $cart_contents, $item_key, $cart_item, $_product ) {
			$decimals = 2; // TODO: Change the number of decimals to your requirement.

			$cart_contents[$item_key]['line_subtotal'] = number_format( $cart_contents[$item_key]['line_subtotal'], $decimals, wc_get_price_decimal_separator(), wc_get_price_thousand_separator() );
			$cart_contents[$item_key]['line_total']    = html_entity_decode( strip_tags( wc_price( $cart_contents[$item_key]['line_total'], $decimals ) ) );

			return $cart_contents;
		}

		/**
		 * Return the stock status, stock quantity and a color based 
		 * on the stock status of each item in cart.
		 *
		 * @access public
		 * @param  array  $cart_contents
		 * @param  int    $item_key
		 * @param  array  $cart_item
		 * @param  object $_product
		 */
		public function return_stock_status( $cart_contents, $item_key, $cart_item, $_product ) {
			$status = $_product->get_stock_status();
			$color  = '#a46497';

			switch( $status ) {
				case 'instock':
					$status = __( 'In Stock', 'co-cart-tweaks' );
					$color  = '#7ad03a';
					break;
				case 'outofstock':
					$status = __( 'Out of Stock', 'co-cart-tweaks' );
					$color  = '#a00';
					break;
				case 'onbackorder':
					$status = __( 'Available on backorders', 'co-cart-tweaks' );
					break;
			}

			$cart_contents[$item_key]['stock'] = array(
				'status'         => $status,
				'stock_quantity' => $_product->get_stock_quantity(),
				'hex_color'      => $color
			);

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

			$status = true;

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
		 * Returns the shipping method contents without the shipping method item key as the parent array.
		 *
		 * @access public
		 * @param  array $shipping_method_contents
		 * @return array $shipping_method_contents
		 */
		public function remove_parent_shipping_method_item_key( $shipping_method_contents ) {
			$new_shipping_method_contents = array();
			foreach ( $shipping_method_contents as $item_key => $shipping_method_item ) {
				$new_shipping_method_contents[] = $shipping_method_item;
			}
			return $new_shipping_method_contents;
		}
		
		/**
		 * Remove the free shipping method should the cart have X amount of items.
		 * 
		 * Note: In this example I have asked it to check if the cart has 
		 * 4 or more items before removing the free shipping method.
		 *
		 * @access public
		 * @param  array  $available_methods
		 * @return array  $available_methods
		 */
		public function no_free_shipping( $available_methods ) {
			// TODO: Change the id ('free_shipping:3') according to the free shipping method set in your store.
			if ( WC()->cart->get_cart_contents_count() >= 4 ) {
				unset( $available_methods['free_shipping:3'] );
			}

			return $available_methods;
		}

		/**
		 * Add Extra Product Data.
		 *
		 * Note: This example shows the additional data added only for a specific product.
		 *
		 * @access public
		 * @param  object $response
		 * @param  object $object
		 * @return object $response
		 */
		public function add_extra_product_data( $response, $object ) {
			if ( $object->get_id() == '326' ) {
				$response->data['my_product_data'] = array(
					'limited_run_number' => '500',
				);
			}

			return $response;
		}

		/**
		 * Changes the empty response when the cart is empty.
		 *
		 * @access public
		 * @return string
		 */
		public function go_back_to_shop() {
			return __( 'Whoa there! I think you forgot to add items to the cart first. Go back to the shop and add something first', 'co-cart-tweaks' );
		}

		/**
		 * Filters the product name.
		 *
		 * @access public
		 * @param  object $_product
		 * @param  array  $cart_item
		 * @param  string $item_key
		 * @return string
		 */
		public function override_product_name( $_product, $cart_item, $item_key ) {
			return __( 'This is just a DEMO!', 'cocart-tweaks' );
		} // END override_product_name()

		/**
		 * Filters the quantity for specified products.
		 *
		 * @requires    2.1.0
		 * @access public
		 * @param  int   $quantity       - The original quantity of the item.
		 * @param  int   $product_id     - The product ID.
		 * @param  int   $variation_id   - The variation ID.
		 * @param  array $variation      - The variation data.
		 * @param  array $cart_item_data - The cart item data.
		 * @return int  $quantity       - The new quantity of the item.
		 */
		public function override_product_quantity( $quantity, $product_id, $variation_id, $variation, $cart_item_data ) {
			// Make sure you specify the product ID you want to override.
			if ( $product_id == 32 ) {
				return 3; // Make sure that you just return the number and not override the `$quantity` variable.
			}

			// Return `$quantity` variable for all other products that you are NOT overriding.
			return $quantity;
		} // END override_product_quantity()


		/**
		 * Disable debug logging for specific events.
		 *
		 * @access public
		 * @param  bool   $status - Logs enabled
		 * @param  string $type   - Type of event.
		 * @param  string $plugin - Plugin slug
		 * @return bool
		 */
		public function disable_logs( $status, $type, $plugin ) {
			if ( in_array( $type, array( 'info', 'notice' ) ) ) {
				return false;
			}

			return $status;
		} //END disable_logs()

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
