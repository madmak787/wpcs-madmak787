<?php
/**
 * The admin functionality of the plugin.
 *
 * @link       http://khanamir.me/
 * @since      1.0.0
 *
 * @package    Madmak
 * @subpackage Madmak/classes
 */

if ( ! class_exists( 'RTCAMP_Admin' ) && defined( 'ABSPATH' ) ) {

	/**
	 * RTCAMP_Admin loader class.
	 */
	class RTCAMP_Admin {

		/**
		 * Hook into the appropriate actions when the class is constructed.
		 */
		public function __construct() {
			add_action( 'add_meta_boxes', array( $this, 'rtcamp_add_meta_box' ) );
			add_action( 'save_post', array( $this, 'rtcamp_save' ) );
		}

		/**
		 * Adds the meta box container.
		 *
		 * @param string $post_type The Post Type.
		 */
		public function rtcamp_add_meta_box( $post_type ) {
			add_meta_box(
				'rtcamp-contributers',
				__( 'Contributors', 'madmak' ),
				array( $this, 'contributors_meta_box' ),
				'post',
				'side'
			);
		}

		/**
		 * Save the meta when the post is saved.
		 *
		 * @param int $post_id The ID of the post being saved.
		 */
		public function rtcamp_save( $post_id ) {
			// Check if our nonce is set.
			if ( ! isset( $_POST['rtcamp_contributers_list_nonce'] ) ) {
				return $post_id;
			}

			$nonce = sanitize_text_field( wp_unslash( $_POST['rtcamp_contributers_list_nonce'] ) );

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $nonce, 'rtcamp_contributers_list' ) ) {
				return $post_id;
			}

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}

			// Sanitize the user input.
			$rt_contibuters = isset( $_POST['rt_contibuters'] ) ? $_POST['rt_contibuters'] : ''; // phpcs:ignore
			// Update the meta field.
			update_post_meta( $post_id, '_post_contributers', $rt_contibuters );
		}

		/**
		 * Render Meta Box content.
		 *
		 * @param WP_Post $post The post object.
		 */
		public function contributors_meta_box( $post ) {

			// Add an nonce field so we can check for it later.
			wp_nonce_field( 'rtcamp_contributers_list', 'rtcamp_contributers_list_nonce' );

			// Use get_post_meta to retrieve an existing value from the database.
			$rt_contibuters = get_post_meta( $post->ID, '_post_contributers', true );
			// Get all author lists.
			$authors = wp_cache_get( 'rt_get_contributers' );
			if ( false === $authors ) {
				global $wpdb;
				$authors = $wpdb->get_results( "SELECT ID, display_name from $wpdb->users ORDER BY display_name" ); // phpcs:ignore
				wp_cache_set( 'rt_get_contributers', $authors );
			}

			// Display selected contributers.
			foreach ( $authors as $author ) {
				$checked = in_array( $author->ID, $rt_contibuters, true ) ? 'checked="checked"' : '';
				printf( '<div><input type="checkbox" name="rt_contibuters[]" value="' . esc_html__( "%d", 'madmak' ) . '" ' . esc_html__( "%s", 'madmak' ) . ' /><label>' . esc_html__( "%s", 'madmak' ) . '</label></div>', $author->ID, $checked, $author->display_name ); // phpcs:ignore
			}
		}
	}

	/**
	 * Instantiate loader class.
	 *
	 * @since 1.0.0
	 */
	new RTCAMP_Admin();
}
