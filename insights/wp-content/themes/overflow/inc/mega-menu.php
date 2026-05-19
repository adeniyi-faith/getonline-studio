<?php
/**
 * Mega Menu.
 *
 * @package Overflow
 */

/**
 * Mega Menu Class
 */
class CSCO_Mega_Menu {

	/**
	 * Holds our custom fields
	 *
	 * @var    array
	 * @access protected
	 */
	protected static $fields = array();

	/**
	 * Menu Locations
	 *
	 * @var    array
	 * @access private
	 */
	private $locations = array();

	/**
	 * Constructor. Set up cacheable values and settings.
	 */
	public function __construct() {

		// Set locations.
		$this->locations = apply_filters( 'csco_mega_menu_locations', array( 'primary' ) );

		// Support secondary languages.
		$this->locations = $this->_support_languages( (array) $this->locations );

		// Enqueue Scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'mega_menu_scripts' ) );
		add_action( 'admin_footer', array( $this, 'footer_inline_scripts' ) );

		// Admin Hooks.
		add_action( 'wp_nav_menu_item_data_fields', array( $this, '_fields' ), 10, 4 );
		add_action( 'wp_update_nav_menu_item', array( $this, '_save' ), 10, 3 );
		add_filter( 'wp_edit_nav_menu_walker', array( $this, '_admin_menu_walker' ), 10, 2 );
		add_filter( 'manage_nav-menus_columns', array( $this, '_columns' ), 99, 1 );
		add_filter( 'esc_html', array( $this, '_support_badge' ), 99, 2 );

		// Frontend Hooks.
		add_filter( 'nav_menu_css_class', array( $this, 'mega_menu_classes' ), 10, 4 );
		add_filter( 'nav_menu_link_attributes', array( $this, 'mega_menu_atts' ), 10, 4 );
		add_filter( 'walker_nav_menu_start_el', array( $this, 'mega_menu_extra_html' ), 10, 4 );
		add_filter( 'wp_nav_menu_objects', array( $this, 'change_mega_menu_children' ), 10, 2 );

		// Rest Api.
		add_action( 'rest_api_init', array( $this, 'menu_posts_rest_route' ) );

		// Ajax.
		add_action( 'wp_ajax_csco_reload_menu', array( $this, 'reload_admin_nav_menu' ) );
		add_action( 'wp_ajax_nopriv_csco_reload_menu', array( $this, 'reload_admin_nav_menu' ) );

		// Custom Fields.
		self::$fields = array(
			'cs-mega-menu' => array(
				'type'    => 'checkbox',
				'label'   => esc_attr( 'Enable mega menu', 'csco' ),
				'default' => false,
				'depth'   => 0,
			),
		);

		// Fields Filter.
		self::$fields = apply_filters( 'csco_mega_menu_fields', self::$fields );
	}

	/**
	 * Add support powerkit badge.
	 *
	 * @param string $safe_text The text after it has been escaped.
	 * @param string $text      The text prior to being escaped.
	 */
	public function _support_badge( $safe_text, $text ) {
		global $pagenow;

		if ( ! is_admin() || 'nav-menus.php' !== $pagenow ) {
			return $safe_text;
		}

		if ( preg_match( '/pk-badge/', $safe_text ) ) {
			$safe_text = $text;
		}

		return $safe_text;
	}

	/**
	 * Add support languages.
	 *
	 * @param array $locations List locations.
	 */
	public function _support_languages( $locations ) {
		foreach ( $locations as $location ) {
			// Polylang.
			if ( function_exists( 'pll_languages_list' ) ) {
				$languages = pll_languages_list();
				if ( $languages ) {
					foreach ( $languages as $language ) {
						$locations[] = sprintf( '%s___%s', $location, $language );
					}
				}
			}
		}

		return $locations;
	}

