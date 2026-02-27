<?php
/**
 * Datenbankklasse für WP Floormap
 * Verwendet WordPress $wpdb statt SQLite.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WP_Floormap_Database {

    // Tabellennamen (ohne Präfix)
    const TABLE_FLOORS   = 'floormap_floors';
    const TABLE_ELEMENTS = 'floormap_elements';
    const TABLE_CONFIG   = 'floormap_config';

    /**
     * Tabellen beim Plugin-Aktivieren anlegen
     */
    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $floors = $wpdb->prefix . self::TABLE_FLOORS;
        $elements = $wpdb->prefix . self::TABLE_ELEMENTS;
        $config = $wpdb->prefix . self::TABLE_CONFIG;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sql_floors = "CREATE TABLE {$floors} (
            id BIGINT NOT NULL AUTO_INCREMENT,
            label VARCHAR(50) NOT NULL,
            name VARCHAR(255) NOT NULL,
            image_url TEXT,
            width INT DEFAULT 0,
            height INT DEFAULT 0,
            sort_order INT DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        $sql_elements = "CREATE TABLE {$elements} (
            id VARCHAR(64) NOT NULL,
            floor_id BIGINT NOT NULL,
            name VARCHAR(255) NOT NULL,
            type VARCHAR(20) NOT NULL DEFAULT 'polygon',
            color VARCHAR(20),
            color_id VARCHAR(64),
            description TEXT,
            coords LONGTEXT NOT NULL,
            icon TEXT,
            icon_size INT DEFAULT 32,
            interactive TINYINT DEFAULT 1,
            hide_label TINYINT DEFAULT 0,
            display_mode VARCHAR(20) DEFAULT 'name',
            label_direction VARCHAR(20) DEFAULT 'auto',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY floor_id (floor_id)
        ) {$charset_collate};";

        $sql_config = "CREATE TABLE {$config} (
            config_key VARCHAR(100) NOT NULL,
            config_value LONGTEXT NOT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (config_key)
        ) {$charset_collate};";

        dbDelta( $sql_floors );
        dbDelta( $sql_elements );
        dbDelta( $sql_config );

        // Sicherstellen, dass die id-Spalte AUTO_INCREMENT hat (dbDelta setzt das nicht immer nachträglich)
        // Ignoriere Fehler, falls bereits korrekt gesetzt.
        $maybe_alter = @ $wpdb->query( "ALTER TABLE {$floors} MODIFY id BIGINT NOT NULL AUTO_INCREMENT" );
    }

    // ========== CONFIG ==========

    public static function get_config( $key, $default = null ) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_CONFIG;
        $value = $wpdb->get_var( $wpdb->prepare(
            "SELECT config_value FROM {$table} WHERE config_key = %s",
            $key
        ) );
        return $value !== null ? $value : $default;
    }

    public static function set_config( $key, $value ) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_CONFIG;
        $wpdb->replace( $table, array(
            'config_key'   => $key,
            'config_value' => $value,
        ), array( '%s', '%s' ) );
    }

    public static function get_all_config() {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_CONFIG;
        $rows = $wpdb->get_results( "SELECT config_key, config_value FROM {$table}", ARRAY_A );
        $config = array();
        foreach ( $rows as $row ) {
            $config[ $row['config_key'] ] = $row['config_value'];
        }
        return $config;
    }

    // ========== FLOORS ==========

    public static function get_floors() {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_FLOORS;
        return $wpdb->get_results(
            "SELECT * FROM {$table} ORDER BY sort_order DESC, id DESC",
            ARRAY_A
        );
    }

    public static function get_floor_by_id( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_FLOORS;
        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d",
            $id
        ), ARRAY_A );
    }

    public static function add_floor( $label, $name, $image_url, $width = 0, $height = 0, $sort_order = 0 ) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_FLOORS;
        $insert = function() use ( $wpdb, $table, $label, $name, $image_url, $width, $height, $sort_order ) {
            return $wpdb->insert( $table, array(
                'label'      => $label,
                'name'       => $name,
                'image_url'  => $image_url,
                'width'      => $width,
                'height'     => $height,
                'sort_order' => $sort_order,
            ), array( '%s', '%s', '%s', '%d', '%d', '%d' ) );
        };

        $success = $insert();

        if ( ! $success ) {
            // Reparatur versuchen, falls AUTO_INCREMENT fehlt oder es einen Datensatz mit id=0 gibt
            self::repair_floors_table();
            $success = $insert();
        }

        return $success ? $wpdb->insert_id : false;
    }

    public static function update_floor( $id, $data ) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_FLOORS;
        return $wpdb->update( $table, $data, array( 'id' => $id ) );
    }

    public static function delete_floor( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_FLOORS;

        // Bilder löschen
        $dirs = wp_floormap_upload_dir();
        $floor_dir = $dirs['maps'] . '/floor_' . $id;
        if ( file_exists( $floor_dir ) ) {
            self::recursive_rmdir( $floor_dir );
        }

        // Falls es noch alte Dateien im Hauptordner gibt (aus der Zeit vor dem Tiling-Update)
        $floor = self::get_floor_by_id( $id );
        if ( $floor && ! empty( $floor['image_url'] ) ) {
            $filename = basename( $floor['image_url'] );
            $path = $dirs['maps'] . '/' . $filename;
            if ( file_exists( $path ) ) {
                @unlink( $path );
            }
        }

        // Elemente des Stockwerks ebenfalls löschen
        $elements_table = $wpdb->prefix . self::TABLE_ELEMENTS;
        $wpdb->delete( $elements_table, array( 'floor_id' => $id ), array( '%d' ) );

        return $wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) );
    }

    /**
     * Repariert typische Altlasten der Tabelle floormap_floors:
     * - Stellt sicher, dass id AUTO_INCREMENT ist
     * - Migriert ggf. vorhandenen Datensatz mit id=0 auf eine neue, positive ID,
     *   inkl. Aktualisierung aller zugehörigen Elemente (floor_id)
     */
    public static function repair_floors_table() {
        global $wpdb;
        $floors   = $wpdb->prefix . self::TABLE_FLOORS;
        $elements = $wpdb->prefix . self::TABLE_ELEMENTS;

        // Prüfen, ob die id-Spalte AUTO_INCREMENT hat
        $column = $wpdb->get_row( $wpdb->prepare( "SHOW COLUMNS FROM {$floors} WHERE Field = %s", 'id' ), ARRAY_A );
        if ( $column && ( strpos( strtolower( $column['Extra'] ?? '' ), 'auto_increment' ) === false ) ) {
            @ $wpdb->query( "ALTER TABLE {$floors} MODIFY id BIGINT NOT NULL AUTO_INCREMENT" );
        }

        // Prüfen, ob es einen Datensatz mit id=0 gibt
        $has_zero = $wpdb->get_var( "SELECT COUNT(*) FROM {$floors} WHERE id = 0" );
        if ( intval( $has_zero ) > 0 ) {
            $next_id = intval( $wpdb->get_var( "SELECT COALESCE(MAX(id),0) + 1 FROM {$floors}" ) );
            if ( $next_id < 1 ) { $next_id = 1; }

            // Elemente umhängen
            @ $wpdb->query( $wpdb->prepare( "UPDATE {$elements} SET floor_id = %d WHERE floor_id = 0", $next_id ) );
            // Stockwerk-ID 0 auf neue ID aktualisieren
            @ $wpdb->update( $floors, array( 'id' => $next_id ), array( 'id' => 0 ), array( '%d' ), array( '%d' ) );
        }
    }

    // ========== ELEMENTS ==========

    public static function get_elements_by_floor( $floor_id ) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_ELEMENTS;
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE floor_id = %d ORDER BY created_at ASC",
            $floor_id
        ), ARRAY_A );
    }

    public static function get_element_by_id( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_ELEMENTS;
        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %s",
            $id
        ), ARRAY_A );
    }

    public static function add_element( $id, $floor_id, $name, $type, $color, $color_id = null, $description = '', $coords = '[]', $icon = null, $interactive = 1, $hide_label = 0, $display_mode = 'name', $icon_size = 32, $label_direction = 'auto' ) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_ELEMENTS;
        $coords_json = is_array( $coords ) ? wp_json_encode( $coords ) : $coords;
        return $wpdb->insert( $table, array(
            'id'              => $id,
            'floor_id'        => $floor_id,
            'name'            => $name,
            'type'            => $type,
            'color'           => $color,
            'color_id'        => $color_id,
            'description'     => $description,
            'coords'          => $coords_json,
            'icon'            => $icon,
            'icon_size'       => $icon_size,
            'interactive'     => $interactive,
            'hide_label'      => $hide_label,
            'display_mode'    => $display_mode,
            'label_direction' => $label_direction,
        ), array( '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s' ) );
    }

    public static function update_element( $id, $data ) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_ELEMENTS;
        if ( isset( $data['coords'] ) && is_array( $data['coords'] ) ) {
            $data['coords'] = wp_json_encode( $data['coords'] );
        }
        return $wpdb->update( $table, $data, array( 'id' => $id ) );
    }

    public static function delete_element( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_ELEMENTS;
        return $wpdb->delete( $table, array( 'id' => $id ), array( '%s' ) );
    }

    /**
     * Alle Daten des Plugins löschen (Reset)
     */
    public static function delete_all_data() {
        global $wpdb;
        $floors   = $wpdb->prefix . self::TABLE_FLOORS;
        $elements = $wpdb->prefix . self::TABLE_ELEMENTS;
        $config   = $wpdb->prefix . self::TABLE_CONFIG;

        // 1. Tabellen leeren
        $wpdb->query( "TRUNCATE TABLE {$floors}" );
        $wpdb->query( "TRUNCATE TABLE {$elements}" );
        $wpdb->query( "TRUNCATE TABLE {$config}" );

        // 2. Dateien löschen
        $dirs = wp_floormap_upload_dir();
        self::recursive_rmdir( $dirs['path'] );
        
        // Verzeichnisse wieder anlegen
        wp_floormap_ensure_upload_dirs();
    }

    public static function recursive_rmdir( $dir ) {
        if ( is_dir( $dir ) ) {
            $objects = scandir( $dir );
            foreach ( $objects as $object ) {
                if ( $object != "." && $object != ".." ) {
                    if ( is_dir( $dir . DIRECTORY_SEPARATOR . $object ) && ! is_link( $dir . DIRECTORY_SEPARATOR . $object ) ) {
                        self::recursive_rmdir( $dir . DIRECTORY_SEPARATOR . $object );
                    } else {
                        @unlink( $dir . DIRECTORY_SEPARATOR . $object );
                    }
                }
            }
            @rmdir( $dir );
        }
    }

    public static function search_elements( $term ) {
        global $wpdb;
        $et = $wpdb->prefix . self::TABLE_ELEMENTS;
        $ft = $wpdb->prefix . self::TABLE_FLOORS;
        $like = '%' . $wpdb->esc_like( $term ) . '%';
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT e.*, f.label AS floor_label, f.name AS floor_name
             FROM {$et} e
             JOIN {$ft} f ON e.floor_id = f.id
             WHERE e.name LIKE %s OR e.description LIKE %s
             ORDER BY e.name ASC",
            $like, $like
        ), ARRAY_A );
    }

    // ========== EXPORT / IMPORT ==========

    /**
     * Erzeugt einen eindeutigen Stockwerksnamen, indem bei Kollisionen ein Suffix " (n)" angehängt wird.
     */
    private static function generate_unique_floor_name( $base_name ) {
        $name = $base_name ?: '';
        $try  = 1;
        while ( self::floor_name_exists( $name ) ) {
            $try++;
            $name = $base_name . ' (' . $try . ')';
        }
        return $name;
    }

    private static function floor_name_exists( $name ) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_FLOORS;
        $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE name = %s", $name ) );
        return intval( $count ) > 0;
    }

    /**
     * Generiert eine neue, systemische Element-ID (ohne Bezug zur JSON) und stellt Eindeutigkeit sicher.
     */
    private static function generate_fresh_element_id() {
        $id = 'el-' . wp_generate_uuid4();
        while ( self::get_element_by_id( $id ) ) {
            $id = 'el-' . wp_generate_uuid4();
        }
        return $id;
    }

    /**
     * Alle Daten als Array exportieren (für JSON-Export)
     */
    public static function export_all() {
        $floors   = self::get_floors();
        $all_config = self::get_all_config();
        $floors_with_elements = array();

        foreach ( $floors as $floor ) {
            $elements = self::get_elements_by_floor( $floor['id'] );
            // coords von JSON-String zu Array
            foreach ( $elements as &$el ) {
                $el['coords'] = json_decode( $el['coords'], true );
            }
            unset( $el );
            $floor['elements'] = $elements;
            $floors_with_elements[] = $floor;
        }

        return array(
            'version'  => WP_FLOORMAP_VERSION,
            'exported' => current_time( 'c' ),
            'config'   => $all_config,
            'floors'   => $floors_with_elements,
        );
    }

    /**
     * Daten aus einem Export-Array importieren
     */
    public static function import_all( $data ) {
        global $wpdb;

        if ( empty( $data['floors'] ) ) {
            return new WP_Error( 'invalid_data', 'Keine Stockwerk-Daten gefunden.' );
        }

        // Config importieren
        if ( ! empty( $data['config'] ) ) {
            foreach ( $data['config'] as $key => $value ) {
                self::set_config( $key, $value );
            }
        }

        // Stockwerke und Elemente importieren (IDs aus JSON werden ignoriert)
        foreach ( $data['floors'] as $floor ) {
            $elements = isset( $floor['elements'] ) ? $floor['elements'] : array();
            unset( $floor['elements'] );

            // Eindeutigen Namen bestimmen (Suffix " (n)" bei Kollision)
            $label = $floor['label'];
            $name  = self::generate_unique_floor_name( $floor['name'] );

            // Neues Stockwerk anlegen – DB vergibt die ID
            $new_id = self::add_floor(
                $label,
                $name,
                $floor['image_url'],
                $floor['width'],
                $floor['height'],
                $floor['sort_order']
            );
            if ( ! $new_id ) {
                // Wenn Einfügen fehlschlägt, nächsten durchlauf fortsetzen
                continue;
            }
            $dest_floor_id = (int) $new_id;

            // Elemente importieren – Element-IDs werden immer neu vergeben (systemisch)
            foreach ( $elements as $el ) {
                $coords = is_array( $el['coords'] ) ? wp_json_encode( $el['coords'] ) : $el['coords'];
                $el_id  = self::generate_fresh_element_id();
                self::add_element(
                    $el_id, $dest_floor_id, $el['name'], $el['type'],
                    $el['color'] ?? null, $el['color_id'] ?? null,
                    $el['description'] ?? '', $coords,
                    $el['icon'] ?? null, $el['interactive'] ?? 1,
                    $el['hide_label'] ?? 0, $el['display_mode'] ?? 'name',
                    $el['icon_size'] ?? 32, $el['label_direction'] ?? 'auto'
                );
            }
        }

        return true;
    }
}
