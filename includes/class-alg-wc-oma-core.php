<?php
/**
 * Order Minimum Amount for WooCommerce - Core Class
 *
 * @version 3.4.1
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Core' ) ) :

class Alg_WC_OMA_Core {

	/**
	 * Constructor.
	 *
	 * @version 3.4.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->amounts = require_once( 'class-alg-wc-oma-amount-types.php' );
		$this->hooks   = require_once( 'class-alg-wc-oma-hooks.php' );
		add_shortcode( 'alg_wc_oma_translate', array( $this, 'language_shortcode' ) );
		do_action( 'alg_wc_oma_core_loaded', $this );
	}

	/**
	 * add_to_log.
	 *
	 * For debugging.
	 *
	 * @version 3.0.0
	 * @since   3.0.0
	 */
	function add_to_log( $message ) {
		if ( function_exists( 'wc_get_logger' ) && ( $log = wc_get_logger() ) ) {
			$log->log( 'info', $message, array( 'source' => 'alg-wc-oma' ) );
		}
	}

	/**
	 * language_shortcode.
	 *
	 * @version 3.4.0
	 * @since   1.2.1
	 */
	function language_shortcode( $atts, $content = '' ) {
		// E.g.: `[alg_wc_oma_translate lang="DE" lang_text="Text for DE" not_lang_text="Text for other languages"]`
		if ( isset( $atts['lang_text'] ) && isset( $atts['not_lang_text'] ) && ! empty( $atts['lang'] ) ) {
			return ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ?
				$atts['not_lang_text'] : $atts['lang_text'];
		}
		// E.g.: `[alg_wc_oma_translate lang="DE"]Text for DE[/alg_wc_oma_translate][alg_wc_oma_translate lang="NL"]Text for NL[/alg_wc_oma_translate][alg_wc_oma_translate not_lang="DE,NL"]Text for other languages[/alg_wc_oma_translate]`
		return (
			( ! empty( $atts['lang'] )     && ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ) ||
			( ! empty( $atts['not_lang'] ) &&     defined( 'ICL_LANGUAGE_CODE' ) &&   in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['not_lang'] ) ) ) ) )
		) ? '' : $content;
	}

	/**
	 * get_min_max_amount.
	 *
	 * @version 3.3.0
	 * @since   1.0.0
	 * @todo    [later] code refactoring: remove `alg_wc_oma_before_get_min_max_amount`
	 * @todo    [maybe] `alg_wc_oma_after_get_min_max_amount`: `$order_minimum_sum`?
	 * @todo    [maybe] rename: `alg_wc_oma_get_order_min_max_amount` to `alg_wc_oma_get_min_max_amount`?
	 * @todo    [maybe] move `$data_version_user_role` etc. to `class-alg-wc-oma-deprecated.php`
	 */
	function get_min_max_amount( $min_or_max, $amount_type, $product_id = false, $scope = false ) {
		if ( $product_id ) {
			return array( 'amount' => apply_filters( 'alg_wc_oma_before_get_min_max_amount', 0, $min_or_max, $amount_type, $product_id, $scope ), 'source' => 'product' );
		}
		$amount_data = apply_filters( 'alg_wc_oma_get_order_min_max_amount', false, $min_or_max, $amount_type );
		if ( empty( $amount_data['amount'] ) ) {
			if ( 'yes' === get_option( 'alg_wc_oma_by_user_role_enabled', 'no' ) ) {
				// User roles
				$current_user_roles      = $this->get_current_user_roles();
				$enabled_user_roles_keys = array_keys( $this->get_enabled_user_roles() );
				$data_version            = get_option( 'alg_wc_oma_data_version', array() );
				$data_version_user_role  = ( isset( $data_version['user_role'] ) ? $data_version['user_role'] : 0 );
				foreach ( $current_user_roles as $role_key ) {
					if ( empty( $role_key ) ) {
						$role_key = 'guest';
					}
					if ( in_array( $role_key, $enabled_user_roles_keys ) ) {
						if ( 'min' === $min_or_max && 'sum' === $amount_type && version_compare( $data_version_user_role, '2.0.0', '<' ) ) {
							if ( ( $order_minimum_sum = get_option( 'alg_wc_order_minimum_amount_by_user_role_' . $role_key, 0 ) ) > 0 ) {
								return array( 'amount' => $order_minimum_sum, 'source' => 'user_role' );
							}
						} else {
							if ( ! isset( $this->amount_by_user_role[ $min_or_max ][ $amount_type ] ) ) {
								$this->amount_by_user_role[ $min_or_max ][ $amount_type ] = get_option( "alg_wc_oma_{$min_or_max}_{$amount_type}_by_user_role", array() );
							}
							$amount_by_user_role = ( isset( $this->amount_by_user_role[ $min_or_max ][ $amount_type ][ $role_key ] ) ?
								$this->amount_by_user_role[ $min_or_max ][ $amount_type ][ $role_key ] : 0 );
							if ( 0 != $amount_by_user_role ) {
								$amount_data = array( 'amount' => $amount_by_user_role, 'source' => 'user_role' );
								break;
							}
						}
					}
				}
			}
		}
		if ( empty( $amount_data['amount'] ) ) {
			// General
			$amount_data = array( 'amount' => get_option( "alg_wc_oma_{$min_or_max}_{$amount_type}", 0 ), 'source' => '' );
		}
		$amount_data['amount'] = apply_filters( 'alg_wc_oma_after_get_min_max_amount', $amount_data['amount'], $min_or_max, $amount_type );
		$amount_data['amount'] = max( $amount_data['amount'], 0 );
		return $amount_data;
	}

	/**
	 * get_current_user_roles.
	 *
	 * @version 3.2.0
	 * @since   3.2.0
	 * @todo    [maybe] save it in `$this->current_user_roles`?
	 */
	function get_current_user_roles() {
		$current_user = wp_get_current_user();
		return ( ! $current_user->exists() ? array( 'guest' ) : $current_user->roles );
	}

	/**
	 * is_cart_empty.
	 *
	 * @version 3.3.0
	 * @since   3.3.0
	 */
	function is_cart_empty() {
		return ( ! function_exists( 'WC' ) || ! isset( WC()->cart ) || WC()->cart->is_empty() );
	}

	/**
	 * get_cart_total.
	 *
	 * @version 3.2.0
	 * @since   1.0.0
	 * @todo    [later] recheck if we need `calculate_totals` for `qty` etc.?
	 */
	function get_cart_total( $amount_type, $product_id = false, $do_count_by_term = false, $taxonomy = false ) {
		if ( ! isset( WC()->cart ) ) {
			return 0;
		}
		WC()->cart->calculate_totals();
		return $this->amounts->get_cart_total( $amount_type, $product_id, $do_count_by_term, $taxonomy );
	}

	/**
	 * is_equal.
	 *
	 * @version 3.1.1
	 * @since   3.0.0
	 * @todo    [maybe] better epsilon value, e.g. `defined( 'PHP_FLOAT_EPSILON' ) ? PHP_FLOAT_EPSILON : $this->get_amount_step()`
	 */
	function is_equal( $float1, $float2 ) {
		$epsilon = $this->get_amount_step();
		return ( abs( $float1 - $float2 ) < $epsilon );
	}

	/**
	 * check_min_max_amount.
	 *
	 * @version 3.4.0
	 * @since   2.0.0
	 * @todo    [next] pass `amount_type` (for the filter)?
	 * @todo    [later] [!] `! $amount`?
	 * @todo    [maybe] when cart total *sum* is zero: check even if we are comparing for e.g. "min qty"; also exclude shipping (i.e. even if the "Exclude shipping" option is disabled)
	 */
	function check_min_max_amount( $min_or_max, $amount, $total ) {
		$amount = floatval( $amount );
		$total  = floatval( $total );
		$passed = ( ! $amount || $this->is_equal( $amount, $total ) ? true : ( 'min' === $min_or_max ? $total > $amount : $total < $amount ) );
		return apply_filters( 'alg_wc_oma_check_order_min_max_amount', $passed, $min_or_max, $amount, $total );
	}

	/**
	 * get_placeholders.
	 *
	 * @version 3.3.0
	 * @since   2.2.0
	 */
	function get_placeholders( $min_or_max, $amount_type, $amount_data, $total, $product_id = false, $term_id = false ) {
		$diff = ( 'min' === $min_or_max ? ( $amount_data['amount'] - $total ) : ( $total - $amount_data['amount'] ) );
		$placeholders = array(
			'%amount_source%' => $amount_data['source'], // for debugging
			'%amount%'        => $this->amounts->format( $amount_data['amount'], $amount_type ),
			'%total%'         => $this->amounts->format( $total,                 $amount_type ),
			'%diff%'          => $this->amounts->format( $diff,                  $amount_type ),
			'%amount_raw%'    => $amount_data['amount'],
			'%total_raw%'     => $total,
			'%diff_raw%'      => $diff,
			'%product_title%' => ( $product_id ? get_the_title( $product_id ) : '' ),
			'%term_title%'    => ( $term_id    ? ( ( $term = get_term( $term_id ) ) && ! is_wp_error( $term ) ? $term->name : '' ) : '' ),
		);
		return apply_filters( 'alg_wc_oma_placeholders', $placeholders, $min_or_max, $amount_type, $amount_data, $total, $diff, $product_id, $term_id );
	}

	/**
	 * get_all_user_roles.
	 *
	 * @version 3.2.0
	 * @since   1.0.0
	 */
	function get_all_user_roles( $do_rearrange = false ) {
		global $wp_roles;
		$roles = apply_filters( 'editable_roles', ( isset( $wp_roles ) && is_object( $wp_roles ) ? $wp_roles->roles : array() ) );
		$roles = wp_list_pluck( $roles, 'name' );
		$roles = array_merge( array( 'guest' => __( 'Guest', 'order-minimum-amount-for-woocommerce' ) ), $roles );
		if ( $do_rearrange && isset( $roles['customer'] ) ) {
			$customer_title = $roles['customer'];
			unset( $roles['customer'] );
			$roles = array_merge( array( 'customer' => $customer_title ), $roles );
		}
		return $roles;
	}

	/**
	 * get_enabled_user_roles.
	 *
	 * @version 3.2.0
	 * @since   3.2.0
	 */
	function get_enabled_user_roles( $do_rearrange = false ) {
		$enabled_user_roles = get_option( 'alg_wc_oma_enabled_user_roles', array() );
		$all_user_roles     = $this->get_all_user_roles( $do_rearrange );
		return ( empty( $enabled_user_roles ) ? $all_user_roles : array_intersect_key( $all_user_roles, array_flip( $enabled_user_roles ) ) );
	}

	/**
	 * get_default_message.
	 *
	 * @version 3.4.0
	 * @since   3.0.0
	 * @todo    [later] shipping, per product etc.: move to Pro?
	 * @todo    [maybe] better default messages for product, product_cat and product_tag?
	 * @todo    [maybe] use filter instead?
	 * @todo    [maybe] code refactoring? e.g. merge `shipping` and `gateway`
	 */
	function get_default_message( $min_or_max, $scope = '', $source = '' ) {
		$messages = array(
			'product' => array(
				'shipping' => __( 'You must have an order with a %s of %%amount%% for %%product_title%% for "%%shipping_method%%" to place your order, your current product total is %%total%%.', 'order-minimum-amount-for-woocommerce' ),
				'gateway'  => __( 'You must have an order with a %s of %%amount%% for %%product_title%% for "%%payment_gateway%%" to place your order, your current product total is %%total%%.', 'order-minimum-amount-for-woocommerce' ),
				''         => __( 'You must have an order with a %s of %%amount%% for %%product_title%% to place your order, your current product total is %%total%%.', 'order-minimum-amount-for-woocommerce' ),
			),
			'product_cat' => array(
				'shipping' => __( 'You must have an order with a %s of %%amount%% for %%term_title%% category for "%%shipping_method%%" to place your order, your current category total is %%total%%.', 'order-minimum-amount-for-woocommerce' ),
				'gateway'  => __( 'You must have an order with a %s of %%amount%% for %%term_title%% category for "%%payment_gateway%%" to place your order, your current category total is %%total%%.', 'order-minimum-amount-for-woocommerce' ),
				''         => __( 'You must have an order with a %s of %%amount%% for %%term_title%% category to place your order, your current category total is %%total%%.', 'order-minimum-amount-for-woocommerce' ),
			),
			'product_tag' => array(
				'shipping' => __( 'You must have an order with a %s of %%amount%% for %%term_title%% tag for "%%shipping_method%%" to place your order, your current tag total is %%total%%.', 'order-minimum-amount-for-woocommerce' ),
				'gateway'  => __( 'You must have an order with a %s of %%amount%% for %%term_title%% tag for "%%payment_gateway%%" to place your order, your current tag total is %%total%%.', 'order-minimum-amount-for-woocommerce' ),
				''         => __( 'You must have an order with a %s of %%amount%% for %%term_title%% tag to place your order, your current tag total is %%total%%.', 'order-minimum-amount-for-woocommerce' ),
			),
			'' => array(
				'shipping' => __( 'You must have an order with a %s of %%amount%% for "%%shipping_method%%" to place your order, your current order total is %%total%%.', 'order-minimum-amount-for-woocommerce' ),
				'gateway'  => __( 'You must have an order with a %s of %%amount%% for "%%payment_gateway%%" to place your order, your current order total is %%total%%.', 'order-minimum-amount-for-woocommerce' ),
				''         => __( 'You must have an order with a %s of %%amount%% to place your order, your current order total is %%total%%.', 'order-minimum-amount-for-woocommerce' ),
			),
		);
		$_source = ( $this->is_source_message( $source )       ? $source  : '' );
		$_scope  = ( isset( $messages[ $scope ] )              ? $scope   : '' );
		$_source = ( isset( $messages[ $_scope ][ $_source ] ) ? $_source : '' );
		$message = $messages[ $_scope ][ $_source ];
		return sprintf( $message, ( 'min' === $min_or_max ? __( 'minimum', 'order-minimum-amount-for-woocommerce' ) : __( 'maximum', 'order-minimum-amount-for-woocommerce' ) ) );
	}

	/**
	 * is_source_message.
	 *
	 * @version 3.4.0
	 * @since   3.4.0
	 */
	function is_source_message( $source ) {
		return (
			( 'shipping' === $source && 'yes' === get_option( 'alg_wc_oma_by_shipping_messages_enabled', 'no' ) ) ||
			( 'gateway'  === $source && 'yes' === get_option( 'alg_wc_oma_by_gateway_messages_enabled',  'no' ) )
		);
	}

	/**
	 * get_message_option_id.
	 *
	 * @version 3.4.0
	 * @since   3.3.0
	 */
	function get_message_option_id( $cart_or_checkout, $scope, $source ) {
		$id = $cart_or_checkout;
		if ( '' != $scope ) {
			$id .= '_' . $scope;
		}
		if ( '' != $source && $this->is_source_message( $source ) ) {
			$id .= '_' . $source;
		}
		return $id;
	}

	/**
	 * get_notice_content.
	 *
	 * @version 3.3.0
	 * @since   2.2.0
	 */
	function get_notice_content( $min_or_max, $amount_type, $amount_data, $total, $cart_or_checkout, $scope = '', $product_id = false, $term_id = false ) {
		$id           = $this->get_message_option_id( $cart_or_checkout, $scope, $amount_data['source'] );
		$content      = get_option( "alg_wc_oma_{$min_or_max}_{$amount_type}_message", array() );
		$content      = ( isset( $content[ $id ] ) ? $content[ $id ] : $this->get_default_message( $min_or_max, $scope, $amount_data['source'] ) );
		$content      = do_shortcode( $content );
		$placeholders = $this->get_placeholders( $min_or_max, $amount_type, $amount_data, $total, $product_id, $term_id );
		return str_replace( array_keys( $placeholders ), $placeholders, $content );
	}

	/**
	 * get_title.
	 *
	 * @version 3.4.0
	 * @since   3.0.0
	 * @todo    [maybe] use another glue, e.g. ` / `
	 */
	function get_title( $min_or_max, $amount_type, $desc = array(), $do_strip_tags = false ) {
		$title = sprintf( ( 'min' === $min_or_max ? __( 'Min %s', 'order-minimum-amount-for-woocommerce' ) : __( 'Max %s', 'order-minimum-amount-for-woocommerce' ) ),
			$this->amounts->get_title( $amount_type ) );
		$desc = array_filter( $desc );
		if ( ! empty( $desc ) ) {
			$title .= '<span style="float:right;"> [' . implode( '] [', $desc ) . ']</span>';
		}
		return ( $do_strip_tags ? strip_tags( $title ) : $title );
	}

	/**
	 * get_enabled_limits.
	 *
	 * @version 3.4.0
	 * @since   3.0.0
	 */
	function get_enabled_limits( $limits = false ) {
		$result = get_option( 'alg_wc_oma_amount_limits', array( 'min', 'max' ) );
		$result = ( empty( $result ) ? array( 'min', 'max' ) : $result );
		return ( ! $limits ? $result : array_intersect( $result, $limits ) );
	}

	/**
	 * get_enabled_types.
	 *
	 * @version 3.4.0
	 * @since   3.0.0
	 * @todo    [maybe] rename to `get_enabled_amount_types()`?
	 */
	function get_enabled_types( $types = false ) {
		$result = get_option( 'alg_wc_oma_amount_types', array( 'sum', 'qty' ) );
		$result = ( empty( $result ) ? array_keys( $this->amounts->get_types() ) : $result );
		return ( ! $types ? $result : array_intersect( $result, $types ) );
	}

	/**
	 * get_amount_step.
	 *
	 * @version 3.0.0
	 * @since   3.0.0
	 */
	function get_amount_step() {
		return 0.000001;
	}

	/**
	 * get_amount_custom_atts.
	 *
	 * @version 3.1.0
	 * @since   3.0.0
	 */
	function get_amount_custom_atts( $min = -1 ) {
		return array( 'step' => $this->get_amount_step(), 'min' => $min );
	}

	/**
	 * get_amounts_desc.
	 *
	 * @version 3.1.0
	 * @since   3.1.0
	 * @todo    [next] simplify the first part, e.g.: `Ignored if set to zero (%s).`
	 * @todo    [later] better desc
	 */
	function get_amounts_desc() {
		return __( 'Ignored if set to zero, i.e. higher level ("general") amount will be applied.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
			sprintf( __( 'No amount will be applied if set to a negative value (e.g. %s).', 'order-minimum-amount-for-woocommerce' ), '<code>-1</code>' );
	}

	/**
	 * get_shipping_methods.
	 *
	 * @version 3.2.0
	 * @since   3.2.0
	 */
	function get_shipping_methods() {
		$shipping_methods = array();
		foreach ( WC()->shipping()->load_shipping_methods() as $method ) {
			$shipping_methods[ $method->id ] = $method->get_method_title();
		}
		return $shipping_methods;
	}

	/**
	 * get_shipping_zones.
	 *
	 * @version 3.2.0
	 * @since   3.2.0
	 */
	function get_shipping_zones( $include_empty_zone = true ) {
		$zones = WC_Shipping_Zones::get_zones();
		if ( $include_empty_zone ) {
			$zone                                                = new WC_Shipping_Zone( 0 );
			$zones[ $zone->get_id() ]                            = $zone->get_data();
			$zones[ $zone->get_id() ]['zone_id']                 = $zone->get_id();
			$zones[ $zone->get_id() ]['formatted_zone_location'] = $zone->get_formatted_location();
			$zones[ $zone->get_id() ]['shipping_methods']        = $zone->get_shipping_methods();
		}
		return $zones;
	}

	/**
	 * get_shipping_methods_instances.
	 *
	 * @version 3.2.0
	 * @since   3.2.0
	 */
	function get_shipping_methods_instances( $full_data = false ) {
		$shipping_methods = array();
		foreach ( $this->get_shipping_zones() as $zone_id => $zone_data ) {
			foreach ( $zone_data['shipping_methods'] as $shipping_method ) {
				if ( $full_data ) {
					$shipping_methods[ $shipping_method->instance_id ] = array(
						'zone_id'                     => $zone_id,
						'zone_name'                   => $zone_data['zone_name'],
						'formatted_zone_location'     => $zone_data['formatted_zone_location'],
						'shipping_method_title'       => $shipping_method->title,
						'shipping_method_id'          => $shipping_method->id,
						'shipping_method_instance_id' => $shipping_method->instance_id,
					);
				} else {
					$shipping_methods[ $shipping_method->instance_id ] = $zone_data['zone_name'] . ': ' . $shipping_method->title;
				}
			}
		}
		return $shipping_methods;
	}

	/**
	 * get_shipping_options.
	 *
	 * @version 3.2.0
	 * @since   3.2.0
	 */
	function get_shipping_options( $shipping_type ) {
		switch ( $shipping_type ) {
			case 'method':
				return $this->get_shipping_methods();
			case 'instance':
				return $this->get_shipping_methods_instances();
			case 'zone':
				$options = array();
				$zones   = $this->get_shipping_zones();
				foreach ( $zones as $id => $data ) {
					$options[ 'z' . $id ] = $data['zone_name'];
				}
				return $options;
		}
	}

	/**
	 * get_memberships.
	 *
	 * @version 3.4.0
	 * @since   3.4.0
	 * @see     https://docs.woocommerce.com/document/woocommerce-memberships-function-reference/
	 * @see     https://docs.memberpress.com/
	 * @todo    [next] `$membership->slug` -> `$membership->ID`?
	 */
	function get_memberships() {
		$memberships = array();
		// WooCommerce Memberships
		if ( function_exists( 'wc_memberships_get_membership_plans' ) ) {
			foreach ( wc_memberships_get_membership_plans() as $membership ) {
				$memberships[ 'alg_wc_wcm_' . $membership->slug ] = array(
					'title' => $membership->name . ' (' . __( 'WooCommerce Memberships', 'order-minimum-amount-for-woocommerce' ) . ')',
					'type' => 'wc_memberships',
				);
			}
		}
		// MemberPress
		if ( class_exists( 'MeprUser' ) ) {
			$args = array(
				'post_type'      => 'memberpressproduct',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'orderby'        => 'ID',
				'order'          => 'DESC',
			);
			$loop = new WP_Query( $args );
			if ( $loop->have_posts() ) {
				foreach ( $loop->posts as $membership ) {
					$memberships[ 'alg_wc_mepr_' . $membership->ID ] = array(
						'title' => $membership->post_title . ' (' . __( 'MemberPress', 'order-minimum-amount-for-woocommerce' ) . ')',
						'type' => 'memberpress',
					);
				}
			}
		}
		return $memberships;
	}

}

endif;

return new Alg_WC_OMA_Core();