	/**
	 * Custom Menu Fields
	 *
	 * @since   1.0.0
	 *
	 * @param int    $item_id  Menu item ID.
	 * @param object $item     Menu item data object.
	 * @param int    $depth    Depth of menu item. Used for padding.
	 * @param array  $args     Menu item args.
	 */
	public function _fields( $item_id, $item, $depth, $args ) {
		// Default Args.
		$default_args = array(
			'type'    => 'input',
			'label'   => '',
			'values'  => array(),
			'default' => null,
			'rules'   => array(),
			'desc'    => '',
			'depth'   => 'any',
		);

		foreach ( self::$fields as $_key => $field ) {
			// Merge Field Options.
			$field = array_merge( $default_args, $field );

			// Set Vars.
			$key   = sprintf( 'menu-item-%s', $_key );
			$id    = sprintf( 'edit-%s-%s', $key, $item->ID );
			$name  = sprintf( '%s[%s]', $key, $item->ID );
			$class = sprintf( 'field-%s description description-wide csco-mega-menu-field-wrap', $_key );
			$value = metadata_exists( 'post', $item->ID, $key ) ? get_post_meta( $item->ID, $key, true ) : $field['default'];

			?>
			<p class="<?php echo esc_attr( $class ); ?>" data-rules="<?php echo esc_attr( wp_json_encode( $field['rules'] ) ); ?>" data-depth="<?php echo esc_attr( $field['depth'] ); ?>">
				<?php
				switch ( $field['type'] ) {
					case 'checkbox':
						printf(
							'<label for="%1$s"><input type="checkbox" id="%1$s" class="csco-mega-menu-field" name="%3$s" data-key="%4$s" value="1" %5$s/>%2$s</label>',
							esc_attr( $id ),
							esc_html( $field['label'] ),
							esc_attr( $name ),
							esc_attr( $_key ),
							checked( (bool) $value, 1, false )
						);
						break;

					case 'input':
						printf(
							'<label for="%1$s">%2$s:<br /><input type="text" id="%1$s" class="widefat csco-mega-menu-field" name="%3$s" data-key="%4$s" value="%5$s" /></label>',
							esc_attr( $id ),
							esc_html( $field['label'] ),
							esc_attr( $name ),
							esc_attr( $_key ),
							esc_attr( $value )
						);
						break;
				}
				?>
			</p>
			<?php
		} // End foreach().
	}

	/**
	 * Save custom field value
	 *
	 * @since   1.0.0
	 * @wp_hook action wp_update_nav_menu_item
	 *
	 * @param int   $menu_id         Nav menu ID.
	 * @param int   $menu_item_db_id Menu item ID.
	 * @param array $menu_item_args  Menu item data.
	 */
	public function _save( $menu_id, $menu_item_db_id, $menu_item_args ) {

		// Check ajax.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// Security.
		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		// Mega Menu Fields.
		foreach ( self::$fields as $_key => $field ) {
			$key = sprintf( 'menu-item-%s', $_key );

			// Sanitize field.
			if ( isset( $_POST[ $key ][ $menu_item_db_id ] ) ) { // Input Var ok.
				$value = sanitize_text_field( wp_unslash( $_POST[ $key ][ $menu_item_db_id ] ) ); // Input Var ok.
			} else {
				$value = null;
			}

			// Update field.
			if ( ! is_null( $value ) ) {
				update_post_meta( $menu_item_db_id, $key, $value );
			} else {
				delete_post_meta( $menu_item_db_id, $key );
			}
		}
	}

	/**
	 * Add fields to the screen options toggle
	 *
	 * @since   1.0.0
	 * @wp_hook filter manage_nav-menus_columns
	 *
	 * @param  array $columns Menu item columns.
	 * @return array
	 */
	public static function _columns( $columns ) {
		$column_fields = array();

		foreach ( self::$fields as $_key => $field ) {
			$column_fields[ $_key ] = $field['label'];
		}
		$columns = array_merge( $columns, $column_fields );

		return $columns;
	}

