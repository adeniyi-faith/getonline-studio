<?php

defined( 'ABSPATH' ) || exit();

$logs = [

	'v.1.5.3' => [
		'date'        => '2025-07-22',
		'new'         => [
			'Added option to auto-restore sharing permissions after a set time.',
			'Added direct folder files display option for the list module.',
			'Added options to show/hide file duration, size and date in the media player playlist items.',
			'Added WooCommerce file uploader support for the block-based cart and checkout pages.',
		],
		'fix'         => [
			'Fixed Image Preview Size Crop Issue.',
			'Fixed initial child folders display issue when a single folder is selected.',
			'Fixed previous folder button showing empty folders.',
			'Fixed email sharing not working for woocommerce redirect downloadable products.',
			'Fixed Copy Links not working in the file browser context menu.',
			'Fixed Slider module output issue.',
			'Fixed Private folder parent folder, template selection not working in shortcode builder.',
			'Fix statistics preview data mismatch issue.',
		],
		'enhancement' => [
			'Improved WooCommerce file uploader UI and Performance.',
		],
		'remove'      => [
			'Deprecated the WooCommerce uploader checkout page location setting.',
		],
	],

	'v.1.5.2' => [
		'date' => '2025-06-25',
		'new'  => [
			'Added manual migration option for the old module shortcodes in the Settings > Tools.',
		],
		'fix'  => [
			' Fixed PDF files Embed error.',
		],
	],

	'v.1.5.1' => [
		'date'        => '2025-06-24',
		'new'         => [
			'Added Review & Approve module.',
			'Added List module.',
			'Added bulk import support to enable importing multiple files from Google Drive into the Media Library',
			'Added Google Drive Upload field support for the FluentForms Conversational Forms.',
			'Added File Browser module supports for form integrations.',
			'Added the rotate option in the image lightbox preview.',
			'Added login screen support for the modules when user needs to login.',
			'Added private folder share option with user email on private folder creation.',
			'Added new Performance settings.',
		],
		'fix'         => [
			'Fixed media not playing on the track change issue for the Media Player module.',
			'Fixed Elementor PRO compatibility issues.',
			'Fix %time% placeholder time zone mismatch issue.',
		],
		'enhancement' => [
			'Improved photo proofing for the Gallery module.',
			'Improved the Media Player module to resume playback from the last played position.',
			'Improved the Module Builder compatibility and integration with page builders (Elementor, Divi, Gutenberg and Classic Editor).',
			'Improved the Module Builder compatibility and integrations with the forms.',
			'Improved overall plugin performance, security, and user experience.',
		],
		'remove'      => [
			'Deprecated the View links and Download links modules and merged them into the new List module.',
		],
		'video'       => 'https://youtu.be/sBRPDDV2A1E',
		'article'     => 'https://softlabbd.com/integrate-google-drive-v1-5-1/',
	],

	'v.1.5.0' => [
		'date'        => '2025-03-17',
		'new'         => [
			'Added list style settings for file views and download links.',
			'Added URL parameter support to refresh (?refresh=1) files in the shortcode module.',
		],
		'fix'         => [
			'Fixed file refresh issue in the shortcode module.',
			'Fixed file sorting issue in AJAX pagination.',
			'Fixed scrolling issue in the Media Library folder list.',
		],
		'enhancement' => [
			'Improved the statistics page.',
			'Improved overall performance and stability.',
		]
	],

	'v.1.4.9' => [
		'date'        => '2025-02-05',
		'fix'         => [
			'Resolved an issue where search results were not displaying.',
			'Fixed a problem causing gallery images to not load properly.',
			'Addressed an issue where the shortcode builder’s configure button was unresponsive in the Divi Builder.',
			'Corrected an issue with invalid embed URLs.',
			'Fixed the next/previous buttons not appearing in the media player.',
			'Resolved extension filter malfunctions in the File Upload module.',
			'Fixed the zooming issue in PDF lightbox previews.',
			'Corrected an issue where files were downloading with the wrong extension.',
			'Fixed an issue preventing large files from downloading properly.',
			'Resolved a problem where files were not uploading correctly in shared drives.',
			'Fixed an issue preventing files in shared drives from being deleted, moved, or renamed.',
		],
		'enhancement' => [
			'Enhanced the file import process from Google Drive to the WordPress media library.',
			'Improved the overall performance of the plugin.',
		]
	],

	'v.1.4.8' => [
		'date'        => '2024-12-03',
		'fix'         => [
			'Fixed Divi builder conflicts.',
			'Fixed search box module design broken issue.',
		],
		'enhancement' => [
			'Improved loading pre-loaders animation.',
		]
	],

	'v.1.4.7' => [
		'date'        => '2024-11-30',
		'fix'         => [
			'Fixed folders not sorting properly.',
			'Fixed statistics email report not sending.',
			'Fixed media player responsive issue.',
			'Fixed links not opening in the file inlined view.',
			'Fixed classic editor conflicts in formidable forms settings.',
			'Fixed auto-save not working.',
		],
		'enhancement' => [
			'Added integration support for Tutor LMS v3.0.',
			'Improved overall plugin performance and user interface.',
		]
	],

	'v.1.4.6' => [
		'date' => '2024-11-11',
		'fix'  => [
			'Fixed sort by name not working properly.',
			'Fixed private folders merging issue.',
		]
	],

	'v.1.4.5' => [
		'date'        => '2024-11-04',
		'new'         => [
			'Added password protection for the shortcode modules.',
			'Added grid playlist style for the media player module.',
			'Added settings to hide the preview bottom thumbnails.',
			'Added video type embed, direct option for the media player module and Tutor LMS video.',
		],
		'fix'         => [
			'Fixed WooCommerce products import image not setting properly.',
			'Fixed nonce validation issue for the shortcode modules.',
			'Fixed Upload module not displaying error messages.',
		],
		'enhancement' => [
			'Updated contact form 7 integration to support the latest version.',
		],
	],

];


