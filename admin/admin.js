/**
 * WP Floormap – Admin JavaScript
 * Wird auf allen Admin-Seiten des Plugins geladen.
 * Die eigentliche Logik befindet sich inline in admin-page.php und editor-page.php.
 * Diese Datei stellt gemeinsame Hilfsfunktionen bereit.
 */

// Sicherstellen, dass WPFloormap verfügbar ist
if (typeof WPFloormap === 'undefined') {
    window.WPFloormap = { apiBase: '', nonce: '', appConfig: {}, pluginUrl: '' };
}
