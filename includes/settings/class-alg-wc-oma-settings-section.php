<?php
/**
 * Order Minimum Amount for WooCommerce - Section Settings
 *
 * @version 3.4.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_Section' ) ) :

class Alg_WC_OMA_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 3.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		add_filter( 'woocommerce_get_sections_' . 'alg_wc_oma',                   array( $this, 'settings_section' ) );
		add_filter( 'woocommerce_get_settings_' . 'alg_wc_oma' . '_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
	}

	/**
	 * settings_section.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function settings_section( $sections ) {
		$sections[ $this->id ] = $this->desc;
		return $sections;
	}

	/**
	 * get_section_link.
	 *
	 * @version 3.4.0
	 * @since   3.4.0
	 * @todo    [next] generate links automatically
	 */
	function get_section_link( $section = 'general' ) {
		$titles = array(
			'general'             => __( 'General', 'order-minimum-amount-for-woocommerce' ),
			'amounts'             => __( 'Amounts', 'order-minimum-amount-for-woocommerce' ),
			'messages'            => __( 'Messages', 'order-minimum-amount-for-woocommerce' ),
			'user_roles'          => __( 'User Roles', 'order-minimum-amount-for-woocommerce' ),
			'users'               => __( 'Users', 'order-minimum-amount-for-woocommerce' ),
			'products'            => __( 'Products', 'order-minimum-amount-for-woocommerce' ),
			'shipping'            => __( 'Shipping', 'order-minimum-amount-for-woocommerce' ),
			'gateways'            => __( 'Payment Gateways', 'order-minimum-amount-for-woocommerce' ),
			'memberships'         => __( 'Memberships', 'order-minimum-amount-for-woocommerce' ),
			'currencies'          => __( 'Currencies', 'order-minimum-amount-for-woocommerce' ),
			'cart_products'       => __( 'Cart Products', 'order-minimum-amount-for-woocommerce' ),
			'products_cart_total' => __( 'Cart Total', 'order-minimum-amount-for-woocommerce' ),
		);
		return '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_oma&section=' . ( 'general' === $section ? '' : $section ) ) . '">' . $titles[ $section ] . '</a>';
	}

	/**
	 * get_pro_msg.
	 *
	 * @version 3.1.0
	 * @since   3.1.0
	 */
	function get_pro_msg( $msg ) {
		return apply_filters( 'alg_wc_oma_settings', '<p style="background-color: #cccccc; padding: 15px;">' .
			sprintf( 'You will need <a target="_blank" href="%s">Order Minimum/Maximum Amount for WooCommerce Pro</a> plugin to %s.',
				'https://wpfactory.com/item/order-minimum-maximum-amount-for-woocommerce/', $msg ) . '</p>' );
	}

	/**
	 * get_info_icon.
	 *
	 * @version 3.2.0
	 * @since   3.2.0
	 * @see     https://developer.wordpress.org/resource/dashicons/
	 */
	function get_info_icon() {
		return '<span class="dashicons dashicons-info"></span> ';
	}

	/**
	 * format_notes.
	 *
	 * @version 3.2.0
	 * @since   3.2.0
	 */
	function format_notes( $notes ) {
		return '<p>' . $this->get_info_icon() . implode( '</p><p>' . $this->get_info_icon(), $notes ) . '</p>';
	}

	/**
	 * get_products.
	 *
	 * @version 3.1.0
	 * @since   3.1.0
	 */
	function get_products() {
		$result = array();
		foreach ( wc_get_products( array( 'limit' => -1, 'return' => 'ids' ) ) as $product_id ) {
			$result[ $product_id ] = get_the_title( $product_id );
		}
		return $result;
	}

	/**
	 * get_terms.
	 *
	 * @version 3.1.0
	 * @since   3.1.0
	 */
	function get_terms( $taxonomy ) {
		$result = array();
		$terms  = get_terms( array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		) );
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$result[ $term->term_id ] = $term->name;
			}
		}
		return $result;
	}

	/**
	 * get_products_options.
	 *
	 * @version 3.3.0
	 * @since   3.3.0
	 * @todo    [later] variations?
	 * @todo    [maybe] set products as comma separated list of IDs (e.g. for WPML/Polylang)
	 * @todo    [maybe] better desc?
	 */
	function get_products_options( $type = '' ) {
		$settings = array();
		$desc_include = ( '' === $type ?
			__( 'Only check min/max amounts if there is at least one selected product(s) in cart.', 'order-minimum-amount-for-woocommerce' ) :
			__( 'Include in cart total.', 'order-minimum-amount-for-woocommerce' ) );
		$desc_exclude = ( '' === $type ?
			__( 'Do not check min/max amounts if there is at least one selected product(s) in cart.', 'order-minimum-amount-for-woocommerce' ) :
			__( 'Exclude from cart total.', 'order-minimum-amount-for-woocommerce' ) );
		$options_data = array(
			'product' => array(
				'title' => __( 'Individual Products', 'order-minimum-amount-for-woocommerce' ),
				'data'  => $this->get_products(),
			),
			'product_cat' => array(
				'title' => __( 'Product Categories', 'order-minimum-amount-for-woocommerce' ),
				'data'  => $this->get_terms( 'product_cat' ),
			),
			'product_tag' => array(
				'title' => __( 'Product Tags', 'order-minimum-amount-for-woocommerce' ),
				'data'  => $this->get_terms( 'product_tag' ),
			),
		);
		foreach ( $options_data as $id => $data ) {
			$settings = array_merge( $settings, array(
				array(
					'title'    => $data['title'],
					'type'     => 'title',
					'id'       => "alg_wc_oma_products_{$id}{$type}_options",
				),
				array(
					'title'    => __( 'Require', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => $desc_include,
					'id'       => "alg_wc_oma_{$id}_include{$type}",
					'default'  => array(),
					'type'     => 'multiselect',
					'class'    => 'chosen_select',
					'options'  => $data['data'],
				),
				array(
					'title'    => __( 'Exclude', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => $desc_exclude,
					'id'       => "alg_wc_oma_{$id}_exclude{$type}",
					'default'  => array(),
					'type'     => 'multiselect',
					'class'    => 'chosen_select',
					'options'  => $data['data'],
				),
				array(
					'type'     => 'sectionend',
					'id'       => "alg_wc_oma_products_{$id}{$type}_options",
				),
			) );
		}
		return $settings;
	}

}

endif;
