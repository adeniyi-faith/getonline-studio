<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$setup_complete = ! get_option( 'igd_show_setup' );

use IGD\Account;
use IGD\Shortcode;

$is_license_activated = ! igd_fs()->is_premium() || igd_fs()->is_trial() || igd_fs()->has_active_valid_license();

$is_accounts_connected = ! empty( Account::instance()->get_accounts() );

global $wpdb;
$count            = $wpdb->get_var( "SELECT COUNT(*) FROM `{$wpdb->prefix}integrate_google_drive_files`" );
$is_files_browsed = $count > 0;

$is_module_created = ! empty( Shortcode::instance()->get_shortcodes() );

$should_finish = $is_license_activated && $is_accounts_connected && $is_files_browsed && $is_module_created;

$plugin_install_url = admin_url( 'plugin-install.php?tab=plugin-information&plugin=integrate-google-drive&TB_iframe=true&width=600&height=550' );
$plugin_upload_url  = admin_url( 'plugin-install.php?tab=upload' );

$license_url         = igd_fs()->get_account_url();
$account_connect_url = admin_url( 'admin.php?page=integrate-google-drive-settings&tab=accounts' );
$file_browse_url     = admin_url( 'admin.php?page=integrate-google-drive' );
$module_builder_url  = admin_url( 'admin.php?page=integrate-google-drive-shortcode-builder' );
$finish_url          = admin_url( 'admin.php?page=integrate-google-drive' );

$steps = [
	[
		'id'                => 'download-install',
		'title'             => __( 'Download & Install', 'integrate-google-drive' ),
		'description'       => __( 'Download the plugin and install it via admin dashboard', 'integrate-google-drive' ),
		'url'               => $plugin_install_url,
		'doc_url'           => 'https://softlabbd.com/docs/how-to-purchase-integrate-google-drive-pro-version-and-activate-pro-license/',
		'video_url'         => 'https://www.youtube.com/watch?v=BN_SBB9uXfM&ab_channel=SoftLab',
		'render'            => true,
		'checked_condition' => true, // always checked
	],
	[
		'id'                => 'activate-license',
		'title'             => __( 'Activate License', 'integrate-google-drive' ),
		'description'       => __( 'If you have purchased the premium version, activate license to unlock all features', 'integrate-google-drive' ),
		'url'               => $license_url,
		'doc_url'           => 'https://softlabbd.com/docs/how-to-purchase-integrate-google-drive-pro-version-and-activate-pro-license/',
		'video_url'         => 'https://www.youtube.com/watch?v=BN_SBB9uXfM&ab_channel=SoftLab',
		'render'            => igd_fs()->is_premium(),
		'checked_condition' => $is_license_activated,
	],
	[
		'id'                => 'connect-account',
		'title'             => __( 'Connect Google Account', 'integrate-google-drive' ),
		'description'       => __( 'Connect your Google account to access your Google Drive files.', 'integrate-google-drive' ),
		'url'               => $account_connect_url,
		'doc_url'           => 'https://softlabbd.com/docs/how-to-link-a-new-google-account-with-integrate-google-drive/',
		'video_url'         => 'https://www.youtube.com/watch?v=i26bBw_G7MM&ab_channel=SoftLab',
		'render'            => true,
		'checked_condition' => $is_accounts_connected,
//		'checked_condition' => false,
	],
	[
		'id'                => 'browse-manage-files',
		'title'             => __( 'Browse & Manage Files', 'integrate-google-drive' ),
		'description'       => __( 'Browse and manage your Google Drive files directly from your WordPress dashboard.', 'integrate-google-drive' ),
		'url'               => $file_browse_url,
		'doc_url'           => '',
		'video_url'         => '',
		'render'            => true,
		'checked_condition' => $is_files_browsed,
//		'checked_condition' => false,
	],
	[
		'id'                => 'module-builder',
		'title'             => __( 'Create Modules', 'integrate-google-drive' ),
		'description'       => __( 'Create custom modules to display & share your Google Drive files in various formats.', 'integrate-google-drive' ),
		'url'               => $module_builder_url,
		'doc_url'           => 'https://softlabbd.com/docs/how-to-use-integrate-google-drive-shortcode-builder-brief/',
		'video_url'         => 'https://www.youtube.com/watch?v=8M84lcvfCiI&ab_channel=SoftLab',
		'render'            => true,
		'checked_condition' => $is_module_created,
//		'checked_condition' => false,
	],
	[
		'id'                => 'display-share',
		'title'             => __( 'Display & Share Files', 'integrate-google-drive' ),
		'description'       => __( 'Display & share your Google Drive files using your created modules.', 'integrate-google-drive' ),
		'url'               => $module_builder_url,
		'doc_url'           => 'https://softlabbd.com/docs/how-to-use-integrate-google-drive-shortcode-builder-brief/',
		'video_url'         => 'https://www.youtube.com/watch?v=8M84lcvfCiI&ab_channel=SoftLab',
		'render'            => true,
		'checked_condition' => false,
	],
	[
		'id'                => 'finish',
		'title'             => __( 'Explore And Enjoy Features', 'integrate-google-drive' ),
		'description'       => __( 'Explore the powerful features of Integrate Google Drive and enhance your site.', 'integrate-google-drive' ),
		'url'               => '#',
		'doc_url'           => 'https://softlabbd.com/docs/',
		'video_url'         => 'https://softlabbd.com/support/',
		'render'            => true,
		'checked_condition' => false,
		'is_finish'         => true,
	],
];

if ( ! $is_license_activated ) {
	igd_fs()->_add_license_action_link();
	igd_fs()->_add_license_activation();
}

?>

<div class="content-heading heading-get-started">
    <h2><?php printf( __( '%s Get Started %s', 'integrate-google-drive' ), '<mark>', '</mark>' ); ?></h2>
</div>

<section class="section-get-started section-half">
    <div class="col-description">

        <div class="timeline-container">

            <div class="timeline-rail"></div>

			<?php
			$step_number = 1;
			foreach ( $steps as $step ) :
				if ( empty( $step['render'] ) ) {
					continue;
				}
				?>

                <div class="timeline-item timeline-step-<?php echo esc_attr( $step['id'] ); ?>">

                    <div class="timeline-step">
                        <div class="circle">
							<?php echo esc_html( $step_number ); ?>

							<?php if ( ( $step['checked_condition'] ) || $should_finish ): ?>
                                <div class="checked">
                                    <i class="dashicons dashicons-saved"></i>
                                </div>
							<?php endif; ?>

                        </div>
                        <div class="connector"></div>
                    </div>

                    <div class="timeline-content-wrap">

                        <div class="timeline-header">
                            <img src="<?php echo esc_url( IGD_ASSETS . "/images/getting-started/timeline/{$step['id']}.png" ); ?>"
                                 alt="<?php echo esc_attr( $step['title'] ); ?>">

                            <div class="timeline-content">

                                <div class="timeline-content-header">
                                    <strong><?php echo esc_html( $step['title'] ); ?></strong>

									<?php if ( ! $is_license_activated && 'activate-license' == $step['id'] ) { ?>
                                        <span class="igd-btn btn-primary activate-license integrate-google-drive">
                                            <i class="dashicons dashicons-admin-network"></i>
											<?php esc_html_e( 'Activate License', 'integrate-google-drive' ); ?>
                                        </span>
									<?php } ?>

									<?php if ( $is_license_activated && ! $is_accounts_connected && 'connect-account' == $step['id'] ) { ?>
                                        <a href="<?php echo esc_url( $step['url'] ); ?>" class="igd-btn btn-primary">
                                            <i class="dashicons dashicons-admin-links"></i>
											<?php esc_html_e( 'Connect Account', 'integrate-google-drive' ); ?>
                                        </a>
									<?php } ?>

									<?php if ( $is_accounts_connected && ! $is_files_browsed && 'browse-manage-files' == $step['id'] ) { ?>
                                        <a href="<?php echo esc_url( $step['url'] ); ?>" class="igd-btn btn-primary">
                                            <i class="dashicons dashicons-open-folder"></i>
											<?php esc_html_e( 'Browse Files', 'integrate-google-drive' ); ?>
                                        </a>
									<?php } ?>

									<?php if ( $is_accounts_connected && ! $is_module_created && 'module-builder' == $step['id'] ) { ?>
                                        <a href="<?php echo esc_url( $step['url'] ); ?>" class="igd-btn btn-primary">
                                            <i class="dashicons dashicons-plus"></i>
											<?php esc_html_e( 'Create First Module', 'integrate-google-drive' ); ?>
                                        </a>
									<?php } ?>

									<?php if ( $should_finish && 'finish' == $step['id'] ) { ?>
                                        <button type="button" class="igd-btn btn-primary btn-finish">
                                            <i class="dashicons dashicons-saved"></i>
											<?php esc_html_e( 'Finish', 'integrate-google-drive' ); ?>
                                        </button>
									<?php } ?>

                                </div>

                                <p><?php echo esc_html( $step['description'] ); ?></p>
                            </div>

                            <i class="timeline-content-toggle dashicons dashicons-arrow-down-alt2"></i>
                        </div>

                        <div class="timeline-body">

							<?php if ( 'download-install' === $step['id'] ) { ?>
                                <p><?php
									/* translators: Installing Integrate Google Drive plugin */
									esc_html_e( 'Follow these simple steps to install and activate the Integrate Google Drive plugin.', 'integrate-google-drive' );
									?></p>

                                <h4><?php esc_html_e( 'Installing the FREE Version:', 'integrate-google-drive' ); ?></h4>
                                <ol>
                                    <li><?php esc_html_e( 'Log in to your WordPress admin dashboard.', 'integrate-google-drive' ); ?></li>
                                    <li><?php
										printf(
											__( 'Navigates to %s.', 'integrate-google-drive' ),
											'<a href="' . $plugin_install_url . '"><strong>Plugins > Add New</strong></a>' );
										?>
                                    </li>
                                    <li><?php esc_html_e( 'Search for Integrate Google Drive.', 'integrate-google-drive' ); ?></li>
                                    <li><?php esc_html_e( 'Click Install Now next to the plugin.', 'integrate-google-drive' ); ?></li>
                                    <li><?php esc_html_e( 'Once installed, click Activate.', 'integrate-google-drive' ); ?></li>
                                    <li><?php esc_html_e( 'A confirmation message will appear once the plugin is successfully activated.', 'integrate-google-drive' ); ?></li>
                                </ol>

                                <h4><?php esc_html_e( 'Installing the PRO Version:', 'integrate-google-drive' ); ?></h4>
                                <ol>
                                    <li><?php esc_html_e( 'After completing your purchase, you will receive a confirmation email with Premium download link and license key.', 'integrate-google-drive' ); ?></li>
                                    <li>
										<?php
										printf(
											__( 'You can also get the PRO Download link and license key from the %s.', 'integrate-google-drive' ),
											'<a href="https://users.freemius.com/store/1760/" target="_blank"><strong>Freemius Dashboard</strong></a>'
										);
										?>
                                    </li>
                                    <li><?php esc_html_e( 'Download the .zip file of the PRO plugin from the purchase confirmation email or Freemius dashboard.', 'integrate-google-drive' ); ?></li>
                                    <li><?php
										printf(
											__( 'In your WordPress admin dashboard, go to %s.', 'integrate-google-drive' ),
											'<a href="' . $plugin_upload_url . '"><strong>Plugins > Add New > Upload Plugin</strong></a>' );
										?>
                                    </li>
                                    <li><?php esc_html_e( 'Upload the downloaded .zip file and click Install Now.', 'integrate-google-drive' ); ?></li>
                                    <li><?php esc_html_e( 'After installation, activate the PRO plugin to enable its features.', 'integrate-google-drive' ); ?></li>
                                </ol>

                                <p><?php
									esc_html_e( 'Once installed, you can connect your Google account and start managing your files seamlessly.', 'integrate-google-drive' );
									?></p>
							<?php } ?>

							<?php if ( 'activate-license' == $step['id'] ) { ?>
                                <p><?php esc_html_e( 'After installing the PRO plugin, follow these steps to activate your license and unlock all premium features.', 'integrate-google-drive' ); ?></p>

                                <h4><?php esc_html_e( 'Activating the PRO License:', 'integrate-google-drive' ); ?></h4>
                                <ol>
                                    <li><?php esc_html_e( 'After completing your purchase, you will receive a confirmation email with License Key & premium download link.', 'integrate-google-drive' ); ?></li>
                                    <li>
										<?php
										printf(
											__( 'You can also get the License Key from the %s.', 'integrate-google-drive' ),
											'<a href="https://users.freemius.com/store/1760/" target="_blank"><strong>Freemius Dashboard</strong></a>'
										);
										?>
                                    </li>
                                    <li><?php esc_html_e( 'Copy the license key from the purchase confirmation email or Freemius dashboard.', 'integrate-google-drive' ); ?></li>
                                    <li><?php esc_html_e( 'Now after installing the PRO version you will be promted to activate your license key.', 'integrate-google-drive' ); ?></li>
                                    <li><?php esc_html_e( 'Enter the license key into the activation field.', 'integrate-google-drive' ); ?></li>
                                    <li><?php esc_html_e( 'Click Agree & Activate License to enable the PRO features.', 'integrate-google-drive' ); ?></li>
                                </ol>

                                <p><?php
									esc_html_e( 'Once activated, you can start enjoying all the advanced features and enhanced capabilities of the PRO version.', 'integrate-google-drive' );
									?></p>
							<?php } ?>

							<?php if ( 'connect-account' == $step['id'] ) { ?>
                                <p><?php esc_html_e( 'After activating the plugin, link your Google account to start using it. You can connect multiple Google accounts if needed.', 'integrate-google-drive' ); ?></p>

                                <h4><?php esc_html_e( 'To add a Google account, follow these steps:', 'integrate-google-drive' ); ?></h4>
                                <ol>
                                    <li><?php
										printf(
											__( 'Navigate to  %s in your WordPress admin dashboard.', 'integrate-google-drive' ),
											'<a href="' . $account_connect_url . '"><strong>Google Drive > Settings</strong></a>' );
										?>
                                    </li>
                                    <li><?php esc_html_e( 'Click the Add Account button.', 'integrate-google-drive' ); ?></li>
                                    <li><?php esc_html_e( 'A new window will open, redirecting you to the Google login page.', 'integrate-google-drive' ); ?></li>
                                    <li><?php esc_html_e( 'Select the Google account you want to link.', 'integrate-google-drive' ); ?></li>
                                    <li><?php esc_html_e( 'Click Allow to grant the plugin access to your Google Drive.', 'integrate-google-drive' ); ?></li>
                                    <li><?php esc_html_e( 'Wait for the authorization to complete — then you’re all set!', 'integrate-google-drive' ); ?></li>
                                </ol>

							<?php } ?>

							<?php if ( 'browse-manage-files' == $step['id'] ) { ?>
								<?php if ( 'browse-manage-files' == $step['id'] ) { ?>
                                    <p><?php esc_html_e( 'Manage your Google Drive files directly from your WordPress dashboard with ease.', 'integrate-google-drive' ); ?></p>

                                    <h4><?php esc_html_e( 'Browsing and Managing Files:', 'integrate-google-drive' ); ?></h4>
                                    <ol>
                                        <li><?php
											printf(
												__( 'Navigate to  %s in your WordPress admin dashboard to view all your Google Drive files.', 'integrate-google-drive' ),
												'<a href="' . $file_browse_url . '"><strong>Google Drive > File Browser</strong></a>' );
											?>
                                        </li>
                                        <li><?php esc_html_e( 'Use options to copy, rename, edit, or delete files without leaving WordPress.', 'integrate-google-drive' ); ?></li>
                                        <li><?php esc_html_e( 'Enjoy fast, real-time syncing to keep your WordPress and Google Drive files up to date.', 'integrate-google-drive' ); ?></li>
                                        <li><?php esc_html_e( 'Filter and search files easily to find exactly what you need.', 'integrate-google-drive' ); ?></li>
                                        <li><?php esc_html_e( 'Manage permissions and sharing settings directly from the plugin.', 'integrate-google-drive' ); ?></li>
                                    </ol>
								<?php } ?>

							<?php } ?>

							<?php if ( 'module-builder' == $step['id'] ) { ?>
                                <p><?php
									/* translators: %s: shortcode */
									printf(
										__( 'You can create unlimited shortcode modules and use them in your posts or pages with the %1$s[integrate_google_drive]%2$s shortcode.', 'integrate-google-drive' ),
										'<code>',
										'</code>'
									);
									?></p>

                                <h4><?php esc_html_e( 'Available Module Types:', 'integrate-google-drive' ); ?></h4>

                                <ol>
                                    <li>
                                        <strong><?php esc_html_e( 'File Browser -', 'integrate-google-drive' ); ?></strong>
										<?php esc_html_e( 'Allows users to browse your Google Drive files directly.', 'integrate-google-drive' ); ?>
                                    </li>
                                    <li>
                                        <strong><?php esc_html_e( 'Embed Document -', 'integrate-google-drive' ); ?></strong>
										<?php esc_html_e( 'Embed any Google Drive file into your page or post.', 'integrate-google-drive' ); ?>
                                    </li>
                                    <li>
                                        <strong><?php esc_html_e( 'File Uploader -', 'integrate-google-drive' ); ?></strong>
										<?php esc_html_e( 'Allows users to upload files directly to your Google Drive.', 'integrate-google-drive' ); ?>
                                    </li>
                                    <li>
                                        <strong><?php esc_html_e( 'Gallery -', 'integrate-google-drive' ); ?></strong>
										<?php esc_html_e( 'Displays a lightbox grid photo & video gallery.', 'integrate-google-drive' ); ?>
                                    </li>
                                    <li>
                                        <strong><?php esc_html_e( 'Review & Approve -', 'integrate-google-drive' ); ?></strong>
										<?php esc_html_e( 'Allow users to review and confirm their file choices.', 'integrate-google-drive' ); ?>
                                    </li>
                                    <li>
                                        <strong><?php esc_html_e( 'Audio & Video Player -', 'integrate-google-drive' ); ?></strong>
										<?php esc_html_e( 'Plays audio and video files in a single player.', 'integrate-google-drive' ); ?>
                                    </li>
                                    <li>
                                        <strong><?php esc_html_e( 'File Search -', 'integrate-google-drive' ); ?></strong>
										<?php esc_html_e( 'Allows users to search Google Drive files from your website.', 'integrate-google-drive' ); ?>
                                    </li>
                                    <li>
                                        <strong><?php esc_html_e( 'List Files -', 'integrate-google-drive' ); ?></strong>
										<?php esc_html_e( 'Displays a list of Google Drive files with various options.', 'integrate-google-drive' ); ?>
                                    </li>
                                    <li>
                                        <strong><?php esc_html_e( 'Slider Carousel -', 'integrate-google-drive' ); ?></strong>
										<?php esc_html_e( 'Displays a slider carousel of Google Drive files.', 'integrate-google-drive' ); ?>
                                    </li>
                                </ol>

							<?php } ?>

							<?php if ( 'display-share' == $step['id'] ) { ?>
                                <p><?php esc_html_e( 'Once you have created your modules, you can easily display and share your Google Drive files on your website.', 'integrate-google-drive' ); ?></p>

                                <h4><?php esc_html_e( 'To display or share files:', 'integrate-google-drive' ); ?></h4>
                                <ol>
                                    <li><?php esc_html_e( 'Use the shortcode generated by the module builder within any post or page for a quick setup.', 'integrate-google-drive' ); ?></li>
                                    <li><?php esc_html_e( 'Alternatively, leverage the plugin’s support for the Classic Editor, Gutenberg blocks, Elementor widgets, or Divi modules to embed your modules anywhere on your site.', 'integrate-google-drive' ); ?></li>
                                    <li><?php esc_html_e( 'You can also share your modules directly with others using the module’s View URL.', 'integrate-google-drive' ); ?></li>
                                </ol>
							<?php } ?>

							<?php if ( 'finish' == $step['id'] ) {
								if ( $should_finish ) {
									?>
                                    <p><?php esc_html_e( 'Congratulations! You have successfully set up Integrate Google Drive. Now you can explore and enjoy the powerful features it offers.', 'integrate-google-drive' ); ?></p>
                                    <p><?php esc_html_e( 'If you need any help, please visit our support page or contact us directly.', 'integrate-google-drive' ); ?></p>
									<?php
								} else { ?>
                                    <p><?php esc_html_e( 'You\'re almost there! Please complete all the setup steps to unlock the full power of Integrate Google Drive.', 'integrate-google-drive' ); ?></p>

                                    <p><?php esc_html_e( 'If you need assistance or have any questions, visit our support page or contact us anytime — we’re here to help!', 'integrate-google-drive' ); ?></p>
								<?php }
							} ?>

                            <div class="timeline-body-actions">
                                <a href="<?php echo $step['doc_url'] ?>" target="_blank" class="igd-btn btn-info">
                                    <i class="dashicons dashicons-text-page"></i>
									<?php esc_html_e( 'Read Documentation', 'integrate-google-drive' ); ?>
                                </a>
                                <a href="<?php echo $step['video_url'] ?>" target="_blank" class="igd-btn btn-info">

									<?php echo 'finish' == $step['id'] ? '<i class="dashicons dashicons-sos"></i>' : '<i class="dashicons dashicons-video-alt3"></i>'; ?>

									<?php echo 'finish' == $step['id'] ? esc_html__( 'Get Support', 'integrate-google-drive' ) : esc_html__( 'Watch Video', 'integrate-google-drive' ); ?>

                                </a>
                            </div>

                        </div>

                    </div>
                </div>

				<?php
				$step_number ++;
			endforeach;
			?>

        </div>


    </div>
</section>
