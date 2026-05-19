<?php

$faqs = [
	[
		'question' => __( 'I have a pre-sale question. How can I get support?', 'integrate-google-drive' ),
		'answer'   => sprintf(
			__( 'For any pre-sale inquiries, please contact us directly by submitting a form here: %s', 'integrate-google-drive' ),
			'<a href="https://softlabbd.com/contact/" target="_blank" rel="noopener noreferrer">Contact Us</a>'
		),
	],

	[
		'question' => __( 'I have purchased a plan, but it still shows the free plan. What should I do?', 'integrate-google-drive' ),
		'answer'   => __( 'After purchasing the PRO plugin, download and install it on your website. Deactivate the Free plugin first (your data will remain intact). Once the PRO plugin is installed, activate your license key. For details on where to find the download link and license key, refer to the related FAQ.', 'integrate-google-drive' ),
	],

	[
		'question' => __( 'Where can I find the PRO download link and license key?', 'integrate-google-drive' ),
		'answer'   => sprintf(
			__( 'After purchase, you should receive a confirmation email containing the PRO download link and license key. If you did not receive it due to email delivery issues, you can access your PRO download and license key from the %s.', 'integrate-google-drive' ),
			'<a href="https://users.freemius.com/store/1760/" target="_blank" rel="noopener noreferrer">Freemius Customer Portal</a>'
		),

	],

	[
		'question' => __( 'Can I use the same license key on my production, staging, and development sites?', 'integrate-google-drive' ),
		'answer'   => <<<HTML
<p>If you’re using a staging or localhost site alongside your live site, you may use the same license key for all, provided the domain clearly identifies as a dev or staging environment.</p>

<p>Whitelisted TLDs considered as dev or staging:</p>
<ul>
    <li><code>*.dev</code></li>
    <li><code>*.dev.cc</code> (DesktopServer)</li>
    <li><code>*.test</code></li>
    <li><code>*.local</code></li>
    <li><code>*.staging</code></li>
    <li><code>*.example</code></li>
    <li><code>*.invalid</code></li>
</ul>

<p>Whitelisted subdomains considered as dev or staging:</p>
<ul>
    <li><code>local.*</code></li>
    <li><code>dev.*</code></li>
    <li><code>test.*</code></li>
    <li><code>stage.*</code></li>
    <li><code>staging.*</code></li>
    <li><code>stagingN.*</code> (SiteGround; N is an unsigned int)</li>
    <li><code>*.myftpupload.com</code> (GoDaddy)</li>
    <li><code>*.cloudwaysapps.com</code> (Cloudways)</li>
    <li><code>*.wpsandbox.pro</code> (WPSandbox)</li>
    <li><code>*.ngrok.io</code> (tunneling)</li>
    <li><code>*.mystagingwebsite.com</code> (Pressable)</li>
    <li><code>*.tempurl.host</code> (WPMU DEV)</li>
    <li><code>*.wpmudev.host</code> (WPMU DEV)</li>
    <li><code>*.websitepro-staging.com</code> (Vendasta)</li>
    <li><code>*.websitepro.hosting</code> (Vendasta)</li>
    <li><code>*.instawp.xyz</code> (InstaWP)</li>
    <li><code>*.wpengine.com</code> (WP Engine)</li>
    <li><code>*.wpenginepowered.com</code> (WP Engine)</li>
    <li><code>dev-*.pantheonsite.io</code> (Pantheon)</li>
    <li><code>test-*.pantheonsite.io</code> (Pantheon)</li>
    <li><code>staging-*.kinsta.com</code> (Kinsta)</li>
    <li><code>staging-*.kinsta.cloud</code> (Kinsta)</li>
    <li><code>*-dev.10web.site</code> (10Web)</li>
    <li><code>*-dev.10web.cloud</code> (10Web)</li>
</ul>

<p>Domains using <code>localhost</code> (any port) are also treated as localhost domains.</p>

<p>If your dev site’s domain does not match these, you can deactivate the license from the Account page in your WP Admin dashboard, and then reuse it on your staging or dev site.</p>
HTML
	],
	[
		'question' => __( 'Can I try a live demo version of the plugin?', 'integrate-google-drive' ),
		'answer'   => sprintf(
			__( 'Yes! You can try the ready-made live demo of the PRO plugin to explore all features on both Front-End and Back-End. %s', 'integrate-google-drive' ),
			'<a href="https://demo.softlabbd.com/?product=integrate-google-drive" target="_blank" rel="noopener noreferrer">Try Live Demo</a>'
		),
	],

	[
		'question' => __( 'Is a trial version of the PRO plugin available?', 'integrate-google-drive' ),
		'answer'   => sprintf(
			__( 'Yes, we offer a 3-day trial period for the PRO plugin, allowing you to test its full capabilities on your website before purchase. No payment method is required. Get your trial here: %s', 'integrate-google-drive' ),
			'<a href="' . igd_fs()->get_trial_url() . '" target="_blank" rel="noopener noreferrer">Get Trial Version</a>'
		),
	],

	[
		'question' => __( 'How can I allow my users or clients to select, review, and approve specific Google Drive files?', 'integrate-google-drive' ),
		'answer'   => __( 'You can use the Review & Approve module along with the Gallery module to enable users or clients to select, review, and approve specific Google Drive files. This setup offers a user-friendly interface where clients can easily choose files, tag them with custom labels for better organization, and submit their selections. You’ll receive these submissions via email or can view them directly within the module’s selection management area.', 'integrate-google-drive' ),
	],

	[
		'question' => __( 'Are files uploaded directly to Google Drive?', 'integrate-google-drive' ),
		'answer'   => __( 'Yes, files are uploaded directly to Google Drive, so your server storage and performance are unaffected.', 'integrate-google-drive' ),
	],
	[
		'question' => __( 'Is there a file size limit for uploads via the File Uploader module?', 'integrate-google-drive' ),
		'answer'   => __( 'No, the File Uploader module allows uploading files of unlimited size to your Google Drive.', 'integrate-google-drive' ),
	],
	[
		'question' => __( 'Can I link a Google Drive folder to a registered user on my site?', 'integrate-google-drive' ),
		'answer'   => __( 'Yes, you can link a Google Drive folder to registered users. You may also manually link private folders to users.', 'integrate-google-drive' ),
	],
	[
		'question' => __( 'Can I connect multiple Google Drive accounts to the plugin?', 'integrate-google-drive' ),
		'answer'   => __( 'Yes, the plugin supports linking multiple Google Drive accounts.', 'integrate-google-drive' ),
	],
	[
		'question' => __( 'How can I embed direct Audio, Video, or Image files into my pages or posts?', 'integrate-google-drive' ),
		'answer'   => __( 'Use the Embed module via shortcode builder, classic editor, Gutenberg, Elementor, or Divi page builder. Enable the "Direct Media Display" option under the "Advanced" tab to display media files directly instead of through the embedded player.', 'integrate-google-drive' ),
	],
	[
		'question' => __( 'Are there any usage limits?', 'integrate-google-drive' ),
		'answer'   => __( 'There are no bandwidth or file size restrictions imposed by the plugin. Media files stream directly from Google Drive. However, zip file downloads are routed through your site and may generate traffic.', 'integrate-google-drive' ),
	],
];


?>


<div id="help" class="igd-help getting-started-content">

    <div class="content-heading heading-questions">
        <h2><?php printf( __( 'Frequently Asked %s Questions %s', 'integrate-google-drive' ), '<mark>', '</mark>' ); ?></h2>
    </div>

    <section class="section-faq">
		<?php foreach ( $faqs as $faq ) : ?>
            <div class="faq-item">
                <div class="faq-header">
                    <i class="dashicons dashicons-arrow-down-alt2"></i>
                    <h3><?php echo $faq['question']; ?></h3>
                </div>

                <div class="faq-body">
                    <p><?php echo $faq['answer']; ?></p>
                </div>
            </div>
		<?php endforeach; ?>
    </section>

    <div class="content-heading heading-tutorials">
        <h2><?php esc_html_e( 'Video ', 'integrate-google-drive' ); ?>
            <mark><?php esc_html_e( 'Tutorials', 'integrate-google-drive' ); ?></mark>
        </h2>
        <p><?php esc_html_e( 'Watch our detailed video tutorials to easily master the plugin—from setup to advanced features.', 'integrate-google-drive' ); ?></p>
    </div>

    <section class="section-video-tutorials section-half">
        <div class="col-image">
            <iframe src="https://www.youtube.com/embed/videoseries?list=PLaR5hjDXnXZyQI6LU-1Ijz_x9vkXQop7I&rel=0"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
            </iframe>
        </div>
    </section>

    <div class="content-heading heading-help">
        <h2><?php esc_html_e( 'Need ', 'integrate-google-drive' ); ?>
            <mark><?php esc_html_e( 'Help?', 'integrate-google-drive' ); ?></mark>
        </h2>
        <p><?php esc_html_e( 'Read our knowledge base documentation or you can contact us directly.', 'integrate-google-drive' ); ?></p>
    </div>

    <div class="section-wrap">
        <section class="section-documentation section-half">
            <div class="col-image">
                <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/documentation.png' ); ?>"
                     alt="<?php esc_attr_e( 'Documentation', 'integrate-google-drive' ); ?>">
            </div>
            <div class="col-description">
                <h2><?php _e( 'Documentation', 'integrate-google-drive' ) ?></h2>
                <p>
					<?php esc_html_e( 'Check out our detailed online documentation and video tutorials to find out more about what you can
                    do.', 'integrate-google-drive' ); ?>
                </p>
                <a class="igd-btn btn-primary" href="https://softlabbd.com/docs-category/integrate-google-drive-docs/"
                   target="_blank">
                    <i class="dashicons dashicons-media-text"></i>
					<?php esc_html_e( 'Documentation', 'integrate-google-drive' ); ?>
                </a>
            </div>
        </section>

        <section class="section-contact section-half">
            <div class="col-image">
                <img src="<?php echo esc_url( IGD_ASSETS . '/images/getting-started/contact.png' ); ?>"
                     alt="<?php esc_attr_e( 'Contact', 'integrate-google-drive' ); ?>">
            </div>
            <div class="col-description">
                <h2><?php esc_html_e( 'Support', 'integrate-google-drive' ); ?></h2>
                <p><?php esc_html_e( 'We have dedicated support team to provide you fast, friendly & top-notch customer support.', 'integrate-google-drive' ); ?></p>
                <a class="igd-btn btn-primary" href="https://softlabbd.com/support" target="_blank">
                    <i class="dashicons dashicons-sos"></i>
					<?php esc_html_e( 'Get Support', 'integrate-google-drive' ); ?>
                </a>
            </div>
        </section>
    </div>

    <div class="facebook-cta">
        <div class="cta-content">
            <h2><?php esc_html_e( 'Join our Facebook Group', 'integrate-google-drive' ); ?></h2>
            <p>
				<?php esc_html_e( 'Discuss, and share your problems & solutions for Integrate Google Drive WordPress plugin. Let\'s make a
                better community, share ideas, solve problems and finally build good relations.', 'integrate-google-drive' ); ?>
            </p>
        </div>

        <div class="cta-btn">
            <a href="https://www.facebook.com/groups/integrate.google.drive.wp" class="igd-btn btn-primary"
               target="_blank"
            >
                <i class="dashicons dashicons-facebook"></i>
				<?php esc_html_e( 'Join Now', 'integrate-google-drive' ); ?>
            </a>
        </div>

    </div>

</div>

<script>
    jQuery(document).ready(function ($) {
        $('.igd-help .faq-item .faq-header').on('click', function () {
            $(this).parent().toggleClass('active');
        });
    });
</script>