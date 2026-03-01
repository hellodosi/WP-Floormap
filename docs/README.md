# WP Floormap Dokumentation

Dieses Verzeichnis enthält die Quelldateien für die Plugin-Dokumentation, die via GitHub Pages unter [https://floormap.dosimo.de/](https://floormap.dosimo.de/) veröffentlicht wird.

## Struktur
- `index.md`: Startseite der Dokumentation.
- `installation.md`: Anleitung zur Installation des Plugins.
- `usage.md`: Details zur Verwendung des Shortcodes und der Einstellungen.
- `technical-details.md`: Informationen zur Bildoptimierung und Datenbankstruktur.

## Lokale Entwicklung
Um die Dokumentation lokal mit Jekyll zu testen:
1. `cd docs`
2. `bundle install`
3. `bundle exec jekyll serve`

---
### Wichtige Informationen zum Theme
- Das Theme wird per `jekyll-remote-theme` geladen. Es müssen **keine** Theme-Dateien im Repository vorhanden sein. Wir nutzen nun das Theme `just-the-docs/just-the-docs`.
- Das Logo der Seite kann unter `docs/assets/images/logo.png` abgelegt werden. Der Pfad ist in der `_config.yml` bereits vorkonfiguriert.
- Ein Google Translate Widget wurde über die Includes `head_custom.html` und `nav_footer.html` im `docs/_includes/` Ordner sauber in das Theme integriert.
- Bei Fehlern wie "exit code 6" während des `bundle`-Vorgangs im CI-Build: Stellen Sie sicher, dass keine Versionskonflikte im `Gemfile` vorliegen (z.B. keine explizite `jekyll` Version, wenn `github-pages` genutzt wird).
- Stellen Sie sicher, dass in den GitHub Repository-Einstellungen unter **Settings > Pages** die Build-Quelle auf **GitHub Actions** steht.
- Der Build wird automatisch durch `.github/workflows/static.yml` gesteuert. Da der Build direkt im `docs/`-Ordner ausgeführt wird (`working-directory: docs`), sieht Jekyll nur die Dateien in diesem Verzeichnis. Dateien im übergeordneten Plugin-Verzeichnis (wie `admin/`, `wp-floormap.php`) müssen daher **nicht** explizit in der `_config.yml` ausgeschlossen werden.
- Die Navigation wird bei diesem Theme direkt in den Markdown-Dateien über `nav_order` in der Front Matter gesteuert.
- Falls das Design fehlt, prüfen Sie im Build-Log (Actions-Tab), ob `jekyll-remote-theme` erfolgreich geladen wurde.

---
*Hinweis: Wenn das Theme online nicht korrekt angezeigt wird, stellen Sie sicher, dass alle Assets über HTTPS geladen werden und das `jekyll-remote-theme` Plugin im GitHub-Workflow korrekt ausgeführt wird (siehe `.github/workflows/static.yml`).*
