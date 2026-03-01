<?php
/**
 * Admin-Klasse für WP Floormap
 * Registriert die Admin-Seite im WordPress-Backend.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WP_Floormap_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_filter( 'admin_body_class', array( $this, 'add_body_class' ) );
    }

    public function add_body_class( $classes ) {
        $screen = get_current_screen();
        if ( $screen && strpos( $screen->id, 'wp-floormap-editor' ) !== false ) {
            $classes .= ' wp-floormap-editor-page';
        }
        return $classes;
    }

    public function add_menu() {
        $floors = WP_Floormap_Database::get_floors();
        $has_floors = ! empty( $floors );

        // Hauptseite: Falls Stockwerke da sind, Karten-Editor zuerst. Sonst Konfiguration.
        $main_slug = $has_floors ? 'wp-floormap-editor' : 'wp-floormap';
        $main_title = $has_floors ? 'Karten-Editor' : 'WP Floormap';
        $main_callback = $has_floors ? array( $this, 'render_editor_page' ) : array( $this, 'render_page' );

        add_menu_page(
            'WP Floormap',
            'Floormap',
            'manage_options',
            $main_slug,
            $main_callback,
            'dashicons-location-alt',
            30
        );

        // Submenü: Karten-Editor (nur wenn Stockwerke da sind)
        if ( $has_floors ) {
            add_submenu_page(
                'wp-floormap-editor',
                'Karten-Editor',
                'Karten-Editor',
                'manage_options',
                'wp-floormap-editor',
                array( $this, 'render_editor_page' )
            );
        }

        // Submenü: Konfiguration
        add_submenu_page(
            $has_floors ? 'wp-floormap-editor' : 'wp-floormap',
            'Stockwerke & Konfiguration',
            'Konfiguration',
            'manage_options',
            'wp-floormap',
            array( $this, 'render_page' )
        );
    }

    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'wp-floormap' ) === false ) {
            return;
        }

        // Leaflet
        wp_enqueue_style(
            'leaflet',
            WP_FLOORMAP_PLUGIN_URL . 'assets/leaflet/leaflet.css',
            array(),
            WP_FLOORMAP_VERSION
        );
        wp_enqueue_script(
            'leaflet',
            WP_FLOORMAP_PLUGIN_URL . 'assets/leaflet/leaflet.js',
            array(),
            WP_FLOORMAP_VERSION,
            true
        );

        // Floormap CSS
        wp_enqueue_style(
            'wp-floormap',
            WP_FLOORMAP_PLUGIN_URL . 'assets/floormap.css',
            array( 'leaflet' ),
            WP_FLOORMAP_VERSION
        );
        // Admin CSS
        wp_enqueue_style(
            'wp-floormap-admin',
            WP_FLOORMAP_PLUGIN_URL . 'admin/admin.css',
            array(),
            WP_FLOORMAP_VERSION
        );
        // Floormap JS (enthält WPFloormapInit)
        wp_enqueue_script(
            'wp-floormap',
            WP_FLOORMAP_PLUGIN_URL . 'assets/floormap.js',
            array( 'leaflet' ),
            WP_FLOORMAP_VERSION,
            true
        );
        // Admin JS
        wp_enqueue_script(
            'wp-floormap-admin',
            WP_FLOORMAP_PLUGIN_URL . 'admin/admin.js',
            array( 'leaflet', 'wp-floormap' ),
            WP_FLOORMAP_VERSION,
            true
        );

        // Daten an JS übergeben
        $global_colors_raw = WP_Floormap_Database::get_config( 'globalColors', '[]' );
        $db_floors         = WP_Floormap_Database::get_floors();
        $floors            = array();

        foreach ( $db_floors as $floor ) {
            $floors[] = array(
                'id'       => (int) $floor['id'],
                'label'    => $floor['label'],
                'name'     => $floor['name'],
                'imageUrl' => $floor['image_url'],
                'width'    => (int) $floor['width'],
                'height'   => (int) $floor['height'],
            );
        }

        wp_localize_script( 'wp-floormap', 'WPFloormap', array(
            'apiBase'            => rest_url( 'wp-floormap/v1' ),
            'nonce'              => wp_create_nonce( 'wp_rest' ),
            'pluginUrl'          => WP_FLOORMAP_PLUGIN_URL,
            'appConfig'          => array(
                'defaultFloorId'     => (int) WP_Floormap_Database::get_config( 'defaultFloorId', 0 ),
                'labelZoomThreshold' => (float) WP_Floormap_Database::get_config( 'labelZoomThreshold', 0 ),
                'showAttribution'    => WP_Floormap_Database::get_config( 'showAttribution', 'true' ),
                'showPluginAttribution' => WP_Floormap_Database::get_config( 'showPluginAttribution', 'true' ),
                'globalColors'       => json_decode( $global_colors_raw, true ) ?: array(),
                'floors'             => $floors,
            ),
        ) );
    }

    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Keine Berechtigung.' );
        }
        require_once WP_FLOORMAP_PLUGIN_DIR . 'admin/admin-page.php';
    }

    public function render_editor_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Keine Berechtigung.' );
        }
        require_once WP_FLOORMAP_PLUGIN_DIR . 'admin/editor-page.php';
    }
}
