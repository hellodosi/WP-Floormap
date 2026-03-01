---
layout: default
title: Verwendung
nav_order: 3
---

# Verwendung

## Shortcode
Verwenden Sie den Shortcode `[wp_floormap]`, um die interaktive Karte auf einer beliebigen Seite anzuzeigen.

### Optionale Attribute:
- `floor`: ID des Stockwerks, das beim Laden der Seite initial angezeigt werden soll.
- `theme`: Legt den Farbmodus fest. Werte: `light` oder `dark` (Standard: folgt den Systemeinstellungen des Nutzers).
- `find`: ID eines Kartenelements, das beim Laden fokussiert und hervorgehoben werden soll.

**Tipp:** Der Theme-Modus kann auch über einen URL-Parameter überschrieben werden: `?theme=light` oder `?theme=dark`.

## Konfiguration
In den Plugin-Einstellungen können Sie das Plugin weiter anpassen:
- **Label-Zoom-Schwellwert**: Ab welcher Zoomstufe sollen Raumbeschriftungen erscheinen?
- **Leaflet Attribution**: Deaktivieren Sie optional den Leaflet-Hinweis (global).
- **Plugin Attribution**: Ein-/Ausblenden des Plugin-Credits. (Wir freuen uns bei Deaktivierung über eine Spende!)
- **Daten bei Deinstallation behalten**: Schützt Ihre Daten vor dem Löschen beim Entfernen des Plugins.

## Elementor Widget
Wenn Sie Elementor verwenden, finden Sie in der Widget-Auswahl ein eigenes `WP Floormap` Widget.
