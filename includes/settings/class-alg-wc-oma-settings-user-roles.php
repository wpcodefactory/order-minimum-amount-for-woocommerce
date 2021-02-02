<?php
/**
 * Order Minimum Amount for WooCommerce - User Roles Section Settings
 *
 * @version 3.2.0
 * @since   1.2.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_User_Roles' ) ) :

class Alg_WC_OMA_Settings_User_Roles extends Alg_WC_OMA_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function __construct() {
		$this->id   = 'user_roles';
		$this->desc = __( 'User Roles', 'order-minimum-amount-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.2.0
	 * @since   1.2.0
	 * @todo    [maybe] Enabled user roles: default to some roles only, e.g. 'guest', 'administrator', 'customer'?
	 * @todo    [maybe] Enabled user roles: better desc?
	 */
	function get_settings() {

		do_action( 'alg_wc_oma_settings_user_roles' );

		$settings = array(
			array(
				'title'    => __( 'User Roles', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => __( 'Optional amounts per user role.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					alg_wc_oma()->core->get_amounts_desc(),
				'id'       => 'alg_wc_oma_by_user_role_options',
			),
			array(
				'title'    => __( 'Amount by user role', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable section', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_oma_by_user_role_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Enabled user roles', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'Select user roles you want to set different amounts for.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					__( 'If empty - settings for all user roles will be displayed.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					__( '"Save changes" after you update this option.', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_enabled_user_roles',
				'default'  => array(),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => alg_wc_oma()->core->get_all_user_roles( true ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_by_user_role_options',
			),
		);
		$enabled_user_roles = alg_wc_oma()->core->get_enabled_user_roles( true );
		foreach ( $enabled_user_roles as $role_key => $role_title ) {
			$settings = array_merge( $settings, array(
				array(
					'title'    => $role_title,
					'type'     => 'title',
					'id'       => "alg_wc_oma_by_user_role_{$role_key}",
				),
			) );
			foreach ( alg_wc_oma()->core->get_enabled_limits() as $min_or_max ) {
				foreach ( alg_wc_oma()->core->get_enabled_types() as $amount_type ) {
					$settings = array_merge( $settings, array(
						array(
							'title'    => alg_wc_oma()->core->get_title( $min_or_max, $amount_type ),
							'desc_tip' => alg_wc_oma()->core->amounts->get_unit( $amount_type ),
							'id'       => "alg_wc_oma_{$min_or_max}_{$amount_type}_by_user_role[{$role_key}]",
							'default'  => 0,
							'type'     => 'number',
							'custom_attributes' => alg_wc_oma()->core->get_amount_custom_atts(),
						),
					) );
				}
			}
			$settings = array_merge( $settings, array(
				array(
					'type'     => 'sectionend',
					'id'       => "alg_wc_oma_by_user_role_{$role_key}",
				),
			) );
		}

		return $settings;
	}

}

endif;

return new Alg_WC_OMA_Settings_User_Roles();