	/**
	 * Set Mega Menu Admin Walker
	 *
	 * @since   1.0.0
	 * @wp_hook filter wp_edit_nav_menu_walker
	 *
	 * @param  string $class    The walker class to use. Default 'Walker_Nav_Menu_Edit'.
	 * @param  int    $menu_id  ID of the menu being rendered.
	 * @return string           Walker class name.
	 */
	public function _admin_menu_walker( $class, $menu_id ) {

		$class = 'CSCO_Menu_Item_Walker';
		if ( ! class_exists( $class ) ) {
			csco_load_admin_menu_walker();
		}

		return $class;
	}

	/**
	 * Refresh menu item fields
	 *
	 * @since   1.0.0
	 */
	public function reload_admin_nav_menu() {
		$nav_menu_selected_id = isset( $_REQUEST['menu_id'] ) ? (int) $_REQUEST['menu_id'] : 0; // Input var okay.
		$menu_name            = isset( $_REQUEST['menu_name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['menu_name'] ) ) : false; // Input var okay.
		$menu_checked         = isset( $_REQUEST['menu_checked'] ) ? (bool) $_REQUEST['menu_checked'] : false; // Input var okay.

		// Get Menu Location.
		preg_match( '/^menu-locations\[(.*?)\]/', $menu_name, $matches );
		$menu_location = isset( $matches[1] ) ? $matches[1] : false;

		if ( is_nav_menu( $nav_menu_selected_id ) && $menu_location ) {

			// Load WP Nav Menu.
			require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

			// Get All Locations.
			$menu_locations = get_nav_menu_locations();

			// Set|Unset Menu to Locations List.
			if ( $menu_checked ) {
				$menu_locations[ $menu_location ] = $nav_menu_selected_id;
			} else {
				if ( isset( $menu_locations[ $menu_location ] ) ) {
					unset( $menu_locations[ $menu_location ] );
				}
			}

			// Set Edited Menu Locations.
			set_theme_mod( 'nav_menu_locations', $menu_locations );

			// Get Nav Menu HTML.
			$edit_markup = wp_get_nav_menu_to_edit( $nav_menu_selected_id );

			if ( ! is_wp_error( $edit_markup ) ) {
				wp_die( $edit_markup );
			}
		}
	}

	/**
	 * Is Item is Mega Menu?
	 *
	 * @since   1.0.0
	 *
	 * @param  WP_Post  $item       The current menu item.
	 * @param  stdClass $args       An object of wp_nav_menu() arguments.
	 * @return string   $menu_type  Type of mega menu item or false.
	 */
	public function is_mega_menu( $item, $args = null ) {

		// Check Mega Menu Location.
		if ( isset( $args->theme_location ) ) {
			if ( ! $args->theme_location || ! in_array( $args->theme_location, $this->locations, true ) ) {
				return false;
			}
		}

		// Classes.
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		// Mega Menu.
		$menu_meta_key   = 'menu-item-cs-mega-menu';
		$mega_menu_class = 'cs-mega-menu';

		// Is Item Mega Menu?
		if ( in_array( $mega_menu_class, $classes, true ) ) {
			$is_mega_menu = true;
		} else {
			$is_mega_menu = get_post_meta( $item->ID, $menu_meta_key, true );
		}

		// Mega Menu Type.
		$menu_type = false;

		if ( $is_mega_menu && in_array( 'menu-item-has-children', $classes, true ) ) {
			$menu_type = 'categories';
		} elseif ( $is_mega_menu && ( 'category' === $item->object || 'post_tag' === $item->object ) ) {
			$menu_type = 'category';
		} elseif ( isset( $item->mega_menu_child ) ) {

			switch ( $item->mega_menu_child ) {
				case 'category':
					$menu_type = 'child-category';
					break;
				case 'post_tag':
					$menu_type = 'child-category';
					break;
				default:
					$menu_type = 'child-item';
					break;
			}
		}

		// Result.
		return $menu_type;
	}

	/**
	 * Filter the sorted list of menu item objects before generating the menu's HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $sorted_menu_items The menu items, sorted by each menu item's menu order.
	 * @param stdClass $args              An object containing wp_nav_menu() arguments.
	 */
	public function change_mega_menu_children( $sorted_menu_items, $args ) {
		foreach ( $sorted_menu_items as $key => $item ) {

			// Get Mega Menu Type.
			$menu_type = $this->is_mega_menu( $item, $args );

			// Create Mega menu Children.
			if ( 'categories' === $menu_type && 0 === (int) $item->menu_item_parent ) {

				// Children Var.
				$item->mega_menu_children = array();

				// Add Items to Children Args.
				foreach ( $sorted_menu_items as $_key => $_item ) {
					if ( $item->ID === (int) $_item->menu_item_parent ) {

						// Set Mega Menu Type.
						$_item->mega_menu_child = $_item->object;

						// Set as child.
						$item->mega_menu_children[] = $_item;
						unset( $sorted_menu_items[ $_key ] );
					}
				}
			}
		}

		return $sorted_menu_items;
	}

	/**
	 * Mega Menu Classes.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $classes The CSS classes that are applied to the menu item's `<li>` element.
	 * @param WP_Post  $item    The current menu item.
	 * @param stdClass $args    An object of wp_nav_menu() arguments.
	 * @param int      $depth   Depth of menu item. Used for padding.
	 */
	public function mega_menu_classes( $classes, $item, $args, $depth ) {

		// Get Mega Menu Type.
		$menu_type = $this->is_mega_menu( $item, $args );

		// Add Mega Menu Classes.
		if ( $menu_type ) {

			if ( 0 === $depth ) {
				$classes[] = 'cs-mega-menu';
			}

			$classes[] = 'cs-mega-menu-has-' . $menu_type;

			// Menu Item Arrow fix.
			if ( 'category' === $menu_type ) {
				$classes[] = 'menu-item-has-children';
			}

			// Child Class.
			if ( in_array( $menu_type, array( 'child-category', 'child-item' ), true ) ) {
				$classes[] = 'cs-mega-menu-child';
			}
		}

		return $classes;
	}

	/**
	 * Mega Menu Attributes
	 *
	 * @since 1.0.0
	 *
	 * @param array    $atts  The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
	 * @param WP_Post  $item  The current menu item.
	 * @param stdClass $args  An object of wp_nav_menu() arguments.
	 * @param int      $depth Depth of menu item. Used for padding.
	 */
	public function mega_menu_atts( $atts, $item, $args, $depth ) {

		// Get Mega Menu Type.
		$menu_type = $this->is_mega_menu( $item, $args );

		// Mega Menu attrs.
		if ( in_array( $menu_type, array( 'category', 'child-category' ), true ) ) {
			$atts['data-cat']  = $item->object_id;
			$atts['data-type'] = $item->object;

			if ( 'category' === $menu_type ) {
				$atts['data-numberposts'] = 5;
			} elseif ( 'child-category' === $menu_type ) {
				$atts['data-numberposts'] = 4;
			}
		}

		return $atts;
	}

	/**
	 * Mega Menu Additional Content.
	 *
	 * @param string   $item_output The menu item's starting HTML output.
	 * @param WP_Post  $item        Menu item data object.
	 * @param int      $depth       Depth of menu item. Used for padding.
	 * @param stdClass $args        An object of wp_nav_menu() arguments.
	 */
	public function mega_menu_extra_html( $item_output, $item, $depth, $args ) {

		// Get Mega Menu Type.
		$menu_type = $this->is_mega_menu( $item, $args );

		// Mega Menu Content.
		ob_start();

		switch ( $menu_type ) {
			case 'categories':
				if ( isset( $item->mega_menu_children ) && ! empty( $item->mega_menu_children ) ) {
					?>
						<div class="sub-menu">
							<div class="cs-mm-content">
								<ul class="cs-mm-categories">
									<?php
									foreach ( $item->mega_menu_children as $_item ) {

										$classes   = empty( $_item->classes ) ? array() : (array) $_item->classes;
										$classes[] = 'menu-item-' . $_item->ID;

										// Filters the CSS class(es) applied to a menu item's list item element.
										$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $_item, $args, $depth + 1 ) );

										$atts = array();

										$atts['title']  = ! empty( $_item->attr_title ) ? $_item->attr_title : '';
										$atts['target'] = ! empty( $_item->target ) ? $_item->target : '';
										$atts['rel']    = ! empty( $_item->xfn ) ? $_item->xfn : '';
										$atts['href']   = ! empty( $_item->url ) ? $_item->url : '';

										// Filters the HTML attributes applied to a menu item's anchor element.
										$atts = apply_filters( 'nav_menu_link_attributes', $atts, $_item, $args, $depth + 1 );

										$attributes = '';
										foreach ( $atts as $attr => $value ) {
											if ( ! empty( $value ) ) {
												$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
												$attributes .= ' ' . $attr . '="' . $value . '"';
											}
										}

										// Link HTML.
										$link = '<a' . $attributes . '>' . apply_filters( 'the_title', $_item->title, $_item->ID ) . '</a>';

										$allowed_html = array(
											'a' => array(
												'href'     => true,
												'title'    => true,
												'target'   => true,
												'rel'      => true,
												'href'     => true,
												'data-cat' => true,
												'data-type' => true,
												'data-numberposts' => true,
											),
										);

										// Output Item.
										?>
											<li class="<?php echo esc_attr( $class_names ); ?>">
												<?php echo wp_kses( $link, $allowed_html ); ?>
											</li>
										<?php
									}
									?>
								</ul>

								<div class="cs-mm-posts-container">
									<?php
									foreach ( $item->mega_menu_children as $_item ) {
										if ( 'category' === $_item->object || 'post_tag' === $_item->object ) {
											?>
												<div class="cs-mm-posts" data-cat="<?php echo esc_attr( $_item->object_id ); ?>"></div>
											<?php
										}
									}
									?>
								</div>
							</div>
						</div>
					<?php
				}

				break;

			case 'category':
				if ( 'category' === $item->object || 'post_tag' === $item->object ) {
					?>
						<div class="sub-menu">
							<div class="cs-mm-posts mega-menu-category"></div>
						</div>
					<?php
				}

				break;

		}

		// Set Mega Menu Content.
		$item_output .= ob_get_clean();

		return $item_output;
	}

