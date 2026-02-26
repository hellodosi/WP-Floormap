<?php
/**
 * Elementor Widget für WP Floormap
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
    return;
}

class WP_Floormap_Elementor_Widget extends \Elementor\Widget_Base {

    public static function register( $widgets_manager ) {
        $widgets_manager->register( new self() );
    }

    public function get_name() {
        return 'wp_floormap';
    }

    public function get_title() {
        return 'WP Floormap';
    }

    public function get_icon() {
        return 'eicon-map-pin';
    }

    public function get_categories() {
        return array( 'general' );
    }

    public function get_keywords() {
        return array( 'map', 'floor', 'karte', 'gebäude', 'floormap' );
    }

    protected function register_controls() {
        $this->start_controls_section( 'content_section', array(
            'label' => 'Einstellungen',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ) );

        $this->add_control( 'height', array(
            'label'       => 'Höhe',
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => '600px',
            'placeholder' => 'z.B. 600px oder auto',
            'description' => '"auto" nutzt die verfügbare Höhe des umgebenden Containers.',
        ) );

        $this->add_control( 'theme', array(
            'label'   => 'Farbschema',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'auto',
            'options' => array(
                'auto'  => 'Automatisch',
                'light' => 'Hell',
                'dark'  => 'Dunkel',
            ),
        ) );

        $floors = \WP_Floormap_Database::get_floors();
        $floor_options = array(
            '' => 'Standard (aus Konfiguration)',
        );
        foreach ( $floors as $floor ) {
            $floor_options[ $floor['id'] ] = $floor['label'] . ' - ' . $floor['name'];
        }

        $this->add_control( 'floor', array(
            'label'   => 'Stockwerk',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'options' => $floor_options,
        ) );

        $this->add_control( 'find', array(
            'label'       => 'Initialer Suchbegriff',
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => '',
            'placeholder' => 'z.B. Haupthalle',
        ) );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $atts = array(
            'height' => ! empty( $settings['height'] ) ? $settings['height'] : '600px',
            'theme'  => ! empty( $settings['theme'] ) ? $settings['theme'] : 'auto',
            'floor'  => ! empty( $settings['floor'] ) ? $settings['floor'] : '',
            'find'   => ! empty( $settings['find'] ) ? $settings['find'] : '',
        );

        $atts_str = '';
        foreach ( $atts as $key => $val ) {
            if ( $val !== '' ) {
                $atts_str .= ' ' . $key . '="' . esc_attr( $val ) . '"';
            }
        }

        echo do_shortcode( '[wp_floormap' . $atts_str . ']' );
    }

    protected function content_template() {
        ?>
        <#
        var height = settings.height || '600px';
        var style = 'background:#f0f0f0; text-align:center; color:#666; display:flex; flex-direction:column; align-items:center; justify-content:center; border: 1px dashed #ccc; box-sizing: border-box;';
        
        if (height.toLowerCase() === 'auto') {
            style += ' min-height: 400px; height: 100%;';
        } else {
            style += ' height: ' + height + ';';
        }
        #>
        <div style="{{ style }}">
            <span class="eicon-map-pin" style="font-size:48px; display:block; margin-bottom:12px;"></span>
            <div style="font-weight:900; font-size:16px;">WP Floormap</div>
            <div style="font-size:12px; opacity:0.7; margin-top:4px;">Vorschau im Editor nicht verfügbar (Performance)</div>
            <div style="font-size:11px; margin-top:8px; background:rgba(0,0,0,0.05); padding:4px 10px; border-radius:12px;">Höhe: {{ height }}</div>
        </div>
        <?php
    }
}
