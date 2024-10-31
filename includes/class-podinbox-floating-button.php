<?php
/**
 * The PodInbox_Floating_Button class.
 *
 * @package PodInbox
 * @author  PodInbox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'PodInbox_Floating_Button' ) ) :

	/**
	 * PodInbox floating buttons.
	 *
	 * Handles adding PodInbox floating button on sites.
	 *
	 * @since 1.0.0
	 */
	class PodInbox_Floating_Button {
		/**
		 * The plugin settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $settings;

		/**
		 * The constructor.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			// Get PodInbox plugin settings.
			$this->settings = podinbox_get_options();

			$enabled          = podinbox_get_option( 'enable_floating_button', $this->settings );
			$script_placement = podinbox_get_option( 'script_placement', $this->settings );

			// Checks if the plugin is enabled.
			if ( $enabled && 'yes' === $enabled ) {
				switch ( $script_placement ) {
					case 'header':
						add_action( 'wp_head', array( $this, 'insert_widget_place' ), 10 );
						break;

					case 'body_open':
						add_action( 'wp_body_open', array( $this, 'insert_widget_place' ), 10 );
						break;

					case 'footer':
						add_action( 'wp_footer', array( $this, 'insert_widget_place' ), 40 );
						break;

					default:
						add_action( 'wp_head', array( $this, 'insert_widget_place' ), 10 );
						break;
				}

				add_action( 'wp_enqueue_scripts', array( $this, 'styles' ) );
			}
		}

		/**
		 * Insert widget code in the selected place - whether in header, after body open or footer.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function insert_widget_place() {
			$show_id = podinbox_get_option( 'show_id', $this->settings );

			/*
			 * Prints PodInbox embed widget code.
			 */
			$show_id_script = 'var podinboxFloatingWidget = "' . esc_attr( $show_id ) . '";';
			wp_print_inline_script_tag( $show_id_script );
			wp_print_script_tag(
				array(
					'type'            => 'text/javascript',
					'src'             => esc_url( 'https://podinbox.com/widget.js' ),
					'podinbox-origin' => esc_url( 'https://podinbox.com' ),
					'async'           => true,
				)
			);
		}

		/**
		 * Enqueue generated inline styles.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function styles() {
			// Adds inline css if display option is not for both desktop and mobile devices.
			if ( 'both' !== podinbox_get_option( 'display_device', $this->settings ) ) {
				/**
				 * Include generated CSS.
				 */
				include_once PODINBOX_ROOT_PATH . '/includes/styles.php';
				wp_register_style( 'podinbox_generated_css', false, array(), PODINBOX_PLUGIN_VERSION );
				wp_enqueue_style( 'podinbox_generated_css' );
				wp_add_inline_style( 'podinbox_generated_css', $generated_css );
			}
		}
	}

	return new PodInbox_Floating_Button();

endif;