	/**
	 * Get Mega Menu Posts
	 *
	 * @param array $request REST API Request.
	 */
	public function load_menu_posts_restapi( $request ) {

		// Default Data.
		$data = array(
			'status'  => 'error',
			'content' => '',
		);

		// Category ID.
		$category_id = isset( $_GET['cat'] ) ? (int) $_GET['cat'] : 0; // Input var ok.

		if ( $category_id <= 0 ) {
			return $data;
		}

		// Type.
		$object_type = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : 'category'; // Input var ok.

		// Number of posts.
		$per_page = isset( $_GET['per_page'] ) ? (int) $_GET['per_page'] : 5; // Input var ok.

		// Get Category Posts.
		$args = array(
			'posts_per_page' => $per_page,
		);

		if ( 'post_tag' === $object_type ) {
			$args['tag_id'] = $category_id;
		} else {
			$args['cat'] = $category_id;
		}

		$category_posts = new WP_Query( $args );

		ob_start();

		if ( $category_posts->have_posts() && isset( $category_posts->posts ) ) {

			// Thumbnail Size Item.
			$image_size = apply_filters( 'csco_mega_menu_thumbnail_size', 'csco-intermediate' );

			// Each Posts.
			while ( $category_posts->have_posts() ) {
				$category_posts->the_post();
				?>
					<article <?php post_class( 'cs-mm-post menu-post-item' ); ?>>
						<?php if ( has_post_thumbnail() ) { ?>
							<div class="entry-thumbnail">
								<div class="cs-overlay cs-overlay-hover cs-overlay-ratio cs-ratio-landscape cs-bg-dark">
									<div class="cs-overlay-background">
										<?php the_post_thumbnail( $image_size ); ?>
									</div>
									<div class="cs-overlay-content">
										<?php csco_the_post_format_icon(); ?>
										<span class="read-more"><?php echo esc_html( get_theme_mod( 'misc_label_readmore', __( 'Read More', 'overflow' ) ) ); ?></span>
									</div>
									<a href="<?php the_permalink(); ?>" class="cs-overlay-link"></a>
								</div>
							</div>
						<?php } ?>
						<header class="entry-header">
							<h6 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
							<?php csco_get_post_meta( array( 'views' ), false, true, 'archive_post_meta' ); ?>
						</header>
					</article>
				<?php
			}

			wp_reset_postdata();
		}

		$data = array(
			'status'  => 'success',
			'content' => ob_get_clean(),
		);

		// Return Result.
		return rest_ensure_response( $data );
	}

