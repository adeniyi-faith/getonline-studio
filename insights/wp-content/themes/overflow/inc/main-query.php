<?php
/**
 * Processing the main query.
 *
 * @package Overflow
 */

if ( ! function_exists( 'csco_exclude_featured_posts_from_homepage_query' ) ) {
	/**
	 * Exclude Featured Posts from the Main Query
	 *
	 * @param array $query Default query.
	 * @param bool  $setup Сhange the current query.
	 */
	function csco_exclude_featured_posts_from_homepage_query( $query, $setup = true ) {
		if ( is_admin() ) {
			return $query;
		}

		if ( false === get_theme_mod( 'featured_posts', false ) ) {
			return $query;
		}

		if ( false === get_theme_mod( 'featured_posts_exclude', false ) && true === $setup ) {
			return $query;
		}

		if ( ! ( $query->get( 'page_id' ) === get_option( 'page_on_front' ) || $query->is_home ) ) {
			return $query;
		}

		if ( $query->get( 'page_id' ) === get_option( 'page_on_front' ) && 'page' === get_option( 'show_on_front', 'posts' ) && 'home' === get_theme_mod( 'featured_posts_location', 'front_page' ) ) {
			return $query;
		}

		if ( $query->is_home && 'page' === get_option( 'show_on_front', 'posts' ) && 'front_page' === get_theme_mod( 'featured_posts_location', 'front_page' ) ) {
			return $query;
		}

		if ( ! $query->is_main_query() ) {
			return $query;
		}

		$ids = csco_get_homepage_posts_ids();

		if ( ! $ids ) {
			return $query;
		}

		// Return only ids.
		if ( false === $setup ) {
			return $ids;
		}

		$query->set( 'post__not_in', array_merge( $query->get( 'post__not_in' ), $ids ) );

		return $query;
	}
}
add_action( 'pre_get_posts', 'csco_exclude_featured_posts_from_homepage_query' );

if ( ! function_exists( 'csco_exclude_featured_posts_from_category_query' ) ) {
	/**
	 * Exclude Featured Posts from category the Main Query
	 *
	 * @param array $query Default query.
	 * @param bool  $setup Сhange the current query.
	 */
	function csco_exclude_featured_posts_from_category_query( $query, $setup = true ) {
		if ( is_admin() ) {
			return $query;
		}

		if ( false === get_theme_mod( 'category_featured_posts', false ) ) {
			return $query;
		}

		if ( false === get_theme_mod( 'category_featured_posts_exclude', false ) && true === $setup ) {
			return $query;
		}

		if ( ! $query->is_category ) {
			return $query;
		}

		if ( ! $query->is_main_query() ) {
			return $query;
		}

		$ids = csco_get_category_posts_ids();

		if ( ! $ids ) {
			return $query;
		}

		// Return only ids.
		if ( false === $setup ) {
			return $ids;
		}

		$query->set( 'post__not_in', array_merge( $query->get( 'post__not_in' ), $ids ) );

		return $query;
	}
}
add_action( 'pre_get_posts', 'csco_exclude_featured_posts_from_category_query' );

if ( ! function_exists( 'csco_exclude_trending_posts_from_homepage_query' ) ) {
	/**
	 * Exclude Trending Posts from the Main Query
	 *
	 * @param array $query Default query.
	 * @param bool  $setup Сhange the current query.
	 */
	function csco_exclude_trending_posts_from_homepage_query( $query, $setup = true ) {
		if ( is_admin() ) {
			return $query;
		}

		if ( false === get_theme_mod( 'trending_featured_posts', false ) ) {
			return $query;
		}

		if ( false === get_theme_mod( 'trending_featured_posts_exclude', false ) && true === $setup ) {
			return $query;
		}

		if ( ! ( $query->get( 'page_id' ) === get_option( 'page_on_front' ) || $query->is_home ) ) {
			return $query;
		}

		if ( $query->get( 'page_id' ) === get_option( 'page_on_front' ) && 'page' === get_option( 'show_on_front', 'posts' ) && 'home' === get_theme_mod( 'trending_featured_posts_location', 'front_page' ) ) {
			return $query;
		}

		if ( $query->is_home && 'page' === get_option( 'show_on_front', 'posts' ) && 'front_page' === get_theme_mod( 'trending_featured_posts_location', 'front_page' ) ) {
			return $query;
		}

		if ( ! $query->is_main_query() ) {
			return $query;
		}

		$ids = csco_get_trending_posts_ids();

		if ( ! $ids ) {
			return $query;
		}

		// Return only ids.
		if ( false === $setup ) {
			return $ids;
		}

		$query->set( 'post__not_in', array_merge( $query->get( 'post__not_in' ), $ids ) );

		return $query;
	}
}
add_action( 'pre_get_posts', 'csco_exclude_trending_posts_from_homepage_query' );
