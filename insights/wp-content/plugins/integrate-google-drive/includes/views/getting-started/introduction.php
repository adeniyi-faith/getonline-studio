<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$setup_complete = ! get_option( 'igd_show_setup' );

$plugins = [
	[
		'key'   => 'radio-player',
		'title' => __( 'Radio Player', 'integrate-google-drive' ),
		'desc'  => __( 'Live Shoutcast, Icecast and Any Audio Stream Player for WordPress.', 'integrate-google-drive' ),
		'color' => '#76DE91',
        'users' => '10K+',
	],
	[
		'key'   => 'dracula-dark-mode',
		'title' => __( 'Dracula Dark Mode', 'integrate-google-drive' ),
		'desc'  => __( 'Enhanced Accessibility, Dark Mode & Reading Mode for WordPress.', 'integrate-google-drive' ),
        'color' => '#675DE1',
        'users' => '5K+',
	],
	[
		'key'   => 'essential-addons-for-contact-form-7',
		'title' => __( 'Essential Addons for Contact Form 7', 'integrate-google-drive' ),
		'desc'  => __( '50+ Essential Fields, Features & Integrations Add-ons for Contact Form 7.', 'integrate-google-drive' ),
        'color' => '#33C6F4',
        'users' => '1K+',
	]
];

?>

