<?php
/**
 * The frontend functionality of the plugin.
 *
 * @link       http://khanamir.me/
 * @since      1.0.0
 *
 * @package    Madmak
 * @subpackage Madmak/classes
 */

if ( ! class_exists( 'RTCAMP_Front' ) && defined( 'ABSPATH' ) ) {

	/**
	 * RTCAMP_Front loader class.
	 */
	class RTCAMP_Front {

		/**
		 * Hook into the appropriate actions when the class is constructed.
		 */
		public function __construct() {
			add_filter( 'the_content', array( $this, 'show_rt_contributers' ) );
		}

		/**
		 * Display a box called "Contributors".
		 *
		 * @param string $content The Post Content.
		 */
		public function show_rt_contributers( $content ) {
			if ( is_single() ) {
				global $post;
				// Use get_post_meta to retrieve an existing value from the database.
				$rt_contibuters = get_post_meta( $post->ID, '_post_contributers', true );
				if ( is_array( $rt_contibuters ) ) {
					$html  = '<div class="rtcamp_contributers"><h2>Contributors</h2>';
					$html .= '<ul>';
					foreach ( $rt_contibuters as $author_id ) {
						$author = get_user_by( 'ID', $author_id );
						$avatar = get_avatar( $author_id );
						$html  .= '<li>' . get_avatar( $author_id ) . ' <a href="' . site_url( 'author/' . $author->user_nicename ) . '">' . $author->display_name . '</a></li>';
					}
					$html .= '</ul>';
					$html .= '</div>';
					return $content . $html;
				}
				return $content;
			}
		}

	}

	/**
	 * Instantiate loader class.
	 *
	 * @since 1.0.0
	 */
	new RTCAMP_Front();
}
