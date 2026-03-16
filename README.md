# WP Floormap

Interaktive Gebäudekarte als WordPress-Plugin.

Die vollständige Dokumentation finden Sie im Verzeichnis [docs/](docs/) oder auf unserer [Plugin-Homepage](https://floormap.dosimo.de/). Eine WordPress-kompatible `readme.txt` für das offizielle Plugin-Verzeichnis ist ebenfalls im Hauptverzeichnis vorhanden.

## Features
- Stockwerke verwalten (Kürzel, Name, Kartenbild)
- Automatische Konvertierung nach WebP für optimierte Performance
- Automatische Ermittlung der Bildmaße beim Upload
- Interaktive Kartenelemente (Polygone, Icons) mit Tooltips und Info-Sheets
- Leaflet-basierte Kartendarstellung
- Elementor-Integration
- SVG-Optimierung beim Upload

## Schnellanleitung
1. Plugin-Ordner in `wp-content/plugins` kopieren.
2. Plugin im WordPress-Backend aktivieren.
3. Den Shortcode `[wp_floormap]` auf einer Seite einfügen.

## Entwicklung & Deployment
Dieses Projekt nutzt GitHub Actions, um das WordPress-Plugin automatisch zu verpacken.
- Bei jedem Push auf `main` oder bei einem neuen Release wird automatisch eine `wp-floormap.zip` Datei erstellt.
- Die ZIP-Datei kann in den [GitHub Actions](https://github.com/hellodosi/WP-Floormap/actions) unter dem jeweiligen Workflow-Lauf als Artefakt heruntergeladen werden.
- Bei offiziellen Releases wird die ZIP-Datei direkt an das [Release](https://github.com/hellodosi/WP-Floormap/releases) angehängt.
- Der Pack-Prozess schließt automatisch Entwicklungs-Dateien (`.git`, `.github`, `.idea`), Dokumentation (`docs/`), Test-Assets (`my-assets/`) und Aufgabenlisten (`todo.md`) aus, um ein schlankes Plugin-Paket zu gewährleisten.

Detaillierte Anleitungen zur Konfiguration und Verwendung finden Sie in der [Dokumentation](docs/index.md) (bzw. auf der [Plugin-Homepage](https://floormap.dosimo.de/)).
