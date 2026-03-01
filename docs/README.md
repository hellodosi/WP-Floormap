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
- Das Theme wird per `jekyll-remote-theme` geladen. Es müssen **keine** Theme-Dateien im Repository vorhanden sein.
- Bei Fehlern wie "exit code 6" während des `bundle`-Vorgangs im CI-Build: Stellen Sie sicher, dass keine Versionskonflikte im `Gemfile` vorliegen (z.B. keine explizite `jekyll` Version, wenn `github-pages` genutzt wird).
- Stellen Sie sicher, dass in den GitHub Repository-Einstellungen unter **Settings > Pages** die Build-Quelle auf **GitHub Actions** steht.
- Der Build wird automatisch durch `.github/workflows/static.yml` gesteuert.

---
*Hinweis: Wenn das Theme online nicht korrekt angezeigt wird, stellen Sie sicher, dass alle Assets über HTTPS geladen werden und das `jekyll-remote-theme` Plugin im GitHub-Workflow korrekt ausgeführt wird (siehe `.github/workflows/static.yml`).*
