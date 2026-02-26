<?php
/**
 * Uninstall handler for WP Floormap
 *
 * Deletes all plugin data (DB tables content and uploaded files) if the setting
 * keepDataOnUninstall is disabled (false). Otherwise keeps all data.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

// Determine table names
$floors_table   = $wpdb->prefix . 'floormap_floors';
$elements_table = $wpdb->prefix . 'floormap_elements';
$config_table   = $wpdb->prefix . 'floormap_config';

// Read config value 'keepDataOnUninstall' (defaults to true)
$keep = $wpdb->get_var( $wpdb->prepare( "SELECT config_value FROM {$config_table} WHERE config_key = %s", 'keepDataOnUninstall' ) );
$keep = ( $keep === null ) ? 'true' : $keep; // default: keep data
$keep_bool = ( $keep === 'true' || $keep === '1' || $keep === 1 || $keep === true );

if ( $keep_bool ) {
    // Do nothing: keep data on uninstall
    return;
}

// 1) Truncate tables (ignore errors if they don't exist)
@$wpdb->query( "TRUNCATE TABLE {$floors_table}" );
@$wpdb->query( "TRUNCATE TABLE {$elements_table}" );
@$wpdb->query( "TRUNCATE TABLE {$config_table}" );

// 2) Delete uploads directory wp-content/uploads/wp-floormap
$upload = wp_upload_dir();
$base   = trailingslashit( $upload['basedir'] ) . 'wp-floormap';

if ( is_dir( $base ) ) {
    $iterator = new RecursiveDirectoryIterator( $base, FilesystemIterator::SKIP_DOTS );
    $files    = new RecursiveIteratorIterator( $iterator, RecursiveIteratorIterator::CHILD_FIRST );
    foreach ( $files as $file ) {
        if ( $file->isDir() ) {
            @rmdir( $file->getRealPath() );
        } else {
            @unlink( $file->getRealPath() );
        }
    }
    @rmdir( $base );
}

// Done
