<?php
/**
 * Order Minimum Amount for WooCommerce - Shortcodes Class
 *
 * @version 4.6.5
 * @since   4.0.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Shortcodes' ) ) :

	class Alg_WC_OMA_Shortcodes {

		/**
		 * Constructor.
		 *
		 * @version 4.1.3
		 * @since   4.0.0
		 */
		function __construct() {
			if ( 'yes' === get_option( 'alg_wc_oma_plugin_enabled', 'yes' ) ) {
				add_shortcode( 'alg_wc_oma_translate', array( $this, 'language_shortcode' ) );
			}
		}

		/**
		 * language_shortcode.
		 *
		 * For WPML and Polylang plugins.
		 *
		 * @version 4.6.5
		 * @since   1.2.1
		 */
		function language_shortcode( $atts, $content = '' ) {
			// E.g.: `[alg_wc_oma_translate lang="DE" lang_text="Text for DE" not_lang_text="Text for other languages"]`
			if ( isset( $atts['lang_text'] ) && isset( $atts['not_lang_text'] ) && ! empty( $atts['lang'] ) ) {
				return ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ?
					esc_html( $atts['not_lang_text'] ) : esc_html( $atts['lang_text'] );
			}

			// E.g.: `[alg_wc_oma_translate lang="DE"]Text for DE[/alg_wc_oma_translate][alg_wc_oma_translate lang="NL"]Text for NL[/alg_wc_oma_translate][alg_wc_oma_translate not_lang="DE,NL"]Text for other languages[/alg_wc_oma_translate]`
			return (
				( ! empty( $atts['lang'] ) && ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ) ||
				( ! empty( $atts['not_lang'] ) && defined( 'ICL_LANGUAGE_CODE' ) && in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['not_lang'] ) ) ) ) )
			) ? '' : esc_html( $content );
		}

	}

endif;

return new Alg_WC_OMA_Shortcodes();
