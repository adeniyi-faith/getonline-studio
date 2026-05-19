<?php

namespace IGD;

defined( 'ABSPATH' ) || exit;
class Ajax {
    private static $instance = null;

    public function __construct() {
        /*--- Admin Actions ---*/
        // Get Shortcodes
        add_action( 'wp_ajax_igd_get_shortcodes', [$this, 'get_shortcodes'] );
        // Update Shortcode
        add_action( 'wp_ajax_igd_update_shortcode', [$this, 'update_shortcode'] );
        // Duplicate Shortcode
        add_action( 'wp_ajax_igd_duplicate_shortcode', [$this, 'duplicate_shortcode'] );
        // Delete Shortcode
        add_action( 'wp_ajax_igd_delete_shortcode', [$this, 'delete_shortcode'] );
        // Get Embed Content
        add_action( 'wp_ajax_igd_get_embed_content', [$this, 'get_embed_content'] );
        // Get Shortcode Content
        add_action( 'wp_ajax_igd_get_shortcode_content', [$this, 'get_shortcode_content'] );
        // Clear cache files
        add_action( 'wp_ajax_igd_clear_cache', [$this, 'clear_cache'] );
        // Save Settings
        add_action( 'wp_ajax_igd_save_settings', [$this, 'save_settings'] );
        // Get Users Data
        add_action( 'wp_ajax_igd_get_users_data', [$this, 'get_users_data'] );
        // Update User Folders
        add_action( 'wp_ajax_igd_update_user_folders', [$this, 'update_user_folders'] );
        // Get Export Data
        add_action( 'wp_ajax_igd_get_export_data', [$this, 'get_export_data'] );
        // Import Data
        add_action( 'wp_ajax_igd_import_data', [$this, 'import_data'] );
        // Handle admin  notice
        add_action( 'wp_ajax_igd_hide_review_notice', [$this, 'hide_review_notice'] );
        add_action( 'wp_ajax_igd_review_feedback', [$this, 'handle_review_feedback'] );
        // Delete Account
        add_action( 'wp_ajax_igd_delete_account', [$this, 'delete_account'] );
        // Reset Usage Limits
        add_action( 'wp_ajax_igd_reset_usage_limits', [$this, 'reset_current_usage'] );
        // get account storage usage
        add_action( 'wp_ajax_igd_get_storage', [$this, 'get_account_storage'] );
        // Update account specific folders
        add_action( 'wp_ajax_igd_toggle_specific_folders', [$this, 'toggle_account_specific_folders__premium_only'] );
        // Get access token
        add_action( 'wp_ajax_igd_get_access_token', [$this, 'get_access_token__premium_only'] );
        // save specific folders
        add_action( 'wp_ajax_igd_save_specific_folders', [$this, 'save_specific_folders__premium_only'] );
        // remove specific folders
        add_action( 'wp_ajax_igd_remove_specific_folders', [$this, 'remove_specific_folders__premium_only'] );
        // Handle Ajax
        add_action( 'wp_ajax_igd_get_parent_folders', array($this, 'get_parent_folders') );
        // igd_hide_setup
        add_action( 'wp_ajax_igd_hide_setup', array($this, 'hide_get_started_setup') );
        /*--- Frontend Actions ---*/
        // Search Files
        add_action( 'wp_ajax_igd_search_files', [$this, 'search_files'] );
        add_action( 'wp_ajax_nopriv_igd_search_files', [$this, 'search_files'] );
        // Get File
        add_action( 'wp_ajax_igd_get_file', [$this, 'get_file'] );
        add_action( 'wp_ajax_nopriv_igd_get_file', [$this, 'get_file'] );
        // Delete Files
        add_action( 'wp_ajax_igd_delete_files', [$this, 'delete_files'] );
        add_action( 'wp_ajax_nopriv_igd_delete_files', [$this, 'delete_files'] );
        // Preview Content
        add_action( 'wp_ajax_igd_preview', [$this, 'preview'] );
        add_action( 'wp_ajax_nopriv_igd_preview', [$this, 'preview'] );
        // Download file
        add_action( 'wp_ajax_igd_download', [$this, 'download'] );
        add_action( 'wp_ajax_nopriv_igd_download', [$this, 'download'] );
        // Get download status
        add_action( 'wp_ajax_igd_download_status', [$this, 'get_download_status'] );
        add_action( 'wp_ajax_nopriv_igd_download_status', [$this, 'get_download_status'] );
        // Get upload direct url
        add_action( 'wp_ajax_igd_get_upload_url', [$this, 'get_upload_url'] );
        add_action( 'wp_ajax_nopriv_igd_get_upload_url', [$this, 'get_upload_url'] );
        // Stream
        add_action( 'wp_ajax_igd_stream', [$this, 'stream_content'] );
        add_action( 'wp_ajax_nopriv_igd_stream', [$this, 'stream_content'] );
        // Get Files
        add_action( 'wp_ajax_igd_get_files', [$this, 'get_files'] );
        add_action( 'wp_ajax_nopriv_igd_get_files', [$this, 'get_files'] );
        // Remove uploaded files
        add_action( 'wp_ajax_igd_upload_remove_file', [$this, 'remove_upload_file'] );
        add_action( 'wp_ajax_nopriv_igd_upload_remove_file', [$this, 'remove_upload_file'] );
        // Upload post process
        add_action( 'wp_ajax_igd_file_uploaded', [$this, 'upload_post_process'] );
        add_action( 'wp_ajax_nopriv_igd_file_uploaded', [$this, 'upload_post_process'] );
        // Move File
        add_action( 'wp_ajax_igd_move_file', [$this, 'move_file'] );
        add_action( 'wp_ajax_nopriv_igd_move_file', [$this, 'move_file'] );
        // Rename File
        add_action( 'wp_ajax_igd_rename_file', [$this, 'rename_file'] );
        add_action( 'wp_ajax_nopriv_igd_rename_file', [$this, 'rename_file'] );
        // Copy Files
        add_action( 'wp_ajax_igd_copy_file', [$this, 'copy_file'] );
        add_action( 'wp_ajax_nopriv_igd_copy_file', [$this, 'copy_file'] );
        // New Folder
        add_action( 'wp_ajax_igd_new_folder', [$this, 'new_folder'] );
        add_action( 'wp_ajax_nopriv_igd_new_folder', [$this, 'new_folder'] );
        // Switch Account
        add_action( 'wp_ajax_igd_switch_account', [$this, 'switch_account'] );
        add_action( 'wp_ajax_nopriv_igd_switch_account', [$this, 'switch_account'] );
        // Create Doc
        add_action( 'wp_ajax_igd_create_doc', [$this, 'create_doc'] );
        add_action( 'wp_ajax_nopriv_igd_create_doc', [$this, 'create_doc'] );
        // Update Description
        add_action( 'wp_ajax_igd_update_description', [$this, 'update_description'] );
        add_action( 'wp_ajax_nopriv_igd_update_description', [$this, 'update_description'] );
        // Update permission of the file - make public
        add_action( 'wp_ajax_igd_update_file_permission', [$this, 'update_file_permission__premium_only'] );
        add_action( 'wp_ajax_nopriv_igd_update_file_permission', [$this, 'update_file_permission__premium_only'] );
        // Module Password Protected
        add_action( 'wp_ajax_igd_check_module_password', [$this, 'handle_password_protected__premium_only'] );
        add_action( 'wp_ajax_nopriv_igd_check_module_password', [$this, 'handle_password_protected__premium_only'] );
        // Module login
        add_action( 'wp_ajax_igd_handle_module_login', [$this, 'handle_module_login'] );
        add_action( 'wp_ajax_nopriv_igd_handle_module_login', [$this, 'handle_module_login'] );
        // Get Pages
        add_action( 'wp_ajax_igd_get_pages', [$this, 'get_pages'] );
        // Create Page
        add_action( 'wp_ajax_igd_create_page', [$this, 'create_page'] );
        // Handle migration for version 1.5.1
        add_action( 'wp_ajax_igd_run_151_migration_batch', [$this, 'run_151_migration_batch'] );
        add_action( 'wp_ajax_igd_get_migration_status', [$this, 'get_migration_status'] );
        add_action( 'wp_ajax_igd_clear_setting_migration', [$this, 'clear_setting_migration'] );
    }