	/**
	 * Register REST MEga Menu Posts Routes
	 */
	public function menu_posts_rest_route() {

		register_rest_route(
			'csco/v1',
			'/menu-posts',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'load_menu_posts_restapi' ),
				'permission_callback' => function() {
					return true;
				},
			)
		);
	}

	/**
	 * Localize mega menu scripts.
	 */
	public function mega_menu_scripts() {

		// Localize Script.
		$args = array(
			'rest_url' => esc_url( get_rest_url( null, '/csco/v1/menu-posts' ) ),
		);

		wp_localize_script( 'csco-scripts', 'csco_mega_menu', $args );
	}

	/**
	 * Output scripts & styles on the Admin Nav Menu Page
	 */
	public function footer_inline_scripts() {
		global $pagenow;

		if ( 'nav-menus.php' === $pagenow ) {

			$js_mega_menu_locations = array();

			if ( $this->locations ) {
				foreach ( $this->locations as $location ) {
					$js_mega_menu_locations[] = sprintf( '#locations-%s', $location );
				}
			}
			?>
			<script>
				(function($) {

					/* On Document Ready */
					$( document ).ready( function() {

						var cscoCheckLocations = '<?php echo (array) $js_mega_menu_locations ? esc_html( implode( ',', $js_mega_menu_locations ) ) : ''; ?>';

						/*
						* Check Mega Menu Visible.
						*/
						function cscoCheckMenuVisible( el ) {
							if ( $( cscoCheckLocations ).is(':checked') ) {
								$( '#menu-to-edit' ).addClass( 'mega-menu-visible' );
							} else {
								$( '#menu-to-edit' ).removeClass( 'mega-menu-visible' );
							}
						}

						/*
						* Set csco Menu Locations
						*/
						function cscoSetMenuLocations( el ) {
							var cscoMenuID      = parseInt( $( el ).val() ),
								cscoMenuName    = $( el ).attr( 'name' ),
								cscoMenuChecked = $( el ).attr( 'checked' );

							if( cscoMenuID > 0 ) {
								$.ajax({
									type: 'POST',
									url: ajaxurl,
									data: { 'action': 'csco_reload_menu', 'menu_id': cscoMenuID, 'menu_name': cscoMenuName, 'menu_checked': cscoMenuChecked },
									beforeSend: function(){
										$( '#update-nav-menu' ).addClass( 'menu-ajax-reloading' );
									},
									success: function( result ) {

										cscoCheckMenuVisible();

										if ( result.length > 0 && result != 0 ) {
											var resultHtml = $.parseHTML( result );

											if ( resultHtml ) {
												$.each( resultHtml, function( i, el ) {
													if ( $( el ).attr( 'id' ) == 'menu-to-edit' ) {
														$( '#menu-to-edit' ).html( $( el ).html() );
													}
												});

												// Refresh Item Buttons
												$( '#menu-to-edit .menu-item' ).hideAdvancedMenuItemFields();

												if ( typeof wpNavMenu !== 'undefined' ) {
													wpNavMenu.refreshKeyboardAccessibility();
													wpNavMenu.refreshAdvancedAccessibility();
												}
											}
										}
										$( '#update-nav-menu' ).removeClass( 'menu-ajax-reloading' );
									},
									error: function(e){
										$( '#update-nav-menu' ).removeClass( 'menu-ajax-reloading' );
									}
								});
							}
						}
						/*
						* Change Menu Location
						*/
						$( '.menu-theme-locations input[type="checkbox"]' ).on( 'change', function( event ) {
							cscoSetMenuLocations( this );
						});

						// Refresh item rules.
						$( document ).ready( function() {
							cscoCheckMenuVisible();
						} );
					});
				})(jQuery);
			</script>
			<style type="text/css">
				#update-nav-menu.menu-ajax-reloading {
					position: relative;
					z-index: 12;
				}
				#update-nav-menu.menu-ajax-reloading:before {
					content: '';
					position: absolute;
					top: 0;
					left: 0;
					width: 100%;
					height: 100%;
					background: rgba( 255, 255, 255, 0.6 );
					z-index: 15;
				}
				#menu-to-edit li.menu-item .field-cs-mega-menu{
					display: none;
				}
				#menu-to-edit.mega-menu-visible li.menu-item.menu-item-depth-0 .field-cs-mega-menu {
					display: block;
				}
			</style>
			<?php
		} // End if().
	}
}

/**
 * Init Mega Menu
 *
 * @since 1.0.0
 */
function csco_mega_menu_init() {
	new CSCO_Mega_Menu();
}
add_action( 'init', 'csco_mega_menu_init' );

/**
 * Mega Menu Admin Walker.
 *
 * @since 1.0.0
 *
 * @see Walker_Nav_Menu_Edit
 */
function csco_load_admin_menu_walker() {

	/**
	 * Custom Walker for Nav Menu Editor
	 */
	class CSCO_Menu_Item_Walker extends Walker_Nav_Menu_Edit {

		/**
		 * Start the element output.
		 *
		 * We're injecting our custom fields after the div.submitbox
		 *
		 * @see Walker_Nav_Menu::start_el()
		 * @since 0.1.0
		 * @since 0.2.0 Update regex pattern to support WordPress 4.7's markup.
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item   Menu item data object.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   Menu item args.
		 * @param int    $id     Nav menu ID.
		 */
		function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			$item_output = '';

			parent::start_el( $item_output, $item, $depth, $args, $id );

			$output .= preg_replace(
				// NOTE: Check this regex from time to time!
				'/(?=<(fieldset|p)[^>]+class="[^"]*field-move)/',
				$this->get_fields( $item, $depth, $args ),
				$item_output
			);
		}

		/**
		 * Get custom fields
		 *
		 * @access protected
		 * @since 0.1.0
		 * @uses add_action() Calls 'menu_item_custom_fields' hook
		 *
		 * @param object $item   Menu item data object.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   Menu item args.
		 * @param int    $id     Nav menu ID.
		 *
		 * @return string Form fields
		 */
		protected function get_fields( $item, $depth, $args = array(), $id = 0 ) {
			ob_start();

			/**
			 * Get menu item custom fields
			 *
			 * @since 0.1.0
			 * @since 1.0.0 Pass correct parameters.
			 *
			 * @param int    $item_id  Menu item ID.
			 * @param object $item     Menu item data object.
			 * @param int    $depth    Depth of menu item. Used for padding.
			 * @param array  $args     Menu item args.
			 *
			 * @return string Custom fields HTML.
			 */
			do_action( 'wp_nav_menu_item_data_fields', $item->ID, $item, $depth, $args );

			return ob_get_clean();
		}
	}
}
