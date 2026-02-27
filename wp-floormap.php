<?php
/**
 * Plugin Name: WP Floormap
 * Plugin URI:  https://github.com/hellodosi/WP-Floormap
 * Description: Interaktive Gebäudekarte als WordPress-Plugin. Stockwerke und Kartenelemente werden in der WordPress-Datenbank gespeichert. Die Karte kann als Shortcode, Widget oder Elementor-Widget eingebunden werden.
 * Version:     1.0.0
 * Author:      Your Name
 * License:     GPL-2.0+
 * Text Domain: wp-floormap
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Konstanten
define( 'WP_FLOORMAP_VERSION', '1.0.0' );
define( 'WP_FLOORMAP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_FLOORMAP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_FLOORMAP_UPLOAD_DIR', 'wp-floormap' ); // Unterordner in wp-content/uploads

// Klassen laden
require_once WP_FLOORMAP_PLUGIN_DIR . 'includes/class-database.php';
require_once WP_FLOORMAP_PLUGIN_DIR . 'includes/class-api.php';
require_once WP_FLOORMAP_PLUGIN_DIR . 'includes/class-admin.php';
require_once WP_FLOORMAP_PLUGIN_DIR . 'includes/class-shortcode.php';

// Plugin-Aktivierung: Datenbanktabellen erstellen
register_activation_hook( __FILE__, array( 'WP_Floormap_Database', 'create_tables' ) );

// Plugin initialisieren
add_action( 'plugins_loaded', 'wp_floormap_init' );

function wp_floormap_init() {
    // Admin-Bereich
    if ( is_admin() ) {
        new WP_Floormap_Admin();
    }

    // REST API
    add_action( 'rest_api_init', array( 'WP_Floormap_API', 'register_routes' ) );

    // Update Checker initialisieren
    wp_floormap_init_updater();

    // Shortcode registrieren
    WP_Floormap_Shortcode::init();

    // Elementor Widget (falls Elementor aktiv)
    add_action( 'elementor/widgets/register', function( $widgets_manager ) {
        if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
            return;
        }
        require_once WP_FLOORMAP_PLUGIN_DIR . 'includes/class-elementor-widget.php';
        if ( class_exists( 'WP_Floormap_Elementor_Widget' ) ) {
            $widgets_manager->register( new WP_Floormap_Elementor_Widget() );
        }
    } );
}

/**
 * Hilfsfunktion: Upload-Verzeichnis für das Plugin ermitteln
 */
function wp_floormap_upload_dir() {
    $upload = wp_upload_dir();
    return array(
        'path'    => $upload['basedir'] . '/' . WP_FLOORMAP_UPLOAD_DIR,
        'url'     => $upload['baseurl'] . '/' . WP_FLOORMAP_UPLOAD_DIR,
        'icons'   => $upload['basedir'] . '/' . WP_FLOORMAP_UPLOAD_DIR . '/icons',
        'icons_url' => $upload['baseurl'] . '/' . WP_FLOORMAP_UPLOAD_DIR . '/icons',
        'maps'    => $upload['basedir'] . '/' . WP_FLOORMAP_UPLOAD_DIR . '/mapdata',
        'maps_url'  => $upload['baseurl'] . '/' . WP_FLOORMAP_UPLOAD_DIR . '/mapdata',
    );
}

/**
 * Hilfsfunktion: Upload-Verzeichnisse anlegen (einmalig)
 */
function wp_floormap_ensure_upload_dirs() {
    $dirs = wp_floormap_upload_dir();
    foreach ( array( $dirs['path'], $dirs['icons'], $dirs['maps'] ) as $dir ) {
        if ( ! file_exists( $dir ) ) {
            wp_mkdir_p( $dir );
            // .htaccess zum Schutz (optional, nur für Apache)
            file_put_contents( $dir . '/.htaccess', "Options -Indexes\n" );
        }
    }
}
add_action( 'admin_init', 'wp_floormap_ensure_upload_dirs' );

/**
 * Initialisiert den Update-Checker für das Plugin.
 */
function wp_floormap_init_updater() {
    $puc_path = WP_FLOORMAP_PLUGIN_DIR . 'plugin-update-checker/plugin-update-checker.php';
    if ( file_exists( $puc_path ) ) {
        require_once $puc_path;
        $myUpdateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
            'https://github.com/hellodosi/WP-Floormap/',
            __FILE__,
            'wp-floormap'
        );
    }
}