    public function clear_setting_migration() {
        $this->check_nonce();
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Permission denied', 'integrate-google-drive' ) );
        }
        $settings = get_option( 'igd_settings', [] );
        $settings['shouldMigrate'] = false;
        update_option( 'igd_settings', $settings );
        wp_send_json_success( __( 'Migration status cleared successfully.', 'integrate-google-drive' ) );
    }

    public function hide_get_started_setup() {
        $this->check_nonce();
        delete_option( 'igd_show_setup' );
        wp_send_json_success( __( 'Setup hidden successfully.', 'integrate-google-drive' ) );
    }

    public function run_151_migration_batch() {
        $this->check_nonce();
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Permission denied', 'integrate-google-drive' ) );
        }
        if ( !class_exists( 'IGD\\Migration_1_5_1' ) ) {
            include_once IGD_INCLUDES . '/updates/class-migration-1.5.1.php';
        }
        $result = Migration_1_5_1::instance()->run_batch();
        // You may also want to set a transient or option to no longer show admin notice
        if ( $result['completed'] ) {
            delete_option( 'igd_migration_1_5_1_state' );
        }
        wp_send_json_success( $result );
    }

    public function get_migration_status() {
        $this->check_nonce();
        $status = get_option( 'igd_migration_1_5_1_status' );
        $state = get_option( 'igd_migration_1_5_1_state', [] );
        $current_step = $state['step'] ?? null;
        $current_offset = $state['offset'] ?? 0;
        if ( $status === 'run' || $status === 'running' ) {
            $message = sprintf( __( 'Migration is running. Step: %s, Offset: %d', 'integrate-google-drive' ), ( $current_step ?: 'N/A' ), $current_offset );
        } else {
            delete_option( 'igd_migration_1_5_1_state' );
            delete_option( 'igd_migration_1_5_1_status' );
            $status = 'completed';
            $message = __( 'Migration completed successfully.', 'integrate-google-drive' );
        }
        wp_send_json_success( [
            'status'  => $status,
            'message' => $message,
            'step'    => $current_step,
            'offset'  => $current_offset,
        ] );
    }

    /**
     * Create a new page on embed shortcode in a new page
     *
     * @return void
     */
    public function create_page() {
        $this->check_nonce();
        if ( !current_user_can( 'edit_pages' ) ) {
            wp_send_json_error( __( 'Permission denied.', 'integrate-google-drive' ) );
        }
        $title = sanitize_text_field( $_POST['title'] ?? '' );
        $shortcode_id = sanitize_text_field( $_POST['id'] ?? '' );
        $post_type = 'page';
        // Check if block editor is enabled for this post-type
        if ( function_exists( 'use_block_editor_for_post_type' ) && use_block_editor_for_post_type( $post_type ) ) {
            $content = sprintf( '<!-- wp:igd/shortcodes {"id":%d} /-->', $shortcode_id );
        } else {
            $content = sprintf( '[integrate_google_drive id="%d"]', $shortcode_id );
        }
        $page_id = wp_insert_post( [
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => 'draft',
            'post_type'    => $post_type,
        ] );
        if ( is_wp_error( $page_id ) ) {
            wp_send_json_error( $page_id->get_error_message() );
        }
        wp_send_json_success( [
            'id'    => $page_id,
            'title' => $title,
        ] );
    }

    public function get_pages() {
        $this->check_nonce();
        if ( !current_user_can( 'edit_pages' ) ) {
            wp_send_json_error( __( 'Permission denied.', 'integrate-google-drive' ) );
        }
        $pages = get_posts( [
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => 999,
            'fields'         => ['ID', 'post_title'],
        ] );
        if ( empty( $pages ) ) {
            wp_send_json_success( [] );
        }
        $formatted = array_map( function ( $page ) {
            return [
                'id'    => $page->ID,
                'title' => esc_html( $page->post_title ),
            ];
        }, $pages );
        wp_send_json_success( $formatted );
    }

    public function handle_module_login() {
        // Verify AJAX nonce
        $this->check_nonce();
        // Sanitize and extract credentials
        $shortcode_id = ( isset( $_POST['shortcodeId'] ) ? sanitize_text_field( $_POST['shortcodeId'] ) : '' );
        $username = ( isset( $_POST['username'] ) ? sanitize_user( $_POST['username'] ) : '' );
        $password = ( isset( $_POST['password'] ) ? $_POST['password'] : '' );
        $remember = !empty( $_POST['remember'] );
        // Validate required fields
        if ( empty( $username ) || empty( $password ) ) {
            wp_send_json_error( [
                'message' => __( 'Username and password are required.', 'integrate-google-drive' ),
            ] );
        }
        // Attempt login
        $user = wp_signon( [
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => $remember,
        ], is_ssl() );
        if ( is_wp_error( $user ) ) {
            error_log( 'IGD Login Failed: ' . $user->get_error_message() );
            wp_send_json_error( [
                'message' => $user->get_error_message(),
            ] );
        }
        // Set session cookies and user context
        wp_set_current_user( $user->ID );
        wp_set_auth_cookie( $user->ID, $remember );
        // Fetch and render shortcode content
        $shortcode_data = Shortcode::get_shortcode_data( $shortcode_id );
        if ( empty( $shortcode_data ) ) {
            wp_send_json_error( [
                'message' => __( 'Invalid shortcode reference.', 'integrate-google-drive' ),
            ] );
        }
        $rendered_output = Shortcode::instance()->render_shortcode( [], $shortcode_data );
        wp_send_json_success( $rendered_output );
    }

    public function get_shortcodes() {
        // Verify nonce for security.
        $this->check_nonce();
        // Check user permission.
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page.', 'integrate-google-drive' ) );
        }
        // Fetch shortcodes.
        $shortcodes = Shortcode::get_shortcodes();
        wp_send_json_success( [
            'shortcodes' => $shortcodes,
            'total'      => Shortcode::get_shortcodes_count(),
        ] );
    }

    public function delete_shortcode() {
        // Check nonce
        $this->check_nonce();
        // Check permission
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $id = ( !empty( $_POST['id'] ) ? intval( $_POST['id'] ) : '' );
        Shortcode::delete_shortcode( $id );
        wp_send_json_success();
    }

    public function get_embed_content() {
        // Check nonce
        $this->check_nonce();
        // Check permission
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action.', 'integrate-google-drive' ) );
        }
        $data = ( !empty( $_POST['data'] ) ? igd_sanitize_array( $_POST['data'] ) : [] );
        $content = igd_get_embed_content( $data );
        wp_send_json_success( $content );
    }

    public function get_shortcode_content() {
        // Check nonce
        $this->check_nonce();
        // Check permission
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action.', 'integrate-google-drive' ) );
        }
        $id = ( !empty( $_POST['id'] ) ? igd_sanitize_array( $_POST['id'] ) : [] );
        $html = Shortcode::instance()->render_shortcode( [
            'id' => $id,
        ] );
        wp_send_json_success( $html );
    }

    public function hide_review_notice() {
        // Check nonce
        $this->check_nonce();
        // Check permission
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        update_option( 'igd_rating_notice', 'off' );
        wp_send_json_success();
    }

    public function handle_review_feedback() {
        // Check nonce
        $this->check_nonce();
        // Check permission
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $feedback = ( !empty( $_POST['feedback'] ) ? sanitize_textarea_field( $_POST['feedback'] ) : '' );
        if ( !empty( $feedback ) ) {
            $feedback = sanitize_textarea_field( $feedback );
            $website_url = get_bloginfo( 'url' );
            /* translators: %s: User feedback */
            $feedback = sprintf( __( 'Feedback: %s', 'integrate-google-drive' ), $feedback );
            $feedback .= '<br>';
            /* translators: %s: Website URL */
            $feedback .= sprintf( __( 'Website URL: %s', 'integrate-google-drive' ), $website_url );
            /* translators: %s: Plugin name */
            $subject = sprintf( __( 'Feedback for %s', 'integrate-google-drive' ), 'Integrate Google Drive' );
            $to = 'israilahmed5@gmail.com';
            $headers = ['Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo( 'name' ) . ' <' . get_bloginfo( 'admin_email' ) . '>'];
            wp_mail(
                $to,
                $subject,
                $feedback,
                $headers
            );
            update_option( 'igd_rating_notice', 'off' );
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }

    public function duplicate_shortcode() {
        // Check nonce
        $this->check_nonce();
        // Check permission
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $ids = ( !empty( $_POST['ids'] ) ? igd_sanitize_array( $_POST['ids'] ) : [] );
        $data = [];
        if ( !empty( $ids ) ) {
            foreach ( $ids as $id ) {
                $data[] = Shortcode::duplicate_shortcode( $id );
            }
        }
        wp_send_json_success( $data );
    }

    public function update_shortcode() {
        // Check nonce
        $this->check_nonce();
        // Check permission
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $data = ( !empty( $_POST['data'] ) ? json_decode( base64_decode( $_POST['data'] ), true ) : [] );
        if ( empty( $data['type'] ) ) {
            $data['type'] = ( igd_fs()->can_use_premium_code__premium_only() ? 'browser' : 'embed' );
        }
        $id = Shortcode::update_shortcode( $data );
        $data['id'] = $id;
        if ( empty( $data['title'] ) ) {
            $type_label = igd_get_module_types( $data['type'] )['title'];
            $title = "Module (#{$id}) - {$type_label} ";
            if ( !empty( $_REQUEST['isModuleBuilder'] ) ) {
                $module_builder = igd_sanitize_array( $_REQUEST['isModuleBuilder'] );
                $type_text = '';
                $id_text = '';
                if ( !empty( $module_builder['type'] ) ) {
                    $type = $module_builder['type'];
                    if ( 'cf7' == $type ) {
                        $type_text = ' - Contact Form 7';
                    } elseif ( 'fluentforms' == $type ) {
                        $type_text = ' - Fluent Forms';
                    } elseif ( 'formidableforms' == $type ) {
                        $type_text = ' - Formidable Forms';
                    } elseif ( 'gravityforms' == $type ) {
                        $type_text = ' - Gravity Forms';
                    } elseif ( 'ninjaforms' == $type ) {
                        $type_text = ' - Ninja Forms';
                    } elseif ( 'wpforms' == $type ) {
                        $type_text = ' - WPForms';
                    } elseif ( 'elementor' == $type ) {
                        $type_text = ' - Elementor Form';
                    } elseif ( 'metform' == $type ) {
                        $type_text = ' - MetForm';
                    } elseif ( 'post' == $type ) {
                        $type_text = ' - Page';
                        if ( !empty( $module_builder['id'] ) ) {
                            $post_type = get_post_type( $module_builder['id'] );
                            $post_type_obj = get_post_type_object( $post_type );
                            if ( $post_type_obj && !empty( $post_type_obj->labels->singular_name ) ) {
                                $type_text = ' - ' . $post_type_obj->labels->singular_name;
                            }
                        }
                    } elseif ( 'woocommerce' == $type ) {
                        $type_text = ' - WooCommerce Product';
                    }
                }
                if ( !empty( $module_builder['id'] ) ) {
                    $id_text = " (#{$module_builder['id']})";
                }
                $title .= $type_text . $id_text;
            }
            $data['title'] = $title;
            global $wpdb;
            $table = $wpdb->prefix . 'integrate_google_drive_shortcodes';
            $wpdb->update(
                $table,
                [
                    'title'  => $data['title'],
                    'config' => maybe_serialize( array_merge( $data, [
                        'id'    => $id,
                        'title' => $data['title'],
                    ] ) ),
                ],
                [
                    'id' => $id,
                ],
                ['%s', '%s'],
                ['%d']
            );
        }
        $data = [
            'id'         => $id,
            'config'     => $data,
            'title'      => $data['title'],
            'status'     => $data['status'] ?? 'on',
            'type'       => $data['type'],
            'created_at' => $data['created_at'] ?? current_time( 'Y-m-d H:i:s' ),
        ];
        wp_send_json_success( $data );
    }

    public function get_users_data() {
        // Check nonce
        $this->check_nonce();
        // Check permission
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $search = ( !empty( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '' );
        $role = ( !empty( $_POST['role'] ) ? sanitize_text_field( $_POST['role'] ) : '' );
        $page = ( !empty( $_POST['page'] ) ? intval( $_POST['page'] ) : 1 );
        $number = ( !empty( $_POST['number'] ) ? intval( $_POST['number'] ) : 999 );
        $offset = 10 * ($page - 1);
        $args = [
            'number' => $number,
            'role'   => ( 'all' != $role ? $role : '' ),
            'offset' => $offset,
            'search' => ( !empty( $search ) ? "*{$search}*" : '' ),
        ];
        $user_data = Private_Folders::instance()->get_user_data( $args );
        wp_send_json_success( $user_data );
    }

    public function update_user_folders() {
        // Check nonce
        $this->check_nonce();
        // Check performance
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $user_id = ( !empty( $_POST['id'] ) ? intval( $_POST['id'] ) : 0 );
        $folders = ( !empty( $_POST['folders'] ) ? igd_sanitize_array( $_POST['folders'] ) : [] );
        update_user_meta( $user_id, 'igd_folders', $folders );
        wp_send_json_success();
    }

    public function clear_cache() {
        // Check nonce
        $this->check_nonce();
        // Check permission
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        igd_delete_cache();
        wp_send_json_success();
    }

    public function get_account_storage() {
        // Check nonce
        $this->check_nonce();
        // Check permission
        if ( !igd_user_can_access( 'settings' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $accounts = ( !empty( $_POST['accounts'] ) ? igd_sanitize_array( $_POST['accounts'] ) : [] );
        $usage = [];
        foreach ( $accounts as $account_id ) {
            $usage[$account_id] = Account::instance()->get_storage_usage( $account_id );
        }
        wp_send_json_success( $usage );
    }

    public function get_export_data() {
        // Check nonce
        $this->check_nonce();
        // Check permission
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $type = ( !empty( $_POST['$type'] ) ? sanitize_text_field( $_POST['$type'] ) : 'all' );
        $export_data = array();
        // Settings
        if ( 'all' == $type || 'settings' == $type ) {
            $export_data['settings'] = igd_get_settings();
        }
        // Shortcodes
        if ( 'all' == $type || 'shortcodes' == $type ) {
            $export_data['shortcodes'] = Shortcode::get_shortcodes();
        }
        // User Private Files
        if ( 'all' == $type || 'user_files' == $type ) {
            $user_files = array();
            $users = get_users();
            foreach ( $users as $user ) {
                $folders = get_user_meta( $user->ID, 'igd_folders', true );
                $user_files[$user->ID] = ( !empty( $folders ) ? $folders : array() );
            }
            $export_data['user_files'] = $user_files;
        }
        wp_send_json_success( $export_data );
    }

    public function import_data() {
        // Check nonce
        $this->check_nonce();
        // Check permission
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $json_string = ( !empty( $_POST['data'] ) ? stripslashes( $_POST['data'] ) : [] );
        $json_decoded = json_decode( $json_string, 1 );
        $return_data = [];
        foreach ( $json_decoded as $key => $data ) {
            if ( empty( $data ) ) {
                continue;
            }
            if ( 'settings' == $key ) {
                update_option( 'igd_settings', $data );
                $return_data['settings'] = $data;
            }
            if ( 'shortcodes' == $key ) {
                Shortcode::delete_shortcode();
                foreach ( $data as $shortcode ) {
                    Shortcode::update_shortcode( $shortcode, true );
                }
            }
            if ( 'user_files' == $key ) {
                foreach ( $data as $user_id => $files ) {
                    update_user_meta( $user_id, 'igd_folders', $files );
                }
            }
        }
        wp_send_json_success( $return_data );
    }

    public function save_settings() {
        // Check nonce
        $this->check_nonce();
        // Check permission
        if ( !igd_user_can_access( 'settings' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $settings = ( !empty( $_POST['settings'] ) ? json_decode( base64_decode( $_POST['settings'] ), true ) : [] );
        update_option( 'igd_settings', $settings );
        wp_send_json_success();
    }

    public function delete_account() {
        // Check nonce
        $this->check_nonce();
        $referrer = wp_get_referer();
        $is_dokan_dashboard = strpos( $referrer, '/settings/google-drive/' ) !== false;
        // Check permission
        if ( !is_user_logged_in() || !igd_user_can_access( 'shortcode_builder' ) && !$is_dokan_dashboard ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $id = ( !empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '' );
        Account::instance()->delete_account( $id );
        wp_send_json_success();
    }

    public function reset_current_usage() {
        // Check nonce
        $this->check_nonce();
        // Check permission
        if ( !igd_user_can_access( 'settings' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        // Delete usage for all users
        delete_metadata(
            'user',
            null,
            'igd_usage_limits',
            null,
            true
        );
        // Delete usage for all not logged in users
        igd_delete_transients_with_prefix( 'igd_usage_limits_' );
        wp_send_json_success();
    }

    /**
     * Get the Google Drive files and folders
     *
     * @return void
     */
    public function get_files() {
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        $posted = ( !empty( $_POST['data'] ) ? igd_sanitize_array( $_POST['data'] ) : [] );
        $active_account_id = igd_get_active_account_id();
        if ( empty( $active_account_id ) ) {
            wp_send_json_error( __( 'No active account found', 'integrate-google-drive' ) );
        }
        $args = [
            'folder'      => [
                'id'         => 'root',
                'accountId'  => $active_account_id,
                'pageNumber' => 1,
            ],
            'sort'        => [
                'sortBy'        => 'name',
                'sortDirection' => 'asc',
            ],
            'from_server' => false,
            'filters'     => [],
        ];
        // Merge request params
        $args = wp_parse_args( $posted, $args );
        $folder = $args['folder'];
        $account_id = ( !empty( $folder['accountId'] ) ? $folder['accountId'] : $active_account_id );
        $refresh = !empty( $args['refresh'] );
        // Check if shortcut folder
        if ( !empty( $folder['shortcutDetails'] ) ) {
            $args['folder']['id'] = $folder['shortcutDetails']['targetId'];
        }
        $transient = get_transient( 'igd_latest_fetch_' . $folder['id'] );
        // Reset cache and get new files
        if ( $refresh || !$transient ) {
            $refresh_args = $args;
            $refresh_args['folder']['pageNumber'] = 1;
            $refresh_args['from_server'] = true;
            igd_delete_cache( [$folder['id']] );
            $data = App::instance( $account_id )->get_files( $refresh_args );
            if ( !$transient ) {
                set_transient( 'igd_latest_fetch_' . $folder['id'], true, HOUR_IN_SECONDS );
            }
        } else {
            $data = App::instance( $account_id )->get_files( $args );
        }
        if ( !empty( $data['error'] ) ) {
            wp_send_json_success( $data );
        }
        // Get breadcrumbs
        if ( empty( $folder['pageNumber'] ) || $folder['pageNumber'] == 1 ) {
            $data['breadcrumbs'] = igd_get_breadcrumb( $folder );
        }
        wp_send_json_success( $data );
    }

    public function search_files() {
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        $posted = ( !empty( $_POST ) ? igd_sanitize_array( $_POST ) : [] );
        $account_id = ( !empty( $posted['accountId'] ) ? sanitize_text_field( $posted['accountId'] ) : '' );
        $data = App::instance( $account_id )->get_search_files( $posted );
        if ( !empty( $data['error'] ) ) {
            wp_send_json_success( $data );
        }
        wp_send_json_success( $data );
    }

    public function get_file() {
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        $file_id = ( !empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '' );
        $account_id = ( !empty( $_POST['accountId'] ) ? sanitize_text_field( $_POST['accountId'] ) : '' );
        $file = App::instance( $account_id )->get_file_by_id( $file_id );
        wp_send_json_success( $file );
    }

    public function get_parent_folders() {
        $this->check_nonce();
        if ( empty( $_POST['folder'] ) ) {
            return;
        }
        $folder = igd_sanitize_array( $_POST['folder'] );
        $folders = igd_get_grouped_parent_folders( $folder );
        wp_send_json_success( $folders );
    }

    public function delete_files() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'delete_files' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $file_ids = ( !empty( $_POST['file_ids'] ) ? igd_sanitize_array( $_POST['file_ids'] ) : [] );
        $account_id = ( !empty( $_POST['account_id'] ) ? sanitize_text_field( $_POST['account_id'] ) : '' );
        // Send email notification
        if ( igd_get_settings( 'deleteNotifications', true ) ) {
            do_action(
                'igd_send_notification',
                'delete',
                $file_ids,
                $account_id
            );
        }
        wp_send_json_success( App::instance( $account_id )->delete( $file_ids ) );
    }

    public function new_folder() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'new_folder' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $folder_name = ( !empty( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '' );
        $parent_id = ( !empty( $_POST['parent_id'] ) ? sanitize_text_field( $_POST['parent_id'] ) : '' );
        $account_id = ( !empty( $_POST['account_id'] ) ? sanitize_text_field( $_POST['account_id'] ) : '' );
        $new_folder = App::instance( $account_id )->new_folder( $folder_name, $parent_id );
        wp_send_json_success( $new_folder );
    }

    public function create_doc() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'create_doc' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $name = ( !empty( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : 'Untitled' );
        $type = ( !empty( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'doc' );
        $folder_id = ( !empty( $_POST['folder_id'] ) ? sanitize_text_field( $_POST['folder_id'] ) : 'root' );
        $account_id = ( !empty( $_POST['account_id'] ) ? sanitize_text_field( $_POST['account_id'] ) : '' );
        $mime_type = 'application/vnd.google-apps.document';
        if ( $type == 'sheet' ) {
            $mime_type = 'application/vnd.google-apps.spreadsheet';
        } elseif ( $type == 'slide' ) {
            $mime_type = 'application/vnd.google-apps.presentation';
        }
        try {
            $item = App::instance( $account_id )->getService()->files->create( new \IGDGoogle_Service_Drive_DriveFile([
                'name'     => $name,
                'mimeType' => $mime_type,
                'parents'  => [$folder_id],
            ]), [
                'fields'            => '*',
                'supportsAllDrives' => true,
            ] );
            // add new folder to cache
            $file = igd_file_map( $item, $account_id );
            // Insert log
            do_action( 'igd_insert_log', [
                'type'       => 'create',
                'file_id'    => $file['id'],
                'account_id' => $account_id,
                'file_name'  => $file['name'],
                'file_type'  => $file['type'],
            ] );
            wp_send_json_success( $file );
        } catch ( \Exception $e ) {
            wp_send_json_error( array(
                'error' => $e->getMessage(),
            ) );
        }
    }

    public function copy_file() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'move_copy' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $files = ( !empty( $_POST['files'] ) ? igd_sanitize_array( $_POST['files'] ) : [] );
        $folder_id = ( !empty( $_POST['folder_id'] ) ? sanitize_text_field( $_POST['folder_id'] ) : '' );
        $account_id = ( !empty( $files[0]['accountId'] ) ? sanitize_text_field( $files[0]['accountId'] ) : '' );
        $copied_files = App::instance( $account_id )->copy( $files, $folder_id );
        wp_send_json_success( $copied_files );
    }

    public function move_file() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'move_copy' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $file_ids = ( !empty( $_POST['file_ids'] ) ? igd_sanitize_array( $_POST['file_ids'] ) : '' );
        $folder_id = ( !empty( $_POST['folder_id'] ) ? sanitize_text_field( $_POST['folder_id'] ) : '' );
        $account_id = ( !empty( $_POST['account_id'] ) ? sanitize_text_field( $_POST['account_id'] ) : '' );
        wp_send_json_success( App::instance( $account_id )->move_file( $file_ids, $folder_id ) );
    }

    public function rename_file() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'rename' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $name = ( !empty( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '' );
        $file_id = ( !empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '' );
        $account_id = ( !empty( $_POST['accountId'] ) ? sanitize_text_field( $_POST['accountId'] ) : '' );
        wp_send_json_success( App::instance( $account_id )->rename( $name, $file_id ) );
    }

    public function switch_account() {
        // Check nonce
        $this->check_nonce();
        // set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !igd_user_can_access( 'file_browser' ) && !Shortcode::can_do( 'switch_account' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $id = ( !empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '' );
        Account::instance()->set_active_account_id( $id );
        wp_send_json_success();
    }

    public function preview() {
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        $file_id = sanitize_text_field( $_REQUEST['file_id'] );
        $account_id = sanitize_text_field( $_REQUEST['account_id'] );
        $popout = true;
        if ( !empty( $_REQUEST['popout'] ) ) {
            $popout = filter_var( $_REQUEST['popout'], FILTER_VALIDATE_BOOLEAN );
        }
        if ( !empty( $_REQUEST['direct_link'] ) ) {
            $popout = false;
        }
        $download = true;
        if ( !empty( $_REQUEST['download'] ) ) {
            $download = filter_var( $_REQUEST['download'], FILTER_VALIDATE_BOOLEAN );
        }
        $app = App::instance( $account_id );
        $file = $app->get_file_by_id( $file_id );
        $preview_url = igd_get_embed_url(
            $file,
            false,
            false,
            true,
            $popout,
            $download
        );
        if ( !$preview_url ) {
            _e( 'Something went wrong! Preview is not available', 'integrate-google-drive' );
            die;
        }
        //		do_action( 'igd_insert_log', [
        //			'type'       => 'preview',
        //			'file_id'    => $file_id,
        //			'account_id' => $account_id,
        //			'file_name'  => $file['name'],
        //			'file_type'  => $file['type'],
        //		] );
        wp_redirect( $preview_url );
        die;
    }

    public function remove_upload_file() {
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        $id = ( !empty( $_POST['id'] ) ? $_POST['id'] : '' );
        $account_id = ( !empty( $_POST['account_id'] ) ? $_POST['account_id'] : '' );
        $is_woocommerce = ( !empty( $_POST['isWooCommerceUploader'] ) ? filter_var( $_POST['isWooCommerceUploader'], FILTER_VALIDATE_BOOLEAN ) : false );
        $product_id = ( !empty( $_POST['wcProductId'] ) ? sanitize_text_field( $_POST['wcProductId'] ) : 0 );
        $item_id = ( !empty( $_POST['wcItemId'] ) ? intval( $_POST['wcItemId'] ) : 0 );
        // Remove uploaded files from Google Drive
        App::instance( $account_id )->delete( [$id] );
        // Remove uploaded files from woocommerce order meta-data
        if ( $is_woocommerce ) {
            if ( $item_id ) {
                $files = array_filter( wc_get_order_item_meta( $item_id, '_igd_files', false ) );
                if ( !empty( $files ) ) {
                    foreach ( $files as $key => $file ) {
                        if ( $file['id'] === $id ) {
                            unset($files[$key]);
                        }
                    }
                    wc_update_order_item_meta( $item_id, '_igd_files', $files );
                }
            } else {
                //Remove uploaded files from wc session
                $files = WC()->session->get( 'igd_product_files_' . $product_id, [] );
                if ( !empty( $files ) ) {
                    foreach ( $files as $key => $file ) {
                        if ( $file['id'] === $id ) {
                            unset($files[$key]);
                        }
                    }
                    WC()->session->set( 'igd_product_files_' . $product_id, $files );
                }
            }
        }
        wp_send_json_success( [
            'success' => true,
        ] );
    }

    public function upload_post_process() {
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        $file = ( !empty( $_POST['file'] ) ? igd_sanitize_array( $_POST['file'] ) : [] );
        $account_id = ( !empty( $file['accountId'] ) ? sanitize_text_field( $file['accountId'] ) : '' );
        $formatted_file = Uploader::instance( $account_id )->upload_post_process( $file );
        // Save uploaded files in the order meta-data for order-received page and my-account page
        $item_id = ( !empty( $_POST['wcItemId'] ) ? intval( $_POST['wcItemId'] ) : false );
        $product_id = ( !empty( $_POST['wcProductId'] ) ? intval( $_POST['wcProductId'] ) : false );
        if ( $item_id ) {
            if ( function_exists( 'wc_add_order_item_meta' ) ) {
                wc_add_order_item_meta( $item_id, '_igd_files', $file );
            }
        } elseif ( $product_id ) {
            // Save uploaded files in the session for checkout page
            if ( function_exists( 'WC' ) ) {
                if ( !WC()->session->has_session() ) {
                    WC()->session->set_customer_session_cookie( true );
                }
                $files = WC()->session->get( 'igd_product_files_' . $product_id, [] );
                $files[] = $file;
                WC()->session->set( 'igd_product_files_' . $product_id, $files );
            }
        }
        do_action( 'igd_insert_log', [
            'type'       => 'upload',
            'file_id'    => $formatted_file['id'],
            'account_id' => $account_id,
            'file_name'  => $formatted_file['name'],
            'file_type'  => $formatted_file['type'],
        ] );
        do_action( 'igd_upload_post_process', $formatted_file, $account_id );
        wp_send_json_success( $formatted_file );
    }

    public function get_upload_url() {
        // Only check the global igd nonce
        if ( !check_ajax_referer( 'igd', 'nonce' ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'integrate-google-drive' ) );
        }
        // Set current shortcode data
        $this->set_current_shortcode_data();
        $data = ( !empty( $_POST['data'] ) ? igd_sanitize_array( $_POST['data'] ) : [] );
        $account_id = ( !empty( $data['accountId'] ) ? sanitize_text_field( $data['accountId'] ) : '' );
        $url = Uploader::instance( $account_id )->get_resume_url( $data );
        if ( isset( $url['error'] ) ) {
            error_log( $url['error'] );
            wp_send_json_error( $url );
        }
        wp_send_json_success( $url );
    }

    public function download() {
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        $file_id = ( !empty( $_REQUEST['id'] ) ? sanitize_text_field( $_REQUEST['id'] ) : '' );
        $file_ids = ( !empty( $_REQUEST['file_ids'] ) ? json_decode( base64_decode( sanitize_text_field( $_REQUEST['file_ids'] ) ) ) : [] );
        $request_id = ( !empty( $_REQUEST['request_id'] ) ? sanitize_text_field( $_REQUEST['request_id'] ) : '' );
        $account_id = ( !empty( $_REQUEST['accountId'] ) ? sanitize_text_field( $_REQUEST['accountId'] ) : '' );
        $mimetype = ( !empty( $_REQUEST['mimetype'] ) ? sanitize_text_field( $_REQUEST['mimetype'] ) : 'default' );
        if ( !empty( $file_ids ) ) {
            igd_download_zip( $file_ids, $request_id, $account_id );
        } elseif ( !empty( $file_id ) ) {
            Download::instance( $file_id, $account_id, $mimetype )->start_download();
        } else {
            wp_send_json_error( __( 'File not found', 'integrate-google-drive' ) );
        }
        exit;
    }

    public function get_download_status() {
        // Check nonce
        $this->check_nonce();
        $id = ( !empty( $_REQUEST['id'] ) ? sanitize_text_field( $_REQUEST['id'] ) : '' );
        $status = get_transient( 'igd_download_status_' . $id );
        wp_send_json_success( $status );
    }

    public function update_description() {
        $this->check_nonce();
        $id = ( !empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '' );
        $account_id = ( !empty( $_POST['accountId'] ) ? sanitize_text_field( $_POST['accountId'] ) : '' );
        $description = ( !empty( $_POST['description'] ) ? sanitize_text_field( $_POST['description'] ) : '' );
        $item_id = ( !empty( $_POST['wcItemId'] ) ? intval( $_POST['wcItemId'] ) : false );
        $product_id = ( !empty( $_POST['wcProductId'] ) ? intval( $_POST['wcProductId'] ) : false );
        $updated_file = App::instance( $account_id )->update_description( $id, $description );
        if ( $item_id ) {
            if ( function_exists( 'wc_add_order_item_meta' ) ) {
                wc_add_order_item_meta( $item_id, '_igd_files', $updated_file );
            }
        } elseif ( $product_id ) {
            // Save uploaded files in the session for checkout page
            if ( function_exists( 'WC' ) ) {
                $files = WC()->session->get( 'igd_product_files_' . $product_id, [] );
                foreach ( $files as $key => $file ) {
                    if ( $file['id'] === $id ) {
                        $files[$key] = $updated_file;
                    }
                }
                WC()->session->set( 'igd_product_files_' . $product_id, $files );
            }
        }
        wp_send_json_success( $updated_file );
    }

    public function stream_content() {
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Get posted data
        $file_id = ( !empty( $_REQUEST['id'] ) ? sanitize_text_field( $_REQUEST['id'] ) : '' );
        $account_id = ( !empty( $_REQUEST['account_id'] ) ? sanitize_text_field( $_REQUEST['account_id'] ) : '' );
        $ignore_limit = !empty( $_REQUEST['ignore_limit'] );
        Stream::instance( $file_id, $account_id, $ignore_limit )->stream_content();
        exit;
    }

    public function check_nonce() {
        // Check cross-site request forgery
        $enabled_cross_domain_verification = igd_get_settings( 'crossDomainVerification' );
        if ( $enabled_cross_domain_verification ) {
            $refer = igd_get_referrer();
            if ( !empty( $refer ) ) {
                $refer_host = parse_url( $refer, PHP_URL_HOST );
                $origin_host = parse_url( get_site_url(), PHP_URL_HOST );
                if ( !empty( $refer_host ) && 0 !== strcasecmp( $refer_host, $origin_host ) ) {
                    wp_send_json_error( __( 'Cross-site request forgery detected', 'integrate-google-drive' ) );
                    exit;
                }
            }
        }
        // Check if nonce verification is enabled
        $enabled_nonce_verification = igd_get_settings( 'nonceVerification', true );
        $nonce_action = ( is_user_logged_in() ? 'igd' : 'igd-shortcode-nonce' );
        if ( $enabled_nonce_verification && !check_ajax_referer( $nonce_action, 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'integrate-google-drive' ) );
        }
    }

    public function set_current_shortcode_data() {
        if ( !empty( $_REQUEST['shortcodeId'] ) ) {
            $shortcode_id = sanitize_text_field( $_REQUEST['shortcodeId'] );
            Shortcode::set_current_shortcode( $shortcode_id );
        }
    }

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}

Ajax::instance();