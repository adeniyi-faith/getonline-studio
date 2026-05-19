<?php

namespace IGD;

class Shortcode_Locations {

	private static $instance = null;

	public function __construct() {
		// Monitoring hooks.
		add_action( 'save_post', [ $this, 'handle_post_save' ], 10, 3 );
		add_action( 'post_updated', [ $this, 'handle_post_update' ], 10, 3 );
		add_action( 'wp_trash_post', [ $this, 'handle_post_trash' ] );
		add_action( 'untrash_post', [ $this, 'handle_post_untrash' ] );
		add_action( 'delete_post', [ $this, 'handle_post_trash' ] );
	}

	/**
	 * Handle post save (new post or update).
	 */
	public function handle_post_save( $post_ID, $post, $update ) {
		// Ensure valid post type and status
		if ( ! $this->is_valid_post( $post ) ) {
			return;
		}

		$shortcode_ids = $this->extract_shortcode_ids( $post->post_content );
		$this->update_shortcode_locations( $post, [], $shortcode_ids );
	}

	/**
	 * Handle post update.
	 */
	public function handle_post_update( $post_id, $post_after, $post_before ) {
		// Ensure valid post type and status
		if ( ! $this->is_valid_post( $post_after ) ) {
			return;
		}

		$shortcode_ids_before = $this->extract_shortcode_ids( $post_before->post_content );
		$shortcode_ids_after  = $this->extract_shortcode_ids( $post_after->post_content );

		$this->update_shortcode_locations( $post_after, $shortcode_ids_before, $shortcode_ids_after );
	}

	/**
	 * Handle post deletion (trash).
	 */
	public function handle_post_trash( $post_id ) {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return;
		}

		$shortcode_ids = $this->extract_shortcode_ids( $post->post_content );
		$this->update_shortcode_locations( $post, $shortcode_ids, [] );
	}

	/**
	 * Handle post untrash (restore).
	 */
	public function handle_post_untrash( $post_id ) {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return;
		}

		$shortcode_ids = $this->extract_shortcode_ids( $post->post_content );
		$this->update_shortcode_locations( $post, [], $shortcode_ids );
	}

	/**
	 * Updates shortcode locations in the database.
	 */
	private function update_shortcode_locations( $post, $shortcode_ids_before, $shortcode_ids_after ) {
		global $wpdb;

		$table = $wpdb->prefix . 'integrate_google_drive_shortcodes';
		$post_id = $post->ID;
		$url = get_permalink( $post_id );
		$url = ( $url === false || is_wp_error( $url ) ) ? '' : $url;

		$shortcode_ids_to_remove = array_diff( $shortcode_ids_before, $shortcode_ids_after );
		$shortcode_ids_to_add    = array_diff( $shortcode_ids_after, $shortcode_ids_before );

		foreach ( $shortcode_ids_to_remove as $shortcode_id ) {
			$locations = $this->get_existing_locations( $shortcode_id, $post_id );

			$wpdb->update(
				$table,
				[ 'locations' => maybe_serialize( $locations ) ],
				[ 'id' => $shortcode_id ],
				[ '%s' ],
				[ '%d' ]
			);
		}

		foreach ( $shortcode_ids_to_add as $shortcode_id ) {
			$locations = $this->get_existing_locations( $shortcode_id, $post_id );

			$locations[] = [
				'type'         => $post->post_type,
				'title'        => $post->post_title,
				'shortcode_id' => $shortcode_id,
				'post_id'      => $post_id,
				'status'       => $post->post_status,
				'url'          => $url,
			];

			$wpdb->update(
				$table,
				[ 'locations' => maybe_serialize( $locations ) ],
				[ 'id' => $shortcode_id ],
				[ '%s' ],
				[ '%d' ]
			);
		}
	}

	/**
	 * Check if a post is valid for processing.
	 */
	private function is_valid_post( $post ) {
		return in_array( $post->post_type, $this->get_post_types(), true ) &&
		       in_array( $post->post_status, $this->get_post_statuses(), true );
	}

	/**
	 * Get allowed post types.
	 */
	private function get_post_types() {
		$args = [
			'public'             => true,
			'publicly_queryable' => true,
		];
		$post_types = get_post_types( $args, 'names' );

		unset( $post_types['attachment'] );

		// Include custom template post types
		$post_types[] = 'wp_template';
		$post_types[] = 'wp_template_part';

		return $post_types;
	}

	/**
	 * Get allowed post statuses.
	 */
	private function get_post_statuses() {
		return [ 'publish', 'pending', 'draft', 'future', 'private' ];
	}

	/**
	 * Extract shortcode IDs from content.
	 */
	private function extract_shortcode_ids( $content ) {
		$shortcode_ids = [];

		if (
			preg_match_all(
				'#\[\s*integrate_google_drive.+id\s*=\s*"(\d+?)".*]|<!-- wp:igd/shortcodes {"id":(\d+).*?} /-->#',
				$content,
				$matches
			)
		) {
			array_shift( $matches );
			$shortcode_ids = array_map(
				'intval',
				array_unique( array_filter( array_merge( ...$matches ) ) )
			);
		}

		return $shortcode_ids;
	}

	/**
	 * Get shortcode locations excluding the current post.
	 */
	private function get_existing_locations( $shortcode_id, $post_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'integrate_google_drive_shortcodes';

		$locations = $wpdb->get_var( $wpdb->prepare( "SELECT locations FROM $table WHERE id = %d", $shortcode_id ) );
		$locations = ! empty( $locations ) ? maybe_unserialize( $locations ) : [];

		if ( ! is_array( $locations ) ) {
			$locations = [];
		}

		// Remove current post from locations
		return array_values( array_filter( $locations, static fn( $location ) => $location['post_id'] !== $post_id ) );
	}

	/**
	 * Get singleton instance.
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}

// Initialize the class
Shortcode_Locations::instance();
