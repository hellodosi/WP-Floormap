# WP Floormap

Interaktive Gebäudekarte als WordPress-Plugin.

## Features
- Stockwerke verwalten (Kürzel, Name, Kartenbild)
- Automatische Konvertierung nach WebP für optimierte Performance
- Automatische Ermittlung der Bildmaße beim Upload
- Interaktive Kartenelemente (Polygone, Icons) mit Tooltips und Info-Sheets
- Leaflet-basierte Kartendarstellung

## Installation
1. Plugin-Ordner in `wp-content/plugins` kopieren.
2. Plugin im WordPress-Backend aktivieren.
3. Die benötigten Datenbanktabellen werden automatisch erstellt.
4. Das Upload-Verzeichnis `wp-content/uploads/wp-floormap` wird automatisch angelegt.

## Verwendung
### Shortcode
Verwenden Sie den Shortcode `[wp_floormap]`, um die Karte auf einer Seite anzuzeigen.
Optionale Attribute:
- `floor`: ID des Stockwerks, das initial angezeigt werden soll.
- `theme`: `light` oder `dark` (Standard: folgt Systemeinstellung). Der Theme-Modus kann auch über den GET-Parameter `?theme=light` oder `?theme=dark` in der URL überschrieben werden.
- `find`: ID eines Elements, das initial fokussiert werden soll.

### Konfiguration
In den Plugin-Einstellungen können verschiedene Optionen angepasst werden:
- **Label-Zoom-Schwellwert**: Bestimmt, ab welchem Zoom-Level Raumbeschriftungen eingeblendet werden.
- **Leaflet Attribution**: Ermöglicht das Ein- oder Ausblenden des Standard-Leaflet-Hinweises. (Nur global in den Einstellungen möglich, nicht pro Shortcode).
- **Plugin Attribution**: Ermöglicht das Ein- oder Ausblenden des Hinweises "WP-Floormap-Plugin" in der Kartenecke. (Nur global in den Einstellungen möglich). Bei Deaktivierung freuen wir uns über eine kleine Spende zur Unterstützung des Projekts.
- **Daten bei Deinstallation behalten**: Verhindert das Löschen von Kartendaten beim Entfernen des Plugins.

### Elementor
Ein Elementor-Widget steht zur Verfügung, wenn Elementor installiert ist.

## Technische Details
### SVG-Optimierung & Bildverarbeitung
Das Plugin ist auf maximale Performance ausgelegt, insbesondere beim Umgang mit großen Kartenbildern.

1. **SVG-Support & Optimierung (Empfohlen)**:
   - SVGs werden beim Upload automatisch bereinigt (Entfernen von XML-Metadaten, Kommentaren und unnötigen Namespaces wie Inkscape/Sodipodi).
   - Das Plugin stellt sicher, dass die ursprünglichen Dimensionen (viewBox) erhalten bleiben oder bei Fehlen automatisch generiert werden, damit interaktive Elemente exakt positioniert bleiben.
   - Dies reduziert die Rechenlast auf dem Client erheblich, da der Browser weniger komplexes XML parsen muss.
   - SVGs bleiben Vektorgrafiken und bieten somit die beste Qualität bei geringster Dateigröße.

2. **Rastergrafiken (JPG/PNG/WebP)**:
   - **WebP-Konvertierung**: Rasterbilder werden bevorzugt in das WebP-Format umgewandelt (falls vom Server unterstützt).
   - **Performance**: Große Rasterbilder werden beim Upload automatisch auf eine web-optimierte Größe (max. 2000px) skaliert.

3. **Größenermittlung**: Die Maße (Breite/Höhe) werden automatisch aus der Datei gelesen (auch aus SVG ViewBox), können aber im Admin-Bereich bei Bedarf manuell korrigiert werden.
4. **Cache-Busting**: Bilder erhalten einen Versions-Query (`?v=timestamp`), um Browser-Caching-Probleme nach Updates zu vermeiden.

### Plugin-Updates
Das Plugin unterstützt automatische Updates über GitHub

### Datenbank
Das Plugin nutzt drei Tabellen:
- `wp_floormap_floors`: Speichert Stockwerksdaten.
- `wp_floormap_elements`: Speichert die interaktiven Flächen und Icons.
- `wp_floormap_config`: Speichert allgemeine Einstellungen.
