<?php
/**
 * Admin-Seite: Karten-Editor
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div id="fm-editor-root" class="wp-floormap-wrap">
    <!-- Die Karte wird hier gerendert -->
    <div id="fm-editor-map" style="width:100%; height:100%;"></div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof WPFloormapInit === 'function') {
        WPFloormapInit(
            'fm-editor-map',
            WPFloormap.appConfig,
            WPFloormap.apiBase,
            'auto',
            '',
            true  // IS_DEV_MODE = true im Admin-Editor
        );
    }
});
</script>
