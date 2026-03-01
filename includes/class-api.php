<?php
/**
 * REST API für WP Floormap
 * Registriert alle Endpunkte unter /wp-json/wp-floormap/v1/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WP_Floormap_API {

    const API_NAMESPACE = 'wp-floormap/v1';

    /**
     * Sanitize-Callback für Ganzzahl-Parameter in REST-Routen.
     * Muss die 3-Argument-Signatur von WordPress unterstützen.
     */
    public static function sanitize_int_param( $value, $request, $param ) {
        return (int) $value;
    }

    public static function register_routes() {
        // ... (existing routes)
        
        // Alle Daten löschen (Admin)
        register_rest_route( self::API_NAMESPACE, '/all-data', array(
            'methods'             => 'DELETE',
            'callback'            => array( __CLASS__, 'delete_all_data' ),
            'permission_callback' => array( __CLASS__, 'check_admin' ),
        ) );

        // Stockwerk-Daten (Frontend, öffentlich)
        register_rest_route( self::API_NAMESPACE, '/floor/(?P<id>-?\d+)', array(
            'methods'             => 'GET',
            'callback'            => array( __CLASS__, 'get_floor_data' ),
            'permission_callback' => '__return_true',
            'args'                => array(
                'id' => array( 'required' => true, 'sanitize_callback' => array( __CLASS__, 'sanitize_int_param' ) ),
            ),
        ) );

        // App-Config (Frontend, öffentlich)
        register_rest_route( self::API_NAMESPACE, '/config', array(
            'methods'             => 'GET',
            'callback'            => array( __CLASS__, 'get_app_config' ),
            'permission_callback' => '__return_true',
        ) );

        // Element erstellen (Admin)
        register_rest_route( self::API_NAMESPACE, '/elements', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'create_element' ),
            'permission_callback' => array( __CLASS__, 'check_admin' ),
        ) );

        // Element aktualisieren (Admin)
        register_rest_route( self::API_NAMESPACE, '/elements/(?P<id>[a-zA-Z0-9_\-]+)', array(
            'methods'             => 'PUT',
            'callback'            => array( __CLASS__, 'update_element' ),
            'permission_callback' => array( __CLASS__, 'check_admin' ),
            'args'                => array(
                'id' => array( 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ),
            ),
        ) );

        // Element löschen (Admin)
        register_rest_route( self::API_NAMESPACE, '/elements/(?P<id>[a-zA-Z0-9_\-]+)', array(
            'methods'             => 'DELETE',
            'callback'            => array( __CLASS__, 'delete_element' ),
            'permission_callback' => array( __CLASS__, 'check_admin' ),
            'args'                => array(
                'id' => array( 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ),
            ),
        ) );

        // Stockwerk erstellen (Admin)
        register_rest_route( self::API_NAMESPACE, '/floors', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'create_floor' ),
            'permission_callback' => array( __CLASS__, 'check_admin' ),
        ) );

        // Stockwerk aktualisieren (Admin)
        register_rest_route( self::API_NAMESPACE, '/floors/(?P<id>-?\d+)', array(
            'methods'             => 'PUT',
            'callback'            => array( __CLASS__, 'update_floor' ),
            'permission_callback' => array( __CLASS__, 'check_admin' ),
            'args'                => array(
                'id' => array( 'required' => true, 'sanitize_callback' => array( __CLASS__, 'sanitize_int_param' ) ),
            ),
        ) );

        // Stockwerk löschen (Admin)
        register_rest_route( self::API_NAMESPACE, '/floors/(?P<id>-?\d+)', array(
            'methods'             => 'DELETE',
            'callback'            => array( __CLASS__, 'delete_floor' ),
            'permission_callback' => array( __CLASS__, 'check_admin' ),
            'args'                => array(
                'id' => array( 'required' => true, 'sanitize_callback' => array( __CLASS__, 'sanitize_int_param' ) ),
            ),
        ) );

        // Stockwerke sortieren (Admin)
        register_rest_route( self::API_NAMESPACE, '/floors/reorder', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'reorder_floors' ),
            'permission_callback' => array( __CLASS__, 'check_admin' ),
        ) );

        // Config speichern (Admin)
        register_rest_route( self::API_NAMESPACE, '/config', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'save_config' ),
            'permission_callback' => array( __CLASS__, 'check_admin' ),
        ) );

        // Icon hochladen (Admin)
        register_rest_route( self::API_NAMESPACE, '/icons', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'upload_icon' ),
            'permission_callback' => array( __CLASS__, 'check_admin' ),
        ) );

        // Icons auflisten (Admin)
        register_rest_route( self::API_NAMESPACE, '/icons', array(
            'methods'             => 'GET',
            'callback'            => array( __CLASS__, 'list_icons' ),
            'permission_callback' => array( __CLASS__, 'check_admin' ),
        ) );

        // Icon löschen (Admin)
        register_rest_route( self::API_NAMESPACE, '/icons/(?P<filename>[^/]+)', array(
            'methods'             => 'DELETE',
            'callback'            => array( __CLASS__, 'delete_icon' ),
            'permission_callback' => array( __CLASS__, 'check_admin' ),
        ) );

        // Kartenbild für ein Stockwerk hochladen (Admin) – überschreibt vorhandenes
        register_rest_route( self::API_NAMESPACE, '/floors/(?P<id>-?\d+)/map-upload', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'upload_floor_map' ),
            'permission_callback' => array( __CLASS__, 'check_admin' ),
            'args'                => array(
                'id' => array( 'required' => true, 'sanitize_callback' => array( __CLASS__, 'sanitize_int_param' ) ),
            ),
        ) );

        // Hintergrundbild hochladen (Admin, generisch – Legacy)
        register_rest_route( self::API_NAMESPACE, '/maps', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'upload_map' ),
            'permission_callback' => array( __CLASS__, 'check_admin' ),
        ) );

        // Export (Admin)
        register_rest_route( self::API_NAMESPACE, '/export', array(
            'methods'             => 'GET',
            'callback'            => array( __CLASS__, 'export_data' ),
            'permission_callback' => array( __CLASS__, 'check_admin' ),
        ) );

        // Import Preview – JSON einlesen, analysieren, NICHT speichern (Admin)
        register_rest_route( self::API_NAMESPACE, '/import/preview', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'import_preview' ),
            'permission_callback' => array( __CLASS__, 'check_admin' ),
        ) );

        // Import (Admin)
        register_rest_route( self::API_NAMESPACE, '/import', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'import_data' ),
            'permission_callback' => array( __CLASS__, 'check_admin' ),
        ) );
    }

    /**
     * Berechtigungsprüfung: nur eingeloggte Admins
     */
    public static function check_admin( $request ) {
        return current_user_can( 'manage_options' );
    }

    // ========== PUBLIC ENDPOINTS ==========

    public static function get_floor_data( $request ) {
        $floor_id = $request->get_param( 'id' );
        $floor = WP_Floormap_Database::get_floor_by_id( $floor_id );

        if ( ! $floor ) {
            return new WP_Error( 'not_found', 'Stockwerk nicht gefunden', array( 'status' => 404 ) );
        }

        $db_elements = WP_Floormap_Database::get_elements_by_floor( $floor_id );
        $elements = array();

        foreach ( $db_elements as $el ) {
            $elements[] = array(
                'id'              => $el['id'],
                'name'            => $el['name'],
                'type'            => $el['type'],
                'color'           => $el['color'],
                'colorId'         => $el['color_id'],
                'description'     => $el['description'],
                'coords'          => json_decode( $el['coords'], true ),
                'icon'            => $el['icon'],
                'interactive'     => (int) $el['interactive'],
                'hide_label'      => (int) $el['hide_label'],
                'display_mode'    => $el['display_mode'],
                'icon_size'       => (int) $el['icon_size'],
                'label_direction' => $el['label_direction'],
            );
        }

        return rest_ensure_response( array(
            'name'     => $floor['name'],
            'imageUrl' => $floor['image_url'],
            'elements' => $elements,
        ) );
    }

    public static function get_app_config( $request ) {
        $global_colors_raw = WP_Floormap_Database::get_config( 'globalColors', '[]' );
        $db_floors = WP_Floormap_Database::get_floors();
        $floors = array();

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

        return rest_ensure_response( array(
            'defaultFloorId'        => (int) WP_Floormap_Database::get_config( 'defaultFloorId', 0 ),
            'labelZoomThreshold'    => (float) WP_Floormap_Database::get_config( 'labelZoomThreshold', 0 ),
            'showAttribution'       => WP_Floormap_Database::get_config( 'showAttribution', 'true' ),
            'showPluginAttribution' => WP_Floormap_Database::get_config( 'showPluginAttribution', 'true' ),
            'keepDataOnUninstall'   => WP_Floormap_Database::get_config( 'keepDataOnUninstall', 'true' ),
            'globalColors'          => json_decode( $global_colors_raw, true ) ?: array(),
            'floors'                => $floors,
        ) );
    }

    // ========== ELEMENT ENDPOINTS ==========

    public static function create_element( $request ) {
        $data     = $request->get_json_params();
        $floor_id = isset( $data['floorId'] ) ? intval( $data['floorId'] ) : null;
        $element  = isset( $data['element'] ) ? $data['element'] : null;

        if ( ! $floor_id || ! $element ) {
            return new WP_Error( 'missing_data', 'floorId und element erforderlich', array( 'status' => 400 ) );
        }

        if ( ! WP_Floormap_Database::get_floor_by_id( $floor_id ) ) {
            return new WP_Error( 'not_found', 'Stockwerk nicht gefunden', array( 'status' => 404 ) );
        }

        $success = WP_Floormap_Database::add_element(
            $element['id'],
            $floor_id,
            $element['name'] ?? 'Unbenannt',
            $element['type'] ?? 'polygon',
            $element['color'] ?? '#000000',
            $element['colorId'] ?? null,
            $element['description'] ?? '',
            $element['coords'] ?? '[]',
            $element['icon'] ?? null,
            $element['interactive'] ?? 1,
            $element['hide_label'] ?? 0,
            $element['display_mode'] ?? 'name',
            $element['icon_size'] ?? 32,
            $element['label_direction'] ?? 'auto'
        );

        if ( $success === false ) {
            return new WP_Error( 'db_error', 'Element konnte nicht gespeichert werden', array( 'status' => 500 ) );
        }

        return rest_ensure_response( array( 'success' => true, 'element' => $element ) );
    }

    public static function update_element( $request ) {
        $id      = $request->get_param( 'id' );
        $data    = $request->get_json_params();
        $element = isset( $data['element'] ) ? $data['element'] : null;

        if ( ! $element ) {
            return new WP_Error( 'missing_data', 'element-Daten fehlen', array( 'status' => 400 ) );
        }

        if ( ! WP_Floormap_Database::get_element_by_id( $id ) ) {
            return new WP_Error( 'not_found', 'Element nicht gefunden', array( 'status' => 404 ) );
        }

        $update = array(
            'name'            => $element['name'] ?? 'Unbenannt',
            'type'            => $element['type'] ?? 'polygon',
            'color'           => $element['color'] ?? '#000000',
            'color_id'        => $element['colorId'] ?? null,
            'description'     => $element['description'] ?? '',
            'coords'          => $element['coords'] ?? '[]',
            'icon'            => $element['icon'] ?? null,
            'icon_size'       => $element['icon_size'] ?? 32,
            'interactive'     => $element['interactive'] ?? 1,
            'hide_label'      => $element['hide_label'] ?? 0,
            'display_mode'    => $element['display_mode'] ?? 'name',
            'label_direction' => $element['label_direction'] ?? 'auto',
        );

        WP_Floormap_Database::update_element( $id, $update );

        return rest_ensure_response( array( 'success' => true, 'element' => $element ) );
    }

    public static function delete_element( $request ) {
        $id = $request->get_param( 'id' );

        if ( ! WP_Floormap_Database::get_element_by_id( $id ) ) {
            return new WP_Error( 'not_found', 'Element nicht gefunden', array( 'status' => 404 ) );
        }

        WP_Floormap_Database::delete_element( $id );

        return rest_ensure_response( array( 'success' => true ) );
    }

    // ========== FLOOR ENDPOINTS ==========

    public static function create_floor( $request ) {
        $data = $request->get_json_params();

        if ( empty( $data['name'] ) || empty( $data['label'] ) ) {
            return new WP_Error( 'missing_data', 'name und label erforderlich', array( 'status' => 400 ) );
        }

        $id = WP_Floormap_Database::add_floor(
            sanitize_text_field( $data['label'] ),
            sanitize_text_field( $data['name'] ),
            esc_url_raw( $data['imageUrl'] ?? '' ),
            intval( $data['width'] ?? 0 ),
            intval( $data['height'] ?? 0 ),
            intval( $data['sortOrder'] ?? 0 )
        );

        if ( $id === false ) {
            return new WP_Error( 'db_error', 'Stockwerk konnte nicht erstellt werden', array( 'status' => 500 ) );
        }

        return rest_ensure_response( array( 'success' => true, 'id' => $id ) );
    }

    public static function update_floor( $request ) {
        $id   = intval( $request->get_param( 'id' ) );
        $data = $request->get_json_params();

        if ( $id === 0 ) {
            return new WP_Error( 'invalid_id', 'Ungültige ID', array( 'status' => 400 ) );
        }

        if ( ! WP_Floormap_Database::get_floor_by_id( $id ) ) {
            return new WP_Error( 'not_found', 'Stockwerk nicht gefunden', array( 'status' => 404 ) );
        }

        $update = array();
        if ( isset( $data['label'] ) )     $update['label']      = sanitize_text_field( $data['label'] );
        if ( isset( $data['name'] ) )      $update['name']       = sanitize_text_field( $data['name'] );
        if ( isset( $data['imageUrl'] ) )  $update['image_url']  = esc_url_raw( $data['imageUrl'] );
        if ( isset( $data['width'] ) )     $update['width']      = intval( $data['width'] );
        if ( isset( $data['height'] ) )    $update['height']     = intval( $data['height'] );
        if ( isset( $data['sortOrder'] ) ) $update['sort_order'] = intval( $data['sortOrder'] );

        WP_Floormap_Database::update_floor( $id, $update );

        return rest_ensure_response( array( 'success' => true ) );
    }

    public static function delete_floor( $request ) {
        $id = intval( $request->get_param( 'id' ) );

        if ( $id === 0 ) {
            return new WP_Error( 'invalid_id', 'Ungültige ID', array( 'status' => 400 ) );
        }

        if ( ! WP_Floormap_Database::get_floor_by_id( $id ) ) {
            return new WP_Error( 'not_found', 'Stockwerk nicht gefunden', array( 'status' => 404 ) );
        }

        WP_Floormap_Database::delete_floor( $id );

        return rest_ensure_response( array( 'success' => true ) );
    }

    public static function reorder_floors( $request ) {
        $data = $request->get_json_params();
        $ids  = $data['ids'] ?? array();

        if ( ! is_array( $ids ) ) {
            return new WP_Error( 'invalid_data', 'ids muss ein Array sein', array( 'status' => 400 ) );
        }

        // Wir setzen die sort_order basierend auf der Position im Array.
        // Da wir im Frontend meist von "oben nach unten" anzeigen, 
        // und sort_order DESC sortiert wird, bekommt das erste Element die höchste Nummer.
        $count = count( $ids );
        foreach ( $ids as $index => $id ) {
            $sort_order = $count - $index;
            WP_Floormap_Database::update_floor( intval( $id ), array( 'sort_order' => $sort_order ) );
        }

        return rest_ensure_response( array( 'success' => true ) );
    }

    // ========== CONFIG ENDPOINT ==========

    public static function save_config( $request ) {
        $data = $request->get_json_params();

        if ( ! isset( $data['key'] ) || ! isset( $data['value'] ) ) {
            return new WP_Error( 'missing_data', 'key und value erforderlich', array( 'status' => 400 ) );
        }

        $allowed = array( 'globalColors', 'defaultFloorId', 'labelZoomThreshold', 'showAttribution', 'showPluginAttribution', 'keepDataOnUninstall' );
        if ( ! in_array( $data['key'], $allowed, true ) ) {
            return new WP_Error( 'invalid_key', 'Unbekannter Config-Key', array( 'status' => 400 ) );
        }

        $value = is_array( $data['value'] ) ? wp_json_encode( $data['value'] ) : $data['value'];
        WP_Floormap_Database::set_config( $data['key'], $value );

        return rest_ensure_response( array( 'success' => true, 'key' => $data['key'] ) );
    }

    public static function delete_all_data( $request ) {
        WP_Floormap_Database::delete_all_data();
        return rest_ensure_response( array( 'success' => true ) );
    }

    // ========== ICON ENDPOINTS ==========

    public static function upload_icon( $request ) {
        $files = $request->get_file_params();

        if ( empty( $files['icon'] ) || $files['icon']['error'] !== UPLOAD_ERR_OK ) {
            return new WP_Error( 'no_file', 'Keine gültige Datei hochgeladen', array( 'status' => 400 ) );
        }

        $file         = $files['icon'];
        $allowed_exts = array( 'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp' );
        $ext          = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
        if ( ! in_array( $ext, $allowed_exts, true ) ) {
            return new WP_Error( 'invalid_type', 'Nur Bild-Dateien erlaubt', array( 'status' => 400 ) );
        }

        $dirs     = wp_floormap_upload_dir();
        $ext      = pathinfo( $file['name'], PATHINFO_EXTENSION );
        $basename = preg_replace( '/[^a-zA-Z0-9_\-]/', '_', pathinfo( $file['name'], PATHINFO_FILENAME ) );
        $filename = $basename . '.' . $ext;
        $target   = $dirs['icons'] . '/' . $filename;

        $counter = 1;
        while ( file_exists( $target ) ) {
            $filename = $basename . '_' . $counter . '.' . $ext;
            $target   = $dirs['icons'] . '/' . $filename;
            $counter++;
        }

        if ( ! move_uploaded_file( $file['tmp_name'], $target ) ) {
            return new WP_Error( 'upload_failed', 'Datei konnte nicht gespeichert werden', array( 'status' => 500 ) );
        }

        return rest_ensure_response( array(
            'success'  => true,
            'filename' => $filename,
            'url'      => $dirs['icons_url'] . '/' . $filename,
        ) );
    }

    public static function list_icons( $request ) {
        $dirs  = wp_floormap_upload_dir();
        $path  = $dirs['icons'];
        $icons = array();

        if ( is_dir( $path ) ) {
            $allowed = array( 'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp' );
            foreach ( scandir( $path ) as $file ) {
                if ( $file === '.' || $file === '..' ) continue;
                $ext = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
                if ( ! in_array( $ext, $allowed, true ) ) continue;
                $icons[] = array(
                    'filename' => $file,
                    'url'      => $dirs['icons_url'] . '/' . $file,
                );
            }
        }

        return rest_ensure_response( array( 'success' => true, 'icons' => $icons ) );
    }

    public static function delete_icon( $request ) {
        $filename = sanitize_file_name( $request->get_param( 'filename' ) );
        $dirs     = wp_floormap_upload_dir();
        $path     = $dirs['icons'] . '/' . $filename;

        if ( ! file_exists( $path ) ) {
            return new WP_Error( 'not_found', 'Icon nicht gefunden', array( 'status' => 404 ) );
        }

        unlink( $path );

        return rest_ensure_response( array( 'success' => true ) );
    }

    // ========== MAP UPLOAD ==========

    /**
     * Einfache SVG-Optimierung: Entfernt Metadaten, Kommentare und unnötige XML-Namespaces (z.B. Inkscape/Sodipodi).
     * Reduziert die Dateigröße und vereinfacht das Rendering im Browser, während die Dimensionen erhalten bleiben.
     */
    private static function optimize_svg( $svg_content ) {
        // 1. Kommentare entfernen
        $svg_content = preg_replace( '/<!--.*?-->/s', '', $svg_content );

        // 2. Metadaten-Tags entfernen (<metadata>...</metadata>)
        $svg_content = preg_replace( '/<metadata>.*?<\/metadata>/is', '', $svg_content );

        // 3. Unnötige Attribute und Namespaces entfernen
        // Wir lassen xmlns, viewBox, width, height explizit in Ruhe, falls sie in den Namespaces auftauchen könnten (unwahrscheinlich aber sicherheitshalber)
        $svg_content = preg_replace( '/\s*(xmlns:inkscape|xmlns:sodipodi|xmlns:dc|xmlns:cc|xmlns:rdf|inkscape:[a-z-]+|sodipodi:[a-z-]+)=["\'][^"\']*["\']/i', '', $svg_content );

        // 4. Mehrfache Leerzeichen und Zeilenumbrüche minimieren (einfaches Minifying)
        // Aber Vorsicht: Leerzeichen innerhalb von Texten (Tags) sollten erhalten bleiben. 
        // Wir machen hier nur ein sehr vorsichtiges Minifying.
        $svg_content = preg_replace( '/>\s+</', '><', $svg_content );
        $svg_content = trim( $svg_content );

        return $svg_content;
    }

    /**
     * Prüft, ob ein bestimmtes Bildformat vom aktuellen Editor unterstützt wird.
     */
    private static function supports_format( $format ) {
        // wp_get_image_editor gibt uns einen Editor, wir prüfen ob er das Format speichern kann.
        // Da wir nicht jedes Mal einen Editor instanziieren wollen, nutzen wir die Klassen-Methoden falls möglich,
        // oder verlassen uns auf die Standard-WP-Funktionen.
        if ( $format === 'image/webp' ) {
            return function_exists( 'imagewebp' ) || ( class_exists( 'Imagick' ) && method_exists( 'Imagick', 'queryFormats' ) && in_array( 'WEBP', Imagick::queryFormats() ) );
        }
        return true; // Grundformate wie JPG/PNG setzen wir voraus
    }

    /**
     * Kartenbild für ein bestimmtes Stockwerk hochladen.
     * Konvertiert nach WebP (falls möglich).
     * Aktualisiert image_url, width und height in der Datenbank.
     */
    public static function upload_floor_map( $request ) {
        $floor_id = intval( $request->get_param( 'id' ) );

        if ( $floor_id === 0 ) {
            return new WP_Error( 'invalid_id', 'Ungültige ID', array( 'status' => 400 ) );
        }

        if ( ! WP_Floormap_Database::get_floor_by_id( $floor_id ) ) {
            return new WP_Error( 'not_found', 'Stockwerk nicht gefunden', array( 'status' => 404 ) );
        }

        $files = $request->get_file_params();

        if ( empty( $files['map'] ) || $files['map']['error'] !== UPLOAD_ERR_OK ) {
            return new WP_Error( 'no_file', 'Keine gültige Datei hochgeladen', array( 'status' => 400 ) );
        }

        $file         = $files['map'];
        $allowed_exts = array( 'png', 'jpg', 'jpeg', 'svg', 'webp' );

        $ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
        if ( ! in_array( $ext, $allowed_exts, true ) ) {
            return new WP_Error( 'invalid_ext', 'Nur JPG, PNG, WebP und SVG erlaubt', array( 'status' => 400 ) );
        }

        $dirs = wp_floormap_upload_dir();
        $floor_dir = $dirs['maps'] . '/floor_' . $floor_id;

        // Altes Verzeichnis löschen falls vorhanden (Bereinigung)
        if ( file_exists( $floor_dir ) ) {
            WP_Floormap_Database::recursive_rmdir( $floor_dir );
        }
        wp_mkdir_p( $floor_dir );
        
        // Legacy-Altdatei (vor Struktur-Update) bereinigen, falls in der DB noch ein alter Pfad stand
        $existing = WP_Floormap_Database::get_floor_by_id( $floor_id );
        if ( $existing && ! empty( $existing['image_url'] ) ) {
            $prev_path = wp_floormap_upload_dir()['maps'] . '/' . basename( parse_url( $existing['image_url'], PHP_URL_PATH ) );
            if ( file_exists( $prev_path ) && strpos( $prev_path, $floor_dir ) === false ) {
                @unlink( $prev_path );
            }
        }

        $source_path = $file['tmp_name'];
        
        // Bildgröße ermitteln
        $width = 0;
        $height = 0;
        
        if ( $ext === 'svg' ) {
            // SVG Größe auslesen & Optimieren
            $svg_content = file_get_contents( $source_path );
            
            // Maße ermitteln - Robusterer Regex für viewBox (unterstützt Dezimalzahlen)
            if ( preg_match( '/viewBox=["\'](-?[\d.]+\s+-?[\d.]+\s+([\d.]+)\s+([\d.]+))["\']/', $svg_content, $matches ) ) {
                $width = floatval( $matches[2] );
                $height = floatval( $matches[3] );
            } elseif ( preg_match( '/width=["\']([\d.]+)(?:px|mm|cm|in|pt|pc)?["\']/', $svg_content, $matches_w ) && preg_match( '/height=["\']([\d.]+)(?:px|mm|cm|in|pt|pc)?["\']/', $svg_content, $matches_h ) ) {
                $width = floatval( $matches_w[1] );
                $height = floatval( $matches_h[1] );
            }

            // Falls wir Breite/Höhe haben, aber keine viewBox, fügen wir eine hinzu, 
            // um eine konsistente Skalierung im Browser zu garantieren.
            if ( $width > 0 && $height > 0 && ! preg_match( '/viewBox=/i', $svg_content ) ) {
                $svg_content = preg_replace( '/<svg/i', '<svg viewBox="0 0 ' . $width . ' ' . $height . '"', $svg_content, 1 );
            }

            // SVG optimieren und speichern (direkt als SVG lassen)
            $svg_content = self::optimize_svg( $svg_content );
            file_put_contents( $source_path, $svg_content );
        } else {
            $image_info = getimagesize( $source_path );
            if ( $image_info ) {
                $width = $image_info[0];
                $height = $image_info[1];
            }
        }

        // 1. Gesamtbild speichern (bevorzugt WebP für Rasterbilder, SVG als SVG lassen)
        $use_webp = self::supports_format( 'image/webp' );
        
        // Ziel-Endung/MIME sauber bestimmen
        if ( $ext === 'svg' ) {
            $main_ext  = 'svg';
            $main_mime = 'image/svg+xml';
        } elseif ( $use_webp ) {
            $main_ext  = 'webp';
            $main_mime = 'image/webp';
        } else {
            if ( $ext === 'webp' ) {
                // Auf PNG zurückfallen, wenn WebP nicht unterstützt wird aber WebP als Quelle
                $main_ext  = 'png';
                $main_mime = 'image/png';
            } else {
                $main_ext  = $ext; // png/jpg/jpeg
                // Sicheres MIME bestimmen
                $main_mime = in_array( $ext, array('png','jpg','jpeg'), true ) ? ( $ext === 'png' ? 'image/png' : 'image/jpeg' ) : $file['type'];
            }
        }
        
        $main_image_path = $floor_dir . '/map.' . $main_ext;
        $editor = wp_get_image_editor( $source_path );

        if ( ! is_wp_error( $editor ) ) {
            // Gesamtansicht speichern (evtl. verkleinert für Performance - nur bei Rastergrafiken)
            if ( $ext !== 'svg' ) {
                $max_dim = 2000; // Etwas höherer Wert als beim Hybrid-Laden, da wir jetzt nur ein Bild haben
                if ( $width > $max_dim || $height > $max_dim ) {
                    $editor->resize( $max_dim, $max_dim, false );
                }
                $editor->save( $main_image_path, $main_mime );
            } else {
                // SVG wurde bereits optimiert im temp-file
                move_uploaded_file( $source_path, $main_image_path );
            }
        } else {
            // Fallback falls wp_get_image_editor fehlschlägt (z.B. SVG oder WebP-Support fehlt)
            if ( $ext === 'svg' ) {
                move_uploaded_file( $source_path, $main_image_path );
            } elseif ( $ext === 'webp' ) {
                // Einfach rüberschieben
                move_uploaded_file( $source_path, $main_image_path );
            } else {
                 return new WP_Error( 'editor_error', 'Bild konnte nicht verarbeitet werden.', array( 'status' => 500 ) );
            }
        }

        $filename = basename( $main_image_path );
        // Cache-Busting anfügen (Query-String), damit Browser nicht altes Bild cachen
        $url_noq = $dirs['maps_url'] . '/floor_' . $floor_id . '/' . $filename;
        $url     = $url_noq . '?v=' . time();

        // Datenbank aktualisieren
        WP_Floormap_Database::update_floor( $floor_id, array( 
            'image_url' => $url,
            'width'     => $width,
            'height'    => $height
        ) );

        return rest_ensure_response( array(
            'success'   => true,
            'filename'  => $filename,
            'url'       => $url,
            'width'     => $width,
            'height'    => $height
        ) );
    }

    public static function upload_map( $request ) {
        $files = $request->get_file_params();

        if ( empty( $files['map'] ) || $files['map']['error'] !== UPLOAD_ERR_OK ) {
            return new WP_Error( 'no_file', 'Keine gültige Datei hochgeladen', array( 'status' => 400 ) );
        }

        $file         = $files['map'];
        $allowed_exts = array( 'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp' );
        $ext          = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
        if ( ! in_array( $ext, $allowed_exts, true ) ) {
            return new WP_Error( 'invalid_type', 'Nur Bild-Dateien erlaubt', array( 'status' => 400 ) );
        }

        $dirs     = wp_floormap_upload_dir();
        $ext      = pathinfo( $file['name'], PATHINFO_EXTENSION );
        $basename = preg_replace( '/[^a-zA-Z0-9_\-]/', '_', pathinfo( $file['name'], PATHINFO_FILENAME ) );
        $filename = $basename . '.' . $ext;
        $target   = $dirs['maps'] . '/' . $filename;

        $counter = 1;
        while ( file_exists( $target ) ) {
            $filename = $basename . '_' . $counter . '.' . $ext;
            $target   = $dirs['maps'] . '/' . $filename;
            $counter++;
        }

        if ( ! move_uploaded_file( $file['tmp_name'], $target ) ) {
            return new WP_Error( 'upload_failed', 'Datei konnte nicht gespeichert werden', array( 'status' => 500 ) );
        }

        return rest_ensure_response( array(
            'success'  => true,
            'filename' => $filename,
            'url'      => $dirs['maps_url'] . '/' . $filename,
        ) );
    }

    // ========== EXPORT / IMPORT ==========

    public static function export_data( $request ) {
        $data = WP_Floormap_Database::export_all();
        return rest_ensure_response( $data );
    }

    /**
     * Import Preview: JSON analysieren und Übersicht zurückgeben, NICHTS speichern.
     */
    public static function import_preview( $request ) {
        $data = $request->get_json_params();

        if ( empty( $data ) ) {
            return new WP_Error( 'missing_data', 'Keine Daten zum Importieren', array( 'status' => 400 ) );
        }

        $preview = array(
            'has_config'       => ! empty( $data['config'] ),
            'has_global_colors' => ! empty( $data['config']['globalColors'] ),
            'floors'           => array(),
        );

        if ( ! empty( $data['floors'] ) ) {
            foreach ( $data['floors'] as $floor ) {
                $element_count = isset( $floor['elements'] ) ? count( $floor['elements'] ) : 0;
                $existing      = WP_Floormap_Database::get_floor_by_id( $floor['id'] );
                $preview['floors'][] = array(
                    'id'            => $floor['id'],
                    'label'         => $floor['label'] ?? '',
                    'name'          => $floor['name'] ?? '',
                    'element_count' => $element_count,
                    'exists_in_db'  => ! empty( $existing ),
                    'db_floors'     => array_map( function( $f ) {
                        return array( 'id' => $f['id'], 'label' => $f['label'], 'name' => $f['name'] );
                    }, WP_Floormap_Database::get_floors() ),
                );
            }
        }

        // Bestehende Stockwerke für Zuordnung mitliefern (einmalig)
        $preview['existing_floors'] = array_map( function( $f ) {
            return array( 'id' => $f['id'], 'label' => $f['label'], 'name' => $f['name'] );
        }, WP_Floormap_Database::get_floors() );

        return rest_ensure_response( $preview );
    }

    /**
     * Selektiver Import: Nur ausgewählte Abschnitte importieren.
     *
     * Erwartet im Body:
     * {
     *   "import_data": { ... },          // die originale JSON
     *   "import_config": true/false,     // globalColors + sonstige Config
     *   "floors": [                      // pro Stockwerk aus der JSON
     *     {
     *       "json_floor_id": 1,          // ID in der JSON
     *       "import_floor": true/false,  // Stockwerkdaten importieren
     *       "import_elements": true/false,
     *       "target_floor_id": 1         // Ziel-Stockwerk-ID (kann abweichen)
     *     }
     *   ]
     * }
     */
    public static function import_data( $request ) {
        $body = $request->get_json_params();

        if ( empty( $body ) ) {
            return new WP_Error( 'missing_data', 'Keine Daten zum Importieren', array( 'status' => 400 ) );
        }

        // Rückwärtskompatibilität: altes Format (direkt die Export-JSON) weiterhin unterstützen
        if ( isset( $body['floors'] ) && ! isset( $body['import_data'] ) ) {
            $result = WP_Floormap_Database::import_all( $body );
            if ( is_wp_error( $result ) ) return $result;
            return rest_ensure_response( array( 'success' => true, 'message' => 'Import erfolgreich' ) );
        }

        $import_data   = $body['import_data'] ?? array();
        $import_config = ! empty( $body['import_config'] );
        $floor_opts    = $body['floors'] ?? array();

        if ( empty( $import_data ) ) {
            return new WP_Error( 'missing_data', 'import_data fehlt', array( 'status' => 400 ) );
        }

        // Config importieren
        if ( $import_config && ! empty( $import_data['config'] ) ) {
            foreach ( $import_data['config'] as $key => $value ) {
                WP_Floormap_Database::set_config( $key, $value );
            }
        }

        // Stockwerke und Elemente selektiv importieren
        $json_floors_by_id = array();
        foreach ( ( $import_data['floors'] ?? array() ) as $floor ) {
            $json_floors_by_id[ $floor['id'] ] = $floor;
        }

        $imported_floors   = 0;
        $imported_elements = 0;

        foreach ( $floor_opts as $opt ) {
            $json_floor_id   = $opt['json_floor_id'] ?? null;
            $import_floor    = ! empty( $opt['import_floor'] );
            $import_elements = ! empty( $opt['import_elements'] );
            $target_floor_id = isset( $opt['target_floor_id'] ) ? intval( $opt['target_floor_id'] ) : $json_floor_id;

            if ( $json_floor_id === null || ( ! $import_floor && ! $import_elements ) ) {
                continue;
            }

            $floor = $json_floors_by_id[ $json_floor_id ] ?? null;
            if ( ! $floor ) continue;

            // Stockwerkdaten importieren
            if ( $import_floor ) {
                $existing = WP_Floormap_Database::get_floor_by_id( $target_floor_id );
                if ( $existing ) {
                    WP_Floormap_Database::update_floor( $target_floor_id, array(
                        'label'      => $floor['label'] ?? $existing['label'],
                        'name'       => $floor['name'] ?? $existing['name'],
                        'image_url'  => $floor['image_url'] ?? $existing['image_url'],
                        'width'      => $floor['width'] ?? $existing['width'],
                        'height'     => $floor['height'] ?? $existing['height'],
                        'sort_order' => $floor['sort_order'] ?? $existing['sort_order'],
                    ) );
                } else {
                    // Neues Stockwerk anlegen (ID wird durch DB vergeben)
                    $new_id = WP_Floormap_Database::add_floor(
                        $floor['label'] ?? '',
                        $floor['name'] ?? '',
                        $floor['image_url'] ?? '',
                        $floor['width'] ?? 0,
                        $floor['height'] ?? 0,
                        $floor['sort_order'] ?? 0
                    );
                    if ( $new_id ) {
                        $target_floor_id = (int) $new_id;
                    }
                }
                $imported_floors++;
            }

            // Elemente importieren
            if ( $import_elements && ! empty( $floor['elements'] ) ) {
                // Ziel-Stockwerk muss existieren
                if ( ! WP_Floormap_Database::get_floor_by_id( $target_floor_id ) ) {
                    continue;
                }
                foreach ( $floor['elements'] as $el ) {
                    $coords = is_array( $el['coords'] ) ? wp_json_encode( $el['coords'] ) : $el['coords'];
                    $existing_el = WP_Floormap_Database::get_element_by_id( $el['id'] );
                    if ( $existing_el ) {
                        WP_Floormap_Database::update_element( $el['id'], array(
                            'floor_id'        => $target_floor_id,
                            'name'            => $el['name'] ?? 'Unbenannt',
                            'type'            => $el['type'] ?? 'polygon',
                            'color'           => $el['color'] ?? null,
                            'color_id'        => $el['color_id'] ?? null,
                            'description'     => $el['description'] ?? '',
                            'coords'          => $coords,
                            'icon'            => $el['icon'] ?? null,
                            'icon_size'       => $el['icon_size'] ?? 32,
                            'interactive'     => $el['interactive'] ?? 1,
                            'hide_label'      => $el['hide_label'] ?? 0,
                            'display_mode'    => $el['display_mode'] ?? 'name',
                            'label_direction' => $el['label_direction'] ?? 'auto',
                        ) );
                    } else {
                        WP_Floormap_Database::add_element(
                            $el['id'], $target_floor_id, $el['name'] ?? 'Unbenannt',
                            $el['type'] ?? 'polygon', $el['color'] ?? null,
                            $el['color_id'] ?? null, $el['description'] ?? '',
                            $coords, $el['icon'] ?? null,
                            $el['interactive'] ?? 1, $el['hide_label'] ?? 0,
                            $el['display_mode'] ?? 'name', $el['icon_size'] ?? 32,
                            $el['label_direction'] ?? 'auto'
                        );
                    }
                    $imported_elements++;
                }
            }
        }

        return rest_ensure_response( array(
            'success'           => true,
            'imported_floors'   => $imported_floors,
            'imported_elements' => $imported_elements,
        ) );
    }
}