<div id="introduction"
     class="getting-started-content content-introduction active <?php echo $setup_complete ? 'setup-complete' : ''; ?>">

	<?php

	if ( ! $setup_complete ) {
		include_once IGD_INCLUDES . '/views/getting-started/get-started.php';
	}

	?>

    <div class="content-heading heading-overview">
        <h2><?php printf( __( 'Quick %s Overview %s', 'integrate-google-drive' ), '<mark>', '</mark>' ); ?></h2>
        <p><?php esc_html_e( 'Get started with Integrate Google Drive', 'integrate-google-drive' ); ?></p>
    </div>

    <section class="section-introduction section-full">
        <div class="col-description">
            <p>
				<?php
				esc_html_e( 'Integrate Google Drive is the best and easy to use Google Drive integration plugin for
                WordPress to use your Google Drive files and documents into your WordPress website.', 'integrate-google-drive' );
				?>
            </p>
            <p>
				<?php
				esc_html_e( 'Share your Google Drive cloud files into your site very fast and easily. You can browse, manage,
                embed, display, upload, download, search, play, share your Google Drive files directly into your
                website without any hassle and coding.', 'integrate-google-drive' );
				?>
            </p>
        </div>

        <div class="col-image">
            <iframe src="https://www.youtube.com/embed/6DrYur4KGLA?rel=0"
                    title="Integrate Google Drive - Video Overview" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
        </div>

    </section>

    <div class="content-heading never-miss-feature">
        <h2>
            <h2><?php echo sprintf( __( 'Never miss a %s Valuable Feature %s', 'integrate-google-drive' ), '<mark>', '</mark>' ); ?></h2>
        </h2>
        <p><?php esc_html_e( 'Let\'s explore the awesome features of the plugin', 'integrate-google-drive' ); ?></p>
    </div>

    <div class="features-wrap">
        <!-- Review and Approve Module -->
        <section class="section-media-library section-full">
            <div class="col-description">
                <h2><?php esc_html_e( 'Review & Approve Module', 'integrate-google-drive' ); ?>
                    <span class="badge"><?php esc_html_e( 'New ⚡', 'integrate-google-drive' ); ?></span>
                </h2>
                <p>
					<?php esc_html_e( 'The Review and Approve module offers a collaborative workflow where users can review, select, and approve Google Drive files submitted for feedback or confirmation. Ideal for use cases like photo proofing, document approvals, or asset selection, this module includes selection checkboxes, approval status indicators, and comment support.', 'integrate-google-drive' ); ?>
                </p>
            </div>

            <div class="col-image">
                <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/review-module.png' ); ?>"/>
            </div>
        </section>

        <!-- List Files -->
        <section class="section-media-library section-full">

            <div class="col-image">
                <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/review-module.png' ); ?>"/>
            </div>

            <div class="col-description">
                <h2><?php esc_html_e( 'List Files', 'integrate-google-drive' ); ?>
                    <span class="badge"><?php esc_html_e( 'New ⚡', 'integrate-google-drive' ); ?></span>
                </h2>
                <p>
					<?php esc_html_e( 'The List Files module offers a clean, organized interface to display your Google Drive files, allowing users to preview and download files directly from the list.', 'integrate-google-drive' ); ?>
                </p>
            </div>
        </section>

        <!-- File Browser -->
        <section class="section-file-browser section-full">
            <div class="col-description">
                <h2><?php esc_html_e( 'File Browser', 'integrate-google-drive' ); ?></h2>

                <p>
					<?php
					esc_html_e( 'You can manage your cloud files from your website using the full-featured file browser of the
                    plugin. Manage preview, download, upload, rename, move, delete, permissions per user using
                    the file browser. Users can also browse your cloud files using the File Browser.', 'integrate-google-drive' );
					?>
                </p>
            </div>

            <div class="col-image">
                <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/file-browser.png' ); ?>"
                     alt="<?php esc_attr_e( 'File Browser', 'integrate-google-drive' ); ?>">
            </div>
        </section>

        <!-- File Uploader -->
        <section class="section-file-uploader section-full">
            <div class="col-image">
                <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/file-uploader.png' ); ?>"
                     alt="<?php esc_attr_e( 'File Uploader', 'integrate-google-drive' ); ?>">
            </div>

            <div class="col-description">
                <h2><?php esc_html_e( 'File Uploader', 'integrate-google-drive' ); ?></h2>

                <p>
					<?php
					esc_attr_e( 'You and also your users can upload files directly to your Google Drive account from your
                    site. You can upload unlimited size of files.', 'integrate-google-drive' );
					?>
                </p>
            </div>
        </section>

        <!-- Gallery -->
        <section class="section-photo-gallery section-full">
            <div class="col-description">
                <h2><?php esc_html_e( 'Gallery', 'integrate-google-drive' ); ?></h2>
                <p>
					<?php esc_html_e( 'You can add a grid lightbox popup gallery of photos and videos in your page/ post using the gallery
                    module of the plugin. The gallery will be generated based on the folders, photos and images that you select.', 'integrate-google-drive' ); ?>
                </p>
            </div>

            <div class="col-image">
                <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/photo-gallery.png' ); ?>"/>
            </div>
        </section>

        <!-- Slider Carousel -->
        <section class="section-slider-carousel section-full">
            <div class="col-image">
                <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/slider-carousel.png' ); ?>"/>
            </div>

            <div class="col-description">
                <h2><?php esc_html_e( 'Slider Carousel', 'integrate-google-drive' ); ?></h2>
                <p>
					<?php esc_html_e( 'With the Slider Carousel module, you can create a beautiful and interactive slider carousel to showcase your Google Drive images, videos, and documents. Simply use the shortcode to embed the slider anywhere on your site.', 'integrate-google-drive' ); ?>
                </p>
            </div>


        </section>

        <section class="section-file-search section-full">
            <div class="col-description">
                <h2><?php esc_html_e( 'File Search', 'integrate-google-drive' ); ?></h2>
                <p><?php esc_html_e( 'You can search any of your cloud files from your site and also let the users to search the
                    cloud files to view and download.', 'integrate-google-drive' ); ?></p>
            </div>

            <div class="col-image">
                <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/file-search.png' ); ?>"/>
            </div>
        </section>

        <!-- Embed Documents -->
        <section class="section-embed section-full">
            <div class="col-image">
                <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/embed.png' ); ?>"/>
            </div>

            <div class="col-description">
                <h2><?php esc_html_e( 'Embed Documents', 'integrate-google-drive' ); ?></h2>
                <p>
					<?php
					esc_html_e( 'You can easily embed any Google Drive Cloud files in any post/ page of your WordPress website
                directly using this plugin.', 'integrate-google-drive' );
					?>
                </p>
            </div>
        </section>

        <!-- Media Player -->
        <section class="section-links section-full">
            <div class="col-description">
                <h2><?php esc_html_e( 'Media Player', 'integrate-google-drive' ); ?></h2>
                <p>
					<?php esc_html_e( 'You can play your Google Drive audio & video files with a playlist into your website. Audio and
                video can be played in a single player.', 'integrate-google-drive' ); ?>
                </p>
            </div>

            <div class="col-image">
                <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/media-player.png' ); ?>"/>
            </div>
        </section>

        <div class="show-all-btn-wrap">
            <button class="igd-btn btn-primary" id="all-feature-show">
                <i class="dashicons dashicons-arrow-down-alt2"></i>
				<?php _e( 'Show More Features', 'integrate-google-drive' ); ?>
            </button>
        </div>

        <!-- Specific Folders Accessibility -->
        <section class="section-media-library section-full igd-hidden">
            <div class="col-image">
                <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/specific-folders-accessibility.png' ); ?>"/>
            </div>

            <div class="col-description">
                <h2><?php esc_html_e( 'Specific Folders Accessibility', 'integrate-google-drive' ); ?>
                    <span class="badge"><?php esc_html_e( 'New ⚡', 'integrate-google-drive' ); ?></span>
                </h2>
                <p>
					<?php esc_html_e( 'Restrict access to specific folders within the plugin. This feature enhances security and control over your Google Drive files and ensures that only the designated folders are available for use and management within the app.', 'integrate-google-drive' ); ?>
                </p>
            </div>
        </section>

        <div class="section-wrap  igd-hidden">
            <section class="section-private-folders section-half">
                <div class="col-description">
                    <h2><?php esc_html_e( 'Private Folders', 'integrate-google-drive' ); ?></h2>
                    <p>
						<?php esc_html_e( 'Using Private Folders you can easily and securely share your Google Drive documents with your users/clients.', 'integrate-google-drive' ); ?>
                    </p>
                </div>

                <div class="col-image">
                    <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/private-folders.png' ); ?>"/>
                </div>
            </section>

            <section class="section-multiple-accounts section-half">
                <div class="col-description">
                    <h2><?php esc_html_e( 'Multiple Google Accounts', 'integrate-google-drive' ); ?></h2>
                    <p><?php esc_html_e( 'You can link multiple Google accounts and can use files from the multiple accounts.', 'integrate-google-drive' ); ?></p>
                </div>

                <div class="col-image">
                    <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/multiple-accounts.png' ); ?>"/>
                </div>
            </section>
        </div>

        <div class="section-wrap igd-hidden">

            <!-- Media Library Integration -->
            <section class="section-media-library section-half">
                <div class="col-description">
                    <h2><?php esc_html_e( 'Media Library Integration', 'integrate-google-drive' ); ?></h2>
                    <p>
						<?php esc_html_e( 'Integrate Google Drive with WordPress Media Library to use Drive files as media attachments directly. This streamlines uploading, importing, and syncing media between WordPress and Google Drive.', 'integrate-google-drive' ); ?>
                    </p>
                </div>

                <div class="col-image">
                    <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/media-library.png' ); ?>"/>
                </div>
            </section>

            <!--        Media Library Import -->
            <section class="section-file-importer section-half">
                <div class="col-description">
                    <h2><?php esc_html_e( 'Media Library Import', 'integrate-google-drive' ); ?></h2>
                    <p>
						<?php esc_html_e( 'Import any Google Drive document and media files to your media library by one click and use
                    them on any post/ page.', 'integrate-google-drive' ); ?>
                    </p>
                </div>

                <div class="col-image">
                    <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/file-importer.png' ); ?>"/>
                </div>
            </section>

        </div>

        <div class="content-heading heading-integrations igd-hidden">
            <h2><?php echo sprintf( __( '%s Powerful Integrations %s with Popular Plugins', 'integrate-google-drive' ), '<mark>', '</mark>' ); ?></h2>
            <p><?php esc_html_e( 'Using this plugin, you can integrate your Google Drive with available popular plugins.', 'integrate-google-drive' ); ?> </p>
        </div>

        <!--  Page Builder Integrations -->
        <section class="section-page-builders section-full igd-hidden">
            <div class="col-description">
                <h2><?php esc_html_e( 'Popular Page Builder Supports', 'integrate-google-drive' ); ?></h2>
                <p>
					<?php esc_html_e( 'The plugin supports all major page builders worldwide, ensuring 100% compatibility with your favorite tools. With Integrate Google Drive, you’re ready to work seamlessly with whichever page builder you choose.', 'integrate-google-drive' ); ?>
                </p>
            </div>

            <div class="col-image">
                <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/page-builders.png' ); ?>"/>
            </div>
        </section>

        <!--  Form Integrations -->
        <section class="section-form-integrations section-full igd-hidden">
            <div class="col-image">
                <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/form-integrations.png' ); ?>"/>
            </div>

            <div class="col-description">
                <h2><?php esc_html_e( 'Popular Form Plugin Supports', 'integrate-google-drive' ); ?></h2>
                <p>
					<?php esc_html_e( 'Upload files directly to Google Drive through your website’s integration with popular form plugins. Build a powerful module for your favorite forms using the Integrate Google Drive plugin.', 'integrate-google-drive' ); ?>
                </p>
            </div>
        </section>
    </div>

    <div class="content-heading heading-features">
        <h2><?php _e( 'Explore Our Popular Plugins', 'integrate-google-drive' ) ?>
            <mark><?php _e( ' You Might Love.', 'integrate-google-drive' ) ?></mark>
        </h2>
        <p><?php _e( 'Integrate Google Drive is developed by the same team behind some of the most popular WordPress plugins.', 'integrate-google-drive' ); ?></p>
    </div>

    <div class="popular-plugins-wrap">
		<?php

		foreach ( $plugins as $index => $item ):
			$plugin_img_url = IGD_ASSETS . '/images/getting-started/plugins/' . $item['key'] . '.png';
			$install_url = esc_url( admin_url( "plugin-install.php?tab=plugin-information&plugin={$item['key']}" ) );
			$learn_more_url = esc_url( "https://softlabbd.com/{$item['key']}" );
			?>
            <div class="popular-plugin-item" style="--plugin-color: <?php echo esc_attr( $item['color'] ); ?>;">
                <img src="<?php echo $plugin_img_url; ?>" alt="<?php echo esc_attr( $item['title'] ); ?>"/>

                <div class="plugin-info">
                    <h3><?php echo esc_html( $item['title'] ); ?></h3>
                    <p><?php echo esc_html( $item['desc'] ); ?></p>

                        <div class="users-count">
                            <i class="dashicons dashicons-admin-users"></i>
                            <?php echo $item['users']; ?> <?php _e('Active Users', 'integrate-google-drive'); ?>
                        </div>

                </div>

                <div class="button-group">
                    <a class="igd-btn btn-primary"
                       href="<?php echo $install_url; ?>">
                        <i class="dashicons dashicons-admin-plugins"></i>
						<?php _e( 'Install Now', 'integrate-google-drive' ); ?>
                    </a>

                    <a class="igd-btn btn-info"
                       href="<?php echo $learn_more_url; ?>"
                       target="_blank">
                        <i class="dashicons dashicons-external"></i>
						<?php _e( 'Learn More', 'integrate-google-drive' ); ?>
                    </a>
                </div>
            </div>
		<?php endforeach; ?>

    </div>

</div>