?>

<div id="what-new" class="getting-started-content content-what-new">
    <div class="content-heading">
        <h2><?php printf( __( 'Exploring the %s Latest Updates %s', 'integrate-google-drive' ), '<mark>', '</mark>' ); ?></h2>
        <p><?php esc_html_e( 'Dive Into the Recent Change Logs for Fresh Insights', 'integrate-google-drive' ); ?></p>
    </div>

	<?php
	$i = 0;
	foreach ( $logs as $v => $log ) { ?>
        <div class="log <?php echo esc_attr( $i == 0 ? 'active' : '' ); ?>">
            <div class="log-header">
                <span class="log-version"><?php echo esc_html( $v ); ?></span>
                <span class="log-date">(<?php echo esc_html( $log['date'] ); ?>)</span>

                <i class="<?php echo esc_attr( $i == 0 ? 'dashicons-arrow-up-alt2' : 'dashicons-arrow-down-alt2' ); ?> dashicons "></i>
            </div>

            <div class="log-body">

				<?php if ( ! empty( $log['video'] ) || ! empty( $log['article'] ) ) : ?>
                    <div class="log-links">
						<?php
						$links = [
							'article' => [
								'icon'  => 'dashicons-text-page',
								'label' => __( 'Read Log Article', 'integrate-google-drive' ),
							],
							'video'   => [
								'icon'  => 'dashicons-video-alt3',
								'label' => __( 'Watch Video', 'integrate-google-drive' ),
							],
						];

						foreach ( $links as $key => $data ) :
							if ( ! empty( $log[ $key ] ) ) :
								?>
                                <div class="log-<?php echo esc_attr( $key ); ?>">
                                    <a href="<?php echo esc_url( $log[ $key ] ); ?>" class="igd-btn btn-info"
                                       target="_blank" rel="noopener noreferrer">
                                        <i class="dashicons <?php echo esc_attr( $data['icon'] ); ?>"></i>
										<?php echo esc_html( $data['label'] ); ?>
                                    </a>
                                </div>
							<?php
							endif;
						endforeach;
						?>
                    </div>
				<?php endif; ?>



				<?php

				if ( ! empty( $log['new'] ) ) {
					printf( '<div class="log-section new"><h3>%s</h3>', __( 'New Features', 'integrate-google-drive' ) );
					foreach ( $log['new'] as $item ) {
						echo '<div class="log-item log-item-new"><i class="dashicons dashicons-plus-alt2"></i> <span>' . $item . '</span></div>';
					}
					echo '</div>';
				}

				if ( ! empty( $log['fix'] ) ) {
					printf( '<div class="log-section fix"><h3>%s</h3>', __( 'Bug Fixes', 'integrate-google-drive' ) );
					foreach ( $log['fix'] as $item ) {
						echo '<div class="log-item log-item-fix"><i class="dashicons dashicons-saved"></i> <span>' . $item . '</span></div>';
					}
					echo '</div>';
				}

				if ( ! empty( $log['enhancement'] ) ) {
					printf( '<div class="log-section enhancement"><h3>%s</h3>', __( 'Improvements', 'integrate-google-drive' ) );
					foreach ( $log['enhancement'] as $item ) {
						echo '<div class="log-item log-item-enhancement"><i class="dashicons dashicons-star-filled"></i> <span>' . $item . '</span></div>';
					}
					echo '</div>';
				}

				if ( ! empty( $log['remove'] ) ) {
					printf( '<div class="log-section remove"><h3>%s</h3>', __( 'Deprecations', 'integrate-google-drive' ) );
					foreach ( $log['remove'] as $item ) {
						echo '<div class="log-item log-item-remove"><i class="dashicons dashicons-trash"></i> <span>' . $item . '</span></div>';
					}
					echo '</div>';
				}

				?>
            </div>

        </div>
		<?php
		$i ++;
	} ?>


</div>


<script>
    jQuery(document).ready(function ($) {
        $('.log-header').on('click', function () {
            $(this).next('.log-body').slideToggle();
            $(this).find('i').toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2');
            $(this).parent().toggleClass('active');
        });
    });
</script>