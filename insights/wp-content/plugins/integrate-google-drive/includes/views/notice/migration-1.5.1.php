<?php
$state          = get_option( 'igd_migration_1_5_1_state', [] );
$current_step   = isset( $state['step'] ) ? esc_html( $state['step'] ) : __( 'N/A', 'integrate-google-drive' );
$current_offset = isset( $state['offset'] ) ? intval( $state['offset'] ) : 0;
?>

<div class="notice-image">
    <img src="<?php echo IGD_ASSETS . '/images/integrate-google-drive-logo.png'; ?>">
</div>

<div class="notice-main igd-migration-notice" id="igd-migrate-notice">

    <div class="notice-text">
        <p><strong><?php _e( 'Database Migration in Progress', 'integrate-google-drive' ); ?></strong></p>
        <p><?php _e( 'We are updating your Google Drive module configurations in the background. This may take a few moments. You can continue using the site normally.', 'integrate-google-drive' ); ?></p>

        <div class="migration-status">
            <p>
				<?php printf( esc_html__( 'Current Step: %s', 'integrate-google-drive' ), "<code>{$current_step}</code>" ); ?>
                <br>
				<?php printf( esc_html__( 'Processed Offset: %d', 'integrate-google-drive' ), $current_offset ); ?>
            </p>
        </div>
    </div>

    <div id="igd-migration-progress" class="igd-migration-progress">
        <img src="<?php echo includes_url( '/images/spinner.gif' ); ?>" style="display:none;">
        <p class="progress-message"><?php _e( 'Checking migration status...', 'integrate-google-drive' ); ?></p>
    </div>

    <div class="notice-actions">

        <p class="notice-actions-text">
			<?php _e( 'If you having any trouble, click the button below to manually trigger the migration process. This is not recommended unless you are experiencing issues with the automatic migration.', 'integrate-google-drive' ); ?>
        </p>

        <button class="button button-primary" id="igd-start-migration">
			<?php _e( 'Update Database', 'integrate-google-drive' ); ?>
        </button>
    </div>

</div>
