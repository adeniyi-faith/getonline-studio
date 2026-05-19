<?php
/**
 * WooCommerce compatibility functions.
 *
 * @package Overflow
 */

if ( class_exists( 'WooCommerce' ) ) {

	/**
	 * Add support WooCommerce.
	 */
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	/**
	 * Get shop header type
	 */
	function csco_wc_shop_header_type() {
		$shop_id = wc_get_page_id( 'shop' );

		$allow = array( 'none', 'standard', 'large', 'title' );

		$page_header = get_post_meta( $shop_id, 'csco_page_header_type', true );

		if ( ! in_array( $page_header, $allow, true ) || 'default' === $page_header ) {
			$page_header = get_theme_mod( 'page_header_type', 'standard' );
		}

		$page_header = apply_filters( 'csco_page_header_type', $page_header );

		if ( 'none' === $page_header ) {
			return 'none';
		}

		$no_paged = in_array( absint( get_query_var( 'paged' ) ), array( 0, 1 ), true );

		if ( ! $no_paged ) {
			$page_header = 'title';
		}

		return $page_header;
	}

	/**
	 * Adds classes to <body> tag
	 *
	 * @param array $classes is an array of all body classes.
	 */
	function csco_wc_body_class( $classes ) {
		if ( is_shop() ) {
			$classes[] = 'woocommerce-shop-header-' . csco_wc_shop_header_type();
		}

		return $classes;
	}
	add_filter( 'body_class', 'csco_wc_body_class' );

	/**
	 * Remove footer posts for WooCommerce.
	 */
	add_action( 'template_redirect', function() {
		if ( is_woocommerce() || is_product_category() || is_product_tag() || is_cart() || is_checkout() ) {
			remove_action( 'csco_footer_before', 'csco_footer_posts' );
		}
	});

	/**
	 * Shop/archives: wrap the product image/thumbnail in a div.
	 *
	 * The product image itself is hooked in at priority 10 using woocommerce_template_loop_product_thumbnail(),
	 * so priority 9 and 11 are used to open and close the div.
	 */
	add_action( 'woocommerce_before_shop_loop_item_title', function() {
		echo '<div class="wc-overlay-background">';
		echo '<span class="cs-bg-dark read-more">' . esc_html__( 'View', 'overflow' ) . '</span>';
	}, 9 );

	add_action( 'woocommerce_before_shop_loop_item_title', function() {
		echo '</div>';
	}, 11 );

	/**
	 * Disable shop page title.
	 */
	add_filter( 'woocommerce_show_page_title', function( $default ) {
		return is_shop() ? false : $default;
	} );

	/**
	 * Add css selectors to output of kirki.
	 */
	add_filter( 'csco_color_primary', function( $rules ) {
		array_push( $rules, array(
			'element'  => csco_implode( array(
				'.woocommerce div.product form.cart button[name="add-to-cart"]',
				'.woocommerce div.product form.cart button[type="submit"]',
				'.woocommerce .widget_shopping_cart .buttons a',
				'.woocommerce .wc-proceed-to-checkout a.checkout-button.alt',
				'.woocommerce ul.products li.product .onsale',
				'.woocommerce #respond input#submit',
				'.woocommerce span.onsale',
				'.woocommerce-cart .return-to-shop a.button',
				'.woocommerce-checkout #payment .button.alt',
			) ),
			'property' => 'background-color',
		) );
		return $rules;
	} );

	add_filter( 'csco_color_primary', function( $rules ) {
		array_push( $rules, array(
			'element'  => csco_implode( array(
				'.woocommerce .woocommerce-pagination .page-numbers li > a:hover',
				'.woocommerce li.product .price a:hover',
				'.woocommerce .star-rating',
			) ),
			'property' => 'color',
		) );
		return $rules;
	} );

	add_filter( 'csco_color_overlay', function( $rules ) {
		array_push( $rules, array(
			'element'  => csco_implode( array(
				'.woocommerce ul.products .wc-overlay-background:after',
			) ),
			'property' => 'background-color',
		) );
		return $rules;
	} );

	add_filter( 'csco_font_headings', function( $rules ) {
		array_push( $rules, array(
			'element' => csco_implode( array(
				'.woocommerce ul.cart_list li a',
				'.woocommerce ul.product_list_widget li a',
				'.woocommerce div.product .woocommerce-tabs ul.tabs li',
				'.woocommerce.widget_products span.product-title',
				'.woocommerce.widget_recently_viewed_products span.product-title',
				'.woocommerce.widget_recent_reviews span.product-title',
				'.woocommerce.widget_top_rated_products span.product-title',
				'.woocommerce-loop-product__title',
				'.woocommerce table.shop_table th',
				'.woocommerce-tabs .panel h2',
				'.related.products > h2',
				'.upsells.products > h2',
			) ),
		) );
		return $rules;
	} );

	add_filter( 'csco_font_secondary', function( $rules ) {
		array_push( $rules, array(
			'element' => csco_implode( array(
				'.navbar-cart .cart-quantity',
				'.widget_shopping_cart .quantity',
				'.woocommerce .widget_layered_nav_filters ul li a',
				'.woocommerce.widget_layered_nav_filters ul li a',
				'.woocommerce.widget_products ul.product_list_widget li',
				'.woocommerce.widget_recently_viewed_products ul.product_list_widget li',
				'.woocommerce.widget_recent_reviews ul.product_list_widget li',
				'.woocommerce.widget_top_rated_products ul.product_list_widget li',
				'.woocommerce .widget_price_filter .price_slider_amount',
				'.woocommerce .woocommerce-result-count',
				'.woocommerce ul.products li.product .price',
				'.woocommerce .woocommerce-breadcrumb',
				'.woocommerce .product_meta',
				'.woocommerce span.onsale',
				'.woocommerce-page .woocommerce-breadcrumb',
				'.woocommerce-mini-cart__total total',
				'.woocommerce-input-wrapper .select2-selection__rendered',
				'.woocommerce table.shop_table.woocommerce-checkout-review-order-table th',
				'.woocommerce table.shop_table.woocommerce-checkout-review-order-table td',
			) ),
		) );
		return $rules;
	} );

	add_filter( 'csco_font_primary', function( $rules ) {
		array_push( $rules, array(
			'element' => csco_implode( array(
				'.woocommerce #respond input#submit',
				'.woocommerce a.button',
				'.woocommerce button.button',
				'.woocommerce input.button',
				'.woocommerce #respond input#submit.alt',
				'.woocommerce a.button.alt',
				'.woocommerce button.button.alt',
				'.woocommerce input.button.alt',
				'.woocommerce-pagination',
				'.woocommerce nav.woocommerce-pagination .page-numbers li > span',
				'.woocommerce nav.woocommerce-pagination .page-numbers li > a',
				'.woocommerce ul.products li.product .button',
				'.woocommerce li.product .price',
				'.woocommerce div.product .woocommerce-tabs ul.tabs li a',
				'.woocommerce-form__label-for-checkbox span',
				'.wc_payment_method.payment_method_bacs label',
				'.wc_payment_method.payment_method_cheque label',
			) ),
		) );
		return $rules;
	} );

	add_filter( 'csco_font_post_content', function( $rules ) {
		array_push( $rules, array(
			'element' => csco_implode( array(
				'.woocommerce-tabs .entry-content',
			) ),
		) );
		return $rules;
	} );

	add_filter( 'csco_font_title_block', function( $rules ) {
		array_push( $rules, array(
			'element' => csco_implode( array(
				'.woocommerce .woocommerce-tabs .panel h2',
				'.woocommerce .related.products > h2',
				'.woocommerce .upsells.products > h2 ',
				'.woocommerce ul.order_details li',
				'.woocommerce-order-details .woocommerce-order-details__title',
				'.woocommerce-customer-details .woocommerce-column__title',
				'.woocommerce-account .addresses .title h3',
				'.woocommerce-checkout h3',
				'.woocommerce-EditAccountForm legend',
				'.cross-sells > h2',
				'.cart_totals > h2',
			) ),
		) );
		return $rules;
	} );

	add_filter( 'csco_design_border_radius', function( $rules ) {
		array_push( $rules, array(
			'element'  => csco_implode( array(
				'.widget_product_search .woocommerce-product-search',
				'.widget_product_search .woocommerce-product-search input[type="search"]',
				'.woocommerce-checkout input[id="coupon_code"]',
				'.woocommerce-cart input[id="coupon_code"]',
				'.woocommerce div.product form.cart input.qty',
				'.woocommerce #respond input#submit',
				'.woocommerce a.button',
				'.woocommerce button.button',
				'.woocommerce input.button',
				'.woocommerce #respond input#submit.alt',
				'.woocommerce a.button.alt',
				'.woocommerce button.button.alt',
				'.woocommerce input.button.alt',
			) ),
			'property' => 'border-radius',
		) );
		return $rules;
	} );

	/**
	 * Add fields to WooCommerce.
	 */
	function csco_wc_add_fields_customizer() {
		CSCO_Kirki::add_section(
			'woocommerce_common_settings',
			array(
				'title'    => esc_html__( 'Common Settings', 'overflow' ),
				'panel'    => 'woocommerce',
				'priority' => 1,
			)
		);

		CSCO_Kirki::add_field(
			'csco_theme_mod',
			array(
				'type'     => 'radio',
				'settings' => 'woocommerce_default_page_sidebar',
				'label'    => esc_html__( 'Default Page Sidebar', 'overflow' ),
				'section'  => 'woocommerce_common_settings',
				'default'  => 'disabled',
				'priority' => 10,
				'choices'  => array(
					'right'    => esc_html__( 'Right Sidebar', 'overflow' ),
					'left'     => esc_html__( 'Left Sidebar', 'overflow' ),
					'disabled' => esc_html__( 'No Sidebar', 'overflow' ),
				),
			)
		);

		CSCO_Kirki::add_field(
			'csco_theme_mod', array(
				'type'     => 'checkbox',
				'settings' => 'woocommerce_product_catalog_cart',
				'label'    => esc_html__( 'Display add to cart buttom', 'overflow' ),
				'section'  => 'woocommerce_product_catalog',
				'default'  => false,
				'priority' => 10,
			)
		);

		CSCO_Kirki::add_section(
			'woocommerce_product_page', array(
				'title'    => esc_html__( 'Product Page', 'overflow' ),
				'panel'    => 'woocommerce',
				'priority' => 30,
			)
		);

		CSCO_Kirki::add_field(
			'csco_theme_mod', array(
				'type'     => 'radio',
				'settings' => 'woocommerce_product_page_sidebar',
				'label'    => esc_html__( 'Default Sidebar', 'overflow' ),
				'section'  => 'woocommerce_product_page',
				'default'  => 'right',
				'priority' => 5,
				'choices'  => array(
					'right'    => esc_attr__( 'Right Sidebar', 'overflow' ),
					'left'     => esc_attr__( 'Left Sidebar', 'overflow' ),
					'disabled' => esc_attr__( 'No Sidebar', 'overflow' ),
				),
			)
		);

		CSCO_Kirki::add_section(
			'woocommerce_product_misc', array(
				'title'    => esc_html__( 'Miscellaneous', 'overflow' ),
				'panel'    => 'woocommerce',
				'priority' => 50,
			)
		);

		CSCO_Kirki::add_field(
			'csco_theme_mod', array(
				'type'     => 'checkbox',
				'settings' => 'woocommerce_header_hide_icon',
				'label'    => esc_html__( 'Hide Cart Icon in Header', 'overflow' ),
				'section'  => 'woocommerce_product_misc',
				'default'  => false,
				'priority' => 10,
			)
		);
	}
	add_action( 'init', 'csco_wc_add_fields_customizer' );

	/**
	 * Woocommerce loop add to cart
	 */
	function csco_wc_shop_loop_item() {
		if ( ! get_theme_mod( 'woocommerce_product_catalog_cart', false ) ) {
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
		}
	}
	add_action( 'template_redirect', 'csco_wc_shop_loop_item' );

	/**
	 * Woocommerce gallery image width
	 */
	function csco_wc_gallery_thumbnail_image_width() {
		add_theme_support( 'woocommerce', array( 'gallery_thumbnail_image_width' => 300 ) );
	}
	add_action( 'template_redirect', 'csco_wc_gallery_thumbnail_image_width' );

	/**
	 * Enqueues WooCommerce assets.
	 */
	function csco_wc_enqueue_scripts() {
		$theme = wp_get_theme();

		$version = $theme->get( 'Version' );

		// Register WooCommerce styles.
		wp_register_style( 'csco_css_wc', csco_style( get_template_directory_uri() . '/css/woocommerce.css' ), array(), $version );

		// Enqueue WooCommerce styles.
		wp_enqueue_style( 'csco_css_wc' );

		// Add RTL support.
		wp_style_add_data( 'csco_css_wc', 'rtl', 'replace' );

		// Remove selectWoo.
		wp_dequeue_style( 'selectWoo' );
		wp_dequeue_script( 'selectWoo' );
	}
	add_action( 'wp_enqueue_scripts', 'csco_wc_enqueue_scripts' );

	/**
	 * PinIt exclude selectors
	 *
	 * @param string $selectors List selectors.
	 */
	function csco_wc_pinit_exclude_selectors( $selectors ) {
		$selectors[] = '.woocommerce .products img';
		$selectors[] = '.woocommerce-product-gallery img';
		$selectors[] = '.woocommerce-cart-form .product-thumbnail img';

		return $selectors;
	}
	add_filter( 'powerkit_pinit_exclude_selectors', 'csco_wc_pinit_exclude_selectors' );

	/**
	 * Get Page Sidebar
	 *
	 * @param string $sidebar Page sidebar.
	 */
	function csco_wc_get_page_sidebar( $sidebar ) {

		if ( is_woocommerce() || is_product_category() || is_product_tag() || is_cart() || is_checkout() || is_account_page() ) {

			global $post;

			if ( is_shop() ) {
				$page_id = wc_get_page_id( 'shop' );
			} elseif ( is_product() || is_page() ) {
				$page_id = $post->ID;
			} else {
				$page_id = 0;
			}

			// Get sidebar for current post.
			$sidebar = get_post_meta( $page_id, 'csco_singular_sidebar', true );

			if ( ! $sidebar || 'default' === $sidebar ) {

				$sidebar = get_theme_mod( 'woocommerce_default_page_sidebar', 'disabled' );

				if ( is_product() ) {
					$sidebar = get_theme_mod( 'woocommerce_product_page_sidebar', 'right' );
				}
			}
		}

		return $sidebar;

	}
	add_filter( 'csco_page_sidebar', 'csco_wc_get_page_sidebar' );

	/**
	 * Register WooCommerce Sidebar
	 */
	function csco_wc_widgets_init() {

		$tag = apply_filters( 'csco_section_title_tag', 'h5' );

		register_sidebar(
			array(
				'name'          => esc_html__( 'WooCommerce', 'overflow' ),
				'id'            => 'sidebar-woocommerce',
				'before_widget' => '<div class="widget %1$s %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="title-block-wrap"><' . $tag . ' class="title-block title-stroke-block title-widget">',
				'after_title'   => '</' . $tag . '></div>',
			)
		);
	}
	add_action( 'widgets_init', 'csco_wc_widgets_init' );

	/**
	 * Overwrite Default Sidebar
	 *
	 * @param string $sidebar Sidebar slug.
	 */
	function csco_wc_sidebar( $sidebar ) {
		if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
			return 'sidebar-woocommerce';
		}
		return $sidebar;
	}

	add_filter( 'csco_sidebar', 'csco_wc_sidebar' );

	/**
	 * Add cart to header
	 */
	function csco_wc_nav_cart() {
		if ( ! get_theme_mod( 'woocommerce_header_hide_icon', false ) ) {

			$quantity = intval( WC()->cart->get_cart_contents_count() );
			?>
			<a class="navbar-cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'overflow' ); ?>">
				<i class="cs-icon cs-icon-bag"></i>
				<?php if ( $quantity ) { ?>
					<span class="cart-quantity"><?php echo esc_html( $quantity ); ?></span>
				<?php } ?>
			</a>
			<?php
		}
	}
	add_action( 'csco_navbar_content', 'csco_wc_nav_cart', 45 );
	add_action( 'csco_navbar_bottombar_right', 'csco_wc_nav_cart', 5 );


	/**
	 * Add location for update nav cart
	 *
	 * @param array $fragments The cart fragments.
	 */
	function csco_wc_update_nav_cart( $fragments ) {

		ob_start();

		csco_wc_nav_cart();

		$fragments['a.navbar-cart'] = ob_get_clean();

		return $fragments;

	}
	add_filter( 'woocommerce_add_to_cart_fragments', 'csco_wc_update_nav_cart', 10, 1 );

	/**
	 * Toc exclude selectors.
	 *
	 * @param string $selectors The selectors.
	 */
	function csco_wc_toc_exclude( $selectors ) {
		$selectors .= '|.woocommerce-loop-product__title';

		return $selectors;
	}
	add_filter( 'pk_toc_exclude', 'csco_wc_toc_exclude' );

	/**
	 * WC Breadcrumbs
	 */
	function csco_wc_breadcrumbs() {
		$display_options = get_option( 'wpseo_titles' );

		if ( ! isset( $display_options['breadcrumbs-enable'] ) ) {
			$display_options['breadcrumbs-enable'] = false;
		}

		if ( is_product_taxonomy() || is_product() || is_cart() || is_checkout() || is_account_page() ) {
			if ( function_exists( 'yoast_breadcrumb' ) && $display_options['breadcrumbs-enable'] ) {
				yoast_breadcrumb( '<section class="cs-breadcrumbs" id="breadcrumbs">', '</section>' );
			} else {
				woocommerce_breadcrumb();
			}
		}
	}
	add_action( 'csco_main_content_before', 'csco_wc_breadcrumbs' );

	/**
	 * Remove default breadcrumbs
	 */
	function csco_wc_remove_breadcrumbs() {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
	}
	add_action( 'template_redirect', 'csco_wc_remove_breadcrumbs' );

	/**
	 * WooCommerce Post Media Large
	 */
	function csco_wc_post_media_large() {
		if ( ! is_shop() ) {
			return;
		}

		$shop_id = wc_get_page_id( 'shop' );

		$shop_header = csco_wc_shop_header_type();

		if ( 'large' === $shop_header && has_post_thumbnail( $shop_id ) ) {
			?>
			<section class="post-media post-media-large">
				<div class="cs-overlay cs-overlay-ratio cs-ratio-wide cs-overlay-transparent">
					<div class="cs-overlay-background">
						<?php
							echo get_the_post_thumbnail( $shop_id, 'csco-extra-large', array(
								'class' => 'pk-lazyload-disabled',
							) );
						?>
						<?php csco_get_video_background( 'large-header', $shop_id ); ?>
					</div>
				</div>
			</section>
			<?php
		}
	}
	add_action( 'csco_site_content_start', 'csco_wc_post_media_large', 100 );

	/**
	 * WooCommerce shop header.
	 */
	function csco_wc_shop_header() {
		if ( is_shop() ) {
			$shop_id = wc_get_page_id( 'shop' );

			$shop_header = csco_wc_shop_header_type();

			if ( 'none' === $shop_header ) {
				return;
			}

			// Class header.
			$class = sprintf( 'entry-header-%s', $shop_header );

			if ( has_post_thumbnail() ) {
				$class .= ' entry-header-thumbnail';
			}

			// Image size.
			$image_size = 'csco-medium';

			if ( 'disabled' === csco_get_page_sidebar() ) {
				$image_size = 'csco-large';
			}

			if ( 'uncropped' === get_theme_mod( 'page_media_preview', 'cropped' ) ) {
				$image_size = sprintf( '%s-uncropped', $image_size );
			}

			if ( 'large' === $shop_header ) {
				$image_size = 'csco-extra-large';
			}
			?>
			<section class="entry-header <?php echo esc_attr( $class ); ?>">

				<div class="entry-header-inner">
					<?php if ( get_the_title( $shop_id ) ) { ?>
						<h1 class="entry-title title-stroke">
							<?php echo get_the_title( $shop_id ); ?>
						</h1>
					<?php } ?>

					<?php
					if ( has_post_thumbnail( $shop_id ) && 'standard' === $shop_header ) {
						?>
						<div class="entry-header-thumbnail">
							<?php
							echo get_the_post_thumbnail( $shop_id, $image_size, array(
								'class' => 'pk-lazyload-disabled',
							) );
							?>
						</div>
					<?php } ?>
				</div>

			</section>
			<?php
		}
	}
	add_action( 'woocommerce_before_main_content', 'csco_wc_shop_header' );

	// Remove default wrappers.
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

	/**
	 * Wrapper Start
	 */
	function csco_wc_wrapper_start() {
		?>
		<div id="primary" class="content-area">
			<div class="woocommerce-area">
		<?php
	}
	add_action( 'woocommerce_before_main_content', 'csco_wc_wrapper_start', 1 );

	/**
	 * Wrapper End
	 */
	function csco_wc_wrapper_end() {
		?>
			</div>
		</div>
		<?php
	}
	add_action( 'woocommerce_after_main_content', 'csco_wc_wrapper_end', 999 );
}
