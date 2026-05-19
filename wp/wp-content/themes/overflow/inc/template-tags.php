<?php
/**
 * Template Tags
 *
 * Functions that are called directly from template parts or within actions.
 *
 * @package Overflow
 */

if ( ! function_exists( 'csco_page_pagination' ) ) {
	/**
	 * Post Pagination
	 */
	function csco_page_pagination() {
		if ( ! is_singular() ) {
			return;
		}

		do_action( 'csco_pagination_before' );

		wp_link_pages(
			array(
				'before'           => '<div class="navigation pagination"><div class="nav-links">',
				'after'            => '</div></div>',
				'link_before'      => '<span class="page-number">',
				'link_after'       => '</span>',
				'next_or_number'   => 'next_and_number',
				'separator'        => ' ',
				'nextpagelink'     => esc_html__( 'Next page', 'overflow' ),
				'previouspagelink' => esc_html__( 'Previous page', 'overflow' ),
			)
		);

		do_action( 'csco_pagination_after' );
	}
}

if ( ! function_exists( 'csco_the_post_format_icon' ) ) {
	/**
	 * Post Format Icon
	 *
	 * @param string $content After content.
	 */
	function csco_the_post_format_icon( $content = null ) {
		$post_format = get_post_format();

		if ( 'gallery' === $post_format ) {
			$attachments = count( (array) get_children( array(
				'post_parent' => get_the_ID(),
				'post_type'   => 'attachment',
			) ) );

			$content = $attachments ? sprintf( '<span>%s</span>', $attachments ) : '';
		}

		if ( $post_format ) {
			?>
			<span class="post-format-icon">
				<a class="cs-format-<?php echo esc_attr( $post_format ); ?>" href="<?php the_permalink(); ?>">
					<?php echo wp_kses( $content, 'post' ); ?>
				</a>
			</span>
			<?php
		}
	}
}

if ( ! function_exists( 'csco_single_tags' ) ) {
	/**
	 * Page Tags
	 */
	function csco_single_tags() {
		if ( ! is_single() ) {
			return;
		}

		if ( false === get_theme_mod( 'post_tags', true ) ) {
			return;
		}

		$tag = apply_filters( 'csco_section_title_tag', 'h5' );
		the_tags( '<section class="post-tags"><' . esc_html( $tag ) . ' class="title-block title-tags">' . esc_html__( 'Related Tags', 'overflow' ) . '</' . esc_html( $tag ) . '><ul><li>', '</li><li>', '</li></ul></section>' );
	}
}

