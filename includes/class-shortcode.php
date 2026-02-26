<?php
/**
 * Shortcode-Klasse für WP Floormap
 * Verwendung: [wp_floormap] oder [wp_floormap theme="dark" height="600px" find="Raumname"]
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WP_Floormap_Shortcode {

    public static function init() {
        add_shortcode( 'wp_floormap', array( __CLASS__, 'render' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_assets' ) );
    }

    public static function register_assets() {
        wp_register_style(
            'leaflet',
            WP_FLOORMAP_PLUGIN_URL . 'assets/leaflet/leaflet.css',
            array(),
            WP_FLOORMAP_VERSION
        );
        wp_register_script(
            'leaflet',
            WP_FLOORMAP_PLUGIN_URL . 'assets/leaflet/leaflet.js',
            array(),
            WP_FLOORMAP_VERSION,
            true
        );
        wp_register_style(
            'wp-floormap-frontend',
            WP_FLOORMAP_PLUGIN_URL . 'assets/floormap.css',
            array( 'leaflet' ),
            WP_FLOORMAP_VERSION
        );
        wp_register_script(
            'wp-floormap-frontend',
            WP_FLOORMAP_PLUGIN_URL . 'assets/floormap.js',
            array( 'leaflet' ),
            WP_FLOORMAP_VERSION,
            true
        );
    }

    public static function render( $atts ) {
        $atts = shortcode_atts( array(
            'theme'     => 'auto',
            'height'    => '600px',
            'find'      => '',
            'floor'     => '',
        ), $atts, 'wp_floormap' );

        // Assets einbinden
        wp_enqueue_style( 'leaflet' );
        wp_enqueue_script( 'leaflet' );
        wp_enqueue_style( 'wp-floormap-frontend' );
        wp_enqueue_script( 'wp-floormap-frontend' );

        // Eindeutige ID für mehrere Karten auf einer Seite
        static $instance = 0;
        $instance++;
        $map_id = 'wp-floormap-' . $instance;

        // Konfiguration für JS
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

        $app_config = array(
            'defaultFloorId'     => (int) WP_Floormap_Database::get_config( 'defaultFloorId', 0 ),
            'labelZoomThreshold' => (float) WP_Floormap_Database::get_config( 'labelZoomThreshold', 0 ),
            'showAttribution'    => WP_Floormap_Database::get_config( 'showAttribution', 'true' ),
            'globalColors'       => json_decode( $global_colors_raw, true ) ?: array(),
            'floors'             => $floors,
        );

        // floor-Attribut überschreibt defaultFloorId
        if ( $atts['floor'] !== '' ) {
            $app_config['defaultFloorId'] = intval( $atts['floor'] );
        }

        $config_json   = wp_json_encode( $app_config );
        $api_base      = esc_url( rest_url( 'wp-floormap/v1' ) );
        $theme         = esc_attr( $atts['theme'] );
        $height        = esc_attr( $atts['height'] );
        $initial_find  = esc_attr( $atts['find'] );

        // Sonderfall: height="auto" soll die verfügbare Höhe des umgebenden Containers nutzen
        $is_auto_height = ( strtolower( trim( $height ) ) === 'auto' );
        $wrap_height_css = $is_auto_height ? 'height:100%;' : ('height:' . $height . ';');

        ob_start();
        ?>
        <div class="wp-floormap-wrap" style="position:relative; width:100%; <?php echo $wrap_height_css; ?>">
            <div id="<?php echo esc_attr( $map_id ); ?>" class="wp-floormap-container" style="width:100%; height:100%;"></div>
        </div>
        <script>
        (function() {
            var mapId      = <?php echo wp_json_encode( $map_id ); ?>;
            var APP_CONFIG = <?php echo $config_json; ?>;
            var API_BASE   = <?php echo wp_json_encode( $api_base ); ?>;
            var THEME      = <?php echo wp_json_encode( $theme ); ?>;
            var INITIAL_FIND = <?php echo wp_json_encode( $initial_find ); ?>;
            var IS_DEV_MODE  = false;

            if (typeof WPFloormapInit === 'function') {
                WPFloormapInit(mapId, APP_CONFIG, API_BASE, THEME, INITIAL_FIND, IS_DEV_MODE);
            } else {
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof WPFloormapInit === 'function') {
                        WPFloormapInit(mapId, APP_CONFIG, API_BASE, THEME, INITIAL_FIND, IS_DEV_MODE);
                    }
                });
            }
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
