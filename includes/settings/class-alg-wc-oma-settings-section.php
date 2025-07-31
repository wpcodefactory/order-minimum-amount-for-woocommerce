<?php
/**
 * Order Minimum Amount for WooCommerce - Section Settings
 *
 * @version 4.6.6
 * @since   1.0.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_Section' ) ) :

	class Alg_WC_OMA_Settings_Section {

		/**
		 * id.
		 *
		 * @since   4.4.2
		 */
		protected $id;

		/**
		 * desc.
		 *
		 * @since   4.4.2
		 */
		protected $desc;

		/**
		 * Constructor.
		 *
		 * @version 4.6.6
		 * @since   1.0.0
		 */
		function __construct() {
			add_filter( 'woocommerce_get_sections_' . 'alg_wc_oma', array( $this, 'settings_section' ) );
			add_filter( 'woocommerce_get_settings_' . 'alg_wc_oma' . '_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
			add_action( 'admin_head', array( $this, 'custom_admin_inline_styles' ) );
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
		 * @version 4.0.0
		 * @since   3.4.0
		 *
		 * @todo    generate links automatically (if possible)?
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
				'coupons'             => __( 'Coupons', 'order-minimum-amount-for-woocommerce' ),
				'cart_products'       => __( 'Cart Products', 'order-minimum-amount-for-woocommerce' ),
				'products_cart_total' => __( 'Cart Total', 'order-minimum-amount-for-woocommerce' ),
			);
			return '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_oma&section=' . ( 'general' === $section ? '' : $section ) ) . '">' . $titles[ $section ] . '</a>';
		}

		/**
		 * get_save_changes_desc.
		 *
		 * @version 4.0.0
		 * @since   4.0.0
		 */
		function get_save_changes_desc() {
			return __( 'New settings fields will be displayed if you change this option and "Save changes".', 'order-minimum-amount-for-woocommerce' );
		}

		/**
		 * get_products.
		 *
		 * @version 4.0.0
		 * @since   3.1.0
		 *
		 * @see     https://github.com/woocommerce/woocommerce/wiki/wc_get_products-and-WC_Product_Query
		 */
		function get_products( $do_list_variations = false ) {
			$result = array();
			$type   = array_merge( array_keys( wc_get_product_types() ) );
			if ( $do_list_variations ) {
				$type[] = 'variation';
			}
			foreach ( wc_get_products( array( 'limit' => - 1, 'return' => 'ids', 'type' => $type ) ) as $product_id ) {
				$result[ $product_id ] = get_the_title( $product_id ) . " (#{$product_id})";
			}
			return $result;
		}

		/**
		 * get_terms.
		 *
		 * @version 4.0.0
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
					$result[ $term->term_id ] = $term->name . " (#{$term->term_id})";
				}
			}
			return $result;
		}

		/**
		 * add_current_values.
		 *
		 * This will add current values to the options. E.g. will be useful when switching language in backend with WPML. Will add deleted products/terms as well.
		 *
		 * @version 4.0.0
		 * @since   4.0.0
		 */
		function add_current_values( $data, $option_id, $id ) {
			$current_values = get_option( $option_id, array() );
			if ( ! empty( $current_values ) ) {
				switch ( $id ) {
					case 'product':
						$title = __( 'Product #%s', 'order-minimum-amount-for-woocommerce' );
						break;
					case 'product_cat':
						$title = __( 'Product category #%s', 'order-minimum-amount-for-woocommerce' );
						break;
					case 'product_tag':
						$title = __( 'Product tag #%s', 'order-minimum-amount-for-woocommerce' );
						break;
				}
				foreach ( $current_values as $current_value ) {
					if ( ! isset( $data[ $current_value ] ) ) {
						$data[ $current_value ] = sprintf( $title, $current_value );
					}
				}
			}
			return $data;
		}

		/**
		 * get_products_options.
		 *
		 * @version 4.0.1
		 * @since   3.3.0
		 */
		function get_products_options( $type = '', $do_list_variations = false ) {
			$settings     = array();
			$desc_include = ( '' === $type ?
				__( 'Only check min/max amounts if there are selected product(s) in cart.', 'order-minimum-amount-for-woocommerce' ) :
				__( 'Include in cart total.', 'order-minimum-amount-for-woocommerce' ) );
			$desc_exclude = ( '' === $type ?
				__( 'Do not check min/max amounts if there are selected product(s) in cart.', 'order-minimum-amount-for-woocommerce' ) :
				__( 'Exclude from cart total.', 'order-minimum-amount-for-woocommerce' ) );
			$options_data = array(
				'product'     => array(
					'title' => __( 'Individual Products', 'order-minimum-amount-for-woocommerce' ),
					'data'  => $this->get_products( $do_list_variations ),
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
						'title' => $data['title'],
						'type'  => 'title',
						'id'    => "alg_wc_oma_products_{$id}{$type}_options",
					),
					array(
						'title'    => __( 'Require', 'order-minimum-amount-for-woocommerce' ),
						'desc_tip' => $desc_include,
						'id'       => "alg_wc_oma_{$id}_include{$type}",
						'default'  => array(),
						'type'     => 'multiselect',
						'class'    => 'chosen_select',
						'options'  => $this->add_current_values( $data['data'], "alg_wc_oma_{$id}_include{$type}", $id ),
					),
					array(
						'title'    => __( 'Exclude', 'order-minimum-amount-for-woocommerce' ),
						'desc_tip' => $desc_exclude,
						'id'       => "alg_wc_oma_{$id}_exclude{$type}",
						'default'  => array(),
						'type'     => 'multiselect',
						'class'    => 'chosen_select',
						'options'  => $this->add_current_values( $data['data'], "alg_wc_oma_{$id}_exclude{$type}", $id ),
					),
					array(
						'type' => 'sectionend',
						'id'   => "alg_wc_oma_products_{$id}{$type}_options",
					),
				) );
			}
			return $settings;
		}

		/**
		 * get_priority_options.
		 *
		 * @version 4.0.0
		 * @since   4.0.0
		 */
		function get_priority_options( $id, $default ) {
			return array(
				array(
					'title' => __( 'Advanced: Priority Options', 'order-minimum-amount-for-woocommerce' ),
					'desc'  => __( 'This section sets the order in which min/max amounts are applied.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					           sprintf( __( 'For example, by default "Shipping" section amounts (priority %s) are applied first, and only then "User Roles" section amounts (priority %s) are applied.', 'order-minimum-amount-for-woocommerce' ),
						           '<code>30</code>', '<code>100</code>' ) . ' ' .
					           __( 'You can change this here.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					           __( 'Sections with lower "Priority" numbers are applied first.', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'id'    => "{$id}_options",
				),
				array(
					'title'   => __( 'Priority', 'order-minimum-amount-for-woocommerce' ),
					'id'      => $id,
					'default' => $default,
					'type'    => 'number',
				),
				array(
					'type' => 'sectionend',
					'id'   => "{$id}_options",
				),
			);
		}

		/**
		 * custom_admin_inline_styles.
		 *
		 * @version 4.6.6
		 * @since   4.6.6
		 *
		 * @return void
		 */
		function custom_admin_inline_styles() {
			if ( isset( $_GET['page'] ) && $_GET['page'] === 'wc-settings' && isset( $_GET['tab'] ) && $_GET['tab'] === 'alg_wc_oma' ) {
				?>
				<style>
                    .alg-wc-oma-section-notes {
                        list-style: inside;
                        margin-top: 0;
                        margin-bottom:25px;
                        background:#fff;
                        padding:12px 12px 6px 12px;
                        border:1px solid #c3c4c7
                    }

                    .alg-wc-oma-section-notes-header {
                        background: #fff;
                        border-left: 1px solid #c3c4c7;
                        border-top: 1px solid #c3c4c7;
                        border-right: 1px solid #c3c4c7;
                        padding: 7px 6px 7px 7px;
                        font-size: 14px;
                        line-height: 1.4;
                        font-weight: 600;
                        margin: 25px 0 0 0;
                    }

                    .alg-wc-oma-section-notes-header .dashicons {
                        margin: 0 2px 0 0;
                    }
				</style>
				<?php
			}
		}

		/**
		 * section_notes.
		 *
		 * @version 4.6.6
		 * @since   4.6.6
		 *
		 * @param $items
		 * @param $args
		 *
		 * @return string
		 */
		function section_notes( $items, $args = null ) {
			$output = '<div class="alg-wc-oma-section-notes-header"><span class="dashicons dashicons-info"></span> '.__('Notes','product-quantity-for-woocommerce').'</div>';
			$output .= $this->array_to_html_list_items( $items, array(
				'wrap_on_ul' => true,
				'ul_class' => 'alg-wc-oma-section-notes',
				'ul_style'   => ''
			) );
			return $output;
		}

		/**
		 * array_to_html_list_items.
		 *
		 * @version 4.6.6
		 * @since   4.6.6
		 *
		 * @param         $items
		 * @param   null  $args
		 *
		 * @return string
		 */
		function array_to_html_list_items( $items, $args = null ) {
			$args       = wp_parse_args( $args, array(
				'wrap_on_ul' => true,
				'ul_class'   => 'alg-wc-oma-array-to-list',
				'ul_style'   => ''
			) );
			$ul_class   = $args['ul_class'];
			$wrap_on_ul = $args['wrap_on_ul'];
			$ul_style   = $args['ul_style'];
			$output     = '';
			if ( is_array( $items ) ) {
				$output .= $wrap_on_ul ? '<ul class="' . esc_attr( $ul_class ) . '" style="' . wp_kses_post( $ul_style ) . '">' : '';
				$output .= '<li>' . implode( '</li><li>', array_map( 'wp_kses_post', $items ) ) . '</li>';
				$output .= $wrap_on_ul ? '</ul>' : '';
			}

			return $output;
		}

	}

endif;