if ( ! function_exists( 'csco_archive_post_description' ) ) {
	/**
	 * Post Description in Archive Pages
	 */
	function csco_archive_post_description() {
		$description = get_the_archive_description();
		if ( $description ) {
			?>
			<div class="archive-description">
				<?php echo do_shortcode( $description ); ?>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'csco_archive_post_count' ) ) {
	/**
	 * Post Count in Archive Pages
	 */
	function csco_archive_post_count() {
		global $wp_query;
		$found_posts = $wp_query->found_posts;
		?>
		<div class="archive-count">
			<?php
			/* translators: 1: Singular, 2: Plural. */
			echo esc_html( apply_filters( 'csco_article_full_count', sprintf( _n( '%s post', '%s posts', $found_posts, 'overflow' ), $found_posts ), $found_posts ) );
			?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'csco_post_media_large' ) ) {
	/**
	 * Post Media Large
	 */
	function csco_post_media_large() {
		if ( ! is_singular() ) {
			return;
		}

		if ( 'large' === csco_get_page_header_type() && has_post_thumbnail() ) {
			?>
			<section class="post-media post-media-large">
				<div class="cs-overlay cs-overlay-ratio cs-ratio-wide cs-overlay-transparent">
					<div class="cs-overlay-background">
						<?php
							the_post_thumbnail( 'csco-extra-large', array(
								'class' => 'pk-lazyload-disabled',
							) );
						?>
						<?php csco_get_video_background( 'large-header' ); ?>
						<span class="cs-overlay-blank"></span>
					</div>
				</div>
			</section>
			<?php
		}
	}
}

if ( ! function_exists( 'csco_post_media' ) ) {
	/**
	 * Post Media
	 */
	function csco_post_media() {

		if ( is_singular() && 'standard' !== csco_get_page_header_type() ) {
			return;
		}

		if ( ! has_post_thumbnail() ) {
			return;
		}

		do_action( 'csco_post_media_before' );

		$caption = get_the_post_thumbnail_caption();

		$image_size = 'csco-medium';

		if ( 'disabled' === csco_get_page_sidebar() ) {
			$image_size = 'csco-large';
		}

		if ( 'uncropped' === csco_get_page_preview() ) {
			$image_size = sprintf( '%s-uncropped', $image_size );
		}

		if ( is_singular() ) {
			$full_image = wp_get_attachment_image_url( get_post_thumbnail_id(), 'full' );
			?>
			<div class="post-media">
				<figure <?php echo (string) $caption ? 'class="wp-caption"' : ''; ?>>
					<a href="<?php echo esc_url( $full_image ); ?>">
						<?php
							the_post_thumbnail( $image_size, array(
								'class' => 'pk-lazyload-disabled',
							) );
						?>
					</a>
					<?php if ( $caption ) { ?>
						<figcaption class="wp-caption-text"><?php the_post_thumbnail_caption(); ?></figcaption>
					<?php } ?>
				</figure>
			</div>
			<?php
		} else {
			?>
			<div class="post-media">
				<figure>
					<a href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( $image_size ); ?>
					</a>
				</figure>
			</div>
			<?php

		}
		do_action( 'csco_post_media_after' );
	}
}

if ( ! function_exists( 'csco_post_author' ) ) {
	/**
	 * Post Author Details
	 *
	 * @param int $id Author ID.
	 */
	function csco_post_author( $id = null ) {
		if ( ! $id ) {
			$id = get_the_author_meta( 'ID' );
		}

		$tag = apply_filters( 'csco_section_title_tag', 'h5' );
		?>
		<div class="author-wrap">
			<div class="author">
				<div class="author-avatar">
					<a href="<?php echo esc_url( get_author_posts_url( $id ) ); ?>" rel="author">
						<?php echo get_avatar( $id, '120' ); ?>
					</a>
				</div>
				<div class="author-description">
					<<?php echo esc_html( $tag ); ?> class="title-author">
						<span class="fn">
							<a href="<?php echo esc_url( get_author_posts_url( $id ) ); ?>" rel="author">
								<?php the_author_meta( 'display_name', $id ); ?>
							</a>
						</span>
					</<?php echo esc_html( $tag ); ?>>
					<p class="note"><?php the_author_meta( 'description', $id ); ?></p>
					<?php
					if ( csco_powerkit_module_enabled( 'social_links' ) ) {
						powerkit_author_social_links( $id );
					}
					?>
				</div>
			</div>
		</div>
	<?php
	}
}

if ( ! function_exists( 'csco_wrap_entry_content' ) ) {
	/**
	 * Wrap .entry-content content in div with a class.
	 *
	 * Used for floated share buttons on single posts.
	 */
	function csco_wrap_entry_content() {
		if ( 'post' !== get_post_type() ) {
			return;
		}

		if ( 'csco_post_content_before' === current_filter() ) {
			$tag = apply_filters( 'csco_share_title_tag', 'h5' );
			?>
			<div class="entry-container">
				<?php
				if ( is_singular( 'post' ) && csco_powerkit_module_enabled( 'share_buttons' ) ) {

					if ( powerkit_share_buttons_exists( 'post_sidebar' ) && csco_has_post_meta( 'shares' ) ) {
					?>
						<div class="entry-sidebar-wrap">
							<div class="entry-sidebar">
								<div class="post-sidebar-shares">
									<div class="post-sidebar-inner">
										<<?php echo esc_html( $tag ); ?> class="title-block">
											<?php esc_html_e( 'Share', 'overflow' ); ?>
										</<?php echo esc_html( $tag ); ?>>

										<?php powerkit_share_buttons_location( 'post_sidebar' ); ?>
									</div>
								</div>
							</div>
						</div>
					<?php
					}
				}
		} else {
			?>
			</div>
			<?php
		}
	}
}
