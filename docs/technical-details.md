---
layout: page
title: Technische Details
toc: true
---

# Technische Details

WP Floormap ist für optimale Performance bei maximaler Bildqualität optimiert.

## SVG-Optimierung (Empfohlen)
SVGs bieten die beste Qualität bei geringster Größe. WP Floormap bereinigt SVGs beim Upload:
- Entfernen unnötiger Metadaten (Inkscape, Sodipodi).
- Sicherstellen der korrekten `viewBox` für exakte Elementpositionierung.
- Geringe Rechenlast auf dem Client durch sauberes XML.

## Bildverarbeitung (Rastergrafiken)
Bei der Verwendung von JPG, PNG oder WebP-Bildern führt das Plugin folgende Optimierungen durch:
- **WebP-Konvertierung**: Wandelt Bilder automatisch nach WebP um.
- **Skalierung**: Große Rasterbilder werden auf eine web-optimierte Größe (max. 2000px) herunterskaliert.
- **Cache-Busting**: Bilder erhalten eine Versions-Query (`?v=timestamp`), um Caching-Probleme bei Updates zu vermeiden.

## Datenbankstruktur
Das Plugin nutzt folgende Tabellen:
- `wp_floormap_floors`: Speichert Metadaten der Stockwerke.
- `wp_floormap_elements`: Speichert Polygone, Icons und deren Konfiguration.
- `wp_floormap_config`: Speichert die globalen Plugin-Einstellungen.
