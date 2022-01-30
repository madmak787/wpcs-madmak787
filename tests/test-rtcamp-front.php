<?php
/**
 * Class Test_RTCAMP_Front
 *
 * @package Post_Contributers
 */

/**
 * Plugin test case.
 */
class Test_RTCAMP_Front extends WP_UnitTestCase {

	/**
	 * A single construct test.
	 */
	public function test_construct() {
		$rtcamp_front  = new RTCAMP_Front();
		$is_registered = has_action( 'the_content', array( $rtcamp_front, 'show_rt_contributers' ) );

		$is_registered = ( 10 === $is_registered );
		$this->assertTrue( $is_registered );
	}


	/**
	 * A single construct test.
	 */
	public function test_show_rt_contributers() {
		global $wp_query;
		$content      = 'Test Content';
		$rtcamp_front = new RTCAMP_Front();

		// Create test users.
		$post_id = $this->factory->post->create(
			array(
				'post_status'  => 'publish',
				'post_title'   => 'Test Post Title',
				'post_content' => 'Test Post Content',
			)
		);

		// Create test users.
		$user_ids = $this->factory->user->create_many( 2 );

		// Add contributers.
		update_post_meta( $post_id, '_post_contributers', $user_ids );

		// Reset wp query and set params as this.
		$wp_query = new WP_Query(
			array(
				'post__in'       => array( $post_id ),
				'posts_per_page' => 1,
			)
		);

		if ( $wp_query->have_posts() ) {
			while ( $wp_query->have_posts() ) {
				$wp_query->the_post();

				$wp_query->is_single = true;

				$filtered_output = $rtcamp_front->show_rt_contributers( $content );
				$class_pos       = strpos( $filtered_output, 'rtcamp_contributers' );

				$this->assertTrue( false !== $class_pos );
			}
		}

	}
}
