<?php
/**
 * Admin-Seite: Konfiguration (Stockwerke, Farben, Import/Export, Uploads)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$upload_dirs = wp_floormap_upload_dir();
$maps_url    = $upload_dirs['maps_url'];
?>
<div class="wrap" id="wp-floormap-admin">
    <h1>WP Floormap – Konfiguration</h1>

    <div id="floormap-notice" style="display:none;" class="notice is-dismissible"></div>

    <style>
        .fm-drag-handle {
            cursor: grab;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            transition: color 0.2s;
        }
        .fm-drag-handle:hover {
            color: #475569;
        }
        .fm-drag-handle:active {
            cursor: grabbing;
        }
        .fm-drag-over-top {
            border-top: 2px solid var(--wp-admin-theme-color, #2271b1) !important;
        }
        .fm-drag-over-bottom {
            border-bottom: 2px solid var(--wp-admin-theme-color, #2271b1) !important;
        }
    </style>
    <nav class="nav-tab-wrapper">
        <a href="#" class="nav-tab nav-tab-active" onclick="fmShowTab('floors', this); return false;">Stockwerke</a>
        <a href="#" class="nav-tab" onclick="fmShowTab('colors', this); return false;">Globale Farben</a>
        <a href="#" class="nav-tab" onclick="fmShowTab('icons', this); return false;">Icons</a>
        <a href="#" class="nav-tab" onclick="fmShowTab('settings', this); return false;">Einstellungen</a>
        <a href="#" class="nav-tab" onclick="fmShowTab('io', this); return false;">Import / Export</a>
        <a href="#" class="nav-tab" onclick="fmShowTab('help', this); return false;">Hilfe</a>
    </nav>

    <!-- ===== TAB: STOCKWERKE ===== -->
    <div id="tab-floors" class="fm-tab">
        <h2>Stockwerke</h2>
        <p>Hier können Stockwerke angelegt, bearbeitet und gelöscht werden. Die Reihenfolge kann über die Pfeil-Buttons angepasst werden.</p>

        <table class="wp-list-table widefat fixed striped" id="fm-floors-table">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th style="width: 50px; text-align: center;">Std.</th>
                    <th style="width: 80px;">Kürzel</th>
                    <th>Name</th>
                    <th>Kartenbild-URL</th>
                    <th style="width: 80px;">Breite</th>
                    <th style="width: 80px;">Höhe</th>
                    <th style="width: 100px;">Sortierung</th>
                    <th style="width: 120px;">Aktionen</th>
                    <th style="width: 40px;"></th>
                </tr>
            </thead>
            <tbody id="fm-floors-body">
                <tr><td colspan="8">Lade...</td></tr>
            </tbody>
        </table>

        <button class="button button-primary" style="margin-top:16px;" onclick="fmOpenFloorModal()">+ Neues Stockwerk</button>
    </div>

    <!-- ===== TAB: GLOBALE FARBEN ===== -->
    <div id="tab-colors" class="fm-tab" style="display:none;">
        <h2>Globale Farben</h2>
        <p>Diese Farben stehen im Karten-Editor als Palette zur Verfügung und können Kartenelementen zugewiesen werden.</p>

        <div id="fm-colors-list" style="max-width:600px;"></div>
        <button class="button" style="margin-top:8px;" onclick="fmAddColor()">+ Farbe hinzufügen</button>
        <br><br>
        <button class="button button-primary" onclick="fmSaveColors()">Farben speichern</button>
    </div>


    <!-- ===== TAB: ICONS ===== -->
    <div id="tab-icons" class="fm-tab" style="display:none;">
        <h2>Icons verwalten</h2>
        <p>Icons werden in <code><?php echo esc_html( $upload_dirs['icons'] ); ?></code> gespeichert.</p>

        <div style="margin-bottom:16px;">
            <label class="button" for="fm-icon-upload-input" style="cursor:pointer;">Icon hochladen</label>
            <input type="file" id="fm-icon-upload-input" accept="image/*" style="display:none;" onchange="fmUploadIcon(this)">
        </div>

        <div id="fm-icons-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(100px,1fr)); gap:12px; max-width:900px;">
            <div>Lade Icons...</div>
        </div>
    </div>

    <!-- ===== TAB: EINSTELLUNGEN ===== -->
    <div id="tab-settings" class="fm-tab" style="display:none;">
        <h2>Allgemeine Einstellungen</h2>
        <table class="form-table">
            <tr>
                <th><label for="fm-zoom-threshold">Label-Zoom-Schwellwert</label></th>
                <td>
                    <input type="number" id="fm-zoom-threshold" class="regular-text" step="0.1" value="">
                    <p class="description">Ab welchem Zoom-Level Beschriftungen sichtbar werden (z.B. 0 oder 0.5).</p>
                </td>
            </tr>
            <tr>
                <th><label for="fm-show-attribution">Leaflet Attribution</label></th>
                <td>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" id="fm-show-attribution" style="width:20px; height:20px;">
                        <span>Leaflet Attribution im Frontend anzeigen</span>
                    </label>
                    <div id="fm-attribution-notice" style="display:none; margin-top:12px; padding:12px; border:1px solid #ffcc00; background:#fffdf5; border-radius:4px; max-width:600px;">
                        <p style="margin:0; font-size:13px; line-height:1.5;">
                            Es ist völlig in Ordnung, die Attribution auszublenden. Da der Entwickler von Leaflet jedoch aus der Ukraine stammt, wäre es im Gegenzug fair, für die Ukraine zu spenden. 
                            Weitere Informationen und Spendenlinks finden Sie auf <a href="https://leafletjs.com/#donate" target="_blank" rel="noopener">leafletjs.com</a>.
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <th><label for="fm-keep-on-uninstall">Daten bei Deinstallation behalten</label></th>
                <td>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" id="fm-keep-on-uninstall" style="width:20px; height:20px;">
                        <span>Bei "Plugin löschen" (Deinstallation) die Datenbanktabellen und Uploads NICHT löschen</span>
                    </label>
                    <p class="description">Wenn deaktiviert, werden beim Deinstallieren des Plugins alle Plugin-Daten entfernt.</p>
                </td>
            </tr>
        </table>
        <button class="button button-primary" onclick="fmSaveSettings()">Einstellungen speichern</button>

        <div style="margin-top:48px; padding:24px; border:1px solid #f8d7da; border-radius:8px; background:#fffafa;">
            <h3 style="margin-top:0; color:#dc3232;">Datenverwaltung</h3>
            <p>Hier können alle Daten des Plugins (Stockwerke, Elemente, Konfiguration und hochgeladene Bilder) unwiderruflich gelöscht werden.</p>
            <button class="button" style="background:#dc3232; color:#fff; border-color:#dc3232;" onclick="fmDeleteAllData()">Alle Daten löschen</button>
        </div>
    </div>


    <!-- ===== TAB: IMPORT / EXPORT ===== -->
    <div id="tab-io" class="fm-tab" style="display:none;">
        <h2>Export</h2>
        <p>Alle Kartendaten (Stockwerke, Elemente, Konfiguration) als JSON-Datei herunterladen.</p>
        <button class="button button-primary" onclick="fmExport()">Daten exportieren (JSON)</button>

        <hr>

        <h2>Import</h2>
        <p>JSON-Datei auswählen. Nach dem Einlesen kann ausgewählt werden, welche Abschnitte importiert werden sollen. Die Datei wird <strong>nicht dauerhaft gespeichert</strong>.</p>

        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <label class="button" for="fm-import-file" style="cursor:pointer;">JSON-Datei auswählen</label>
            <input type="file" id="fm-import-file" accept=".json,application/json" style="display:none;" onchange="fmImportPreview(this)">
            <span id="fm-import-filename" style="opacity:0.6; font-size:13px;"></span>
        </div>

        <!-- Auswahl-UI (wird nach Einlesen eingeblendet) -->
        <div id="fm-import-selection" style="display:none; margin-top:20px;">
            <h3 style="margin-bottom:12px;">Was soll importiert werden?</h3>

            <!-- Globale Farben / Config -->
            <div id="fm-import-config-row" style="display:none; margin-bottom:16px; padding:12px; border:1px solid #ddd; border-radius:4px; background:#fafafa;">
                <label style="display:flex; align-items:center; gap:10px; font-weight:600; cursor:pointer;">
                    <input type="checkbox" id="fm-import-config-cb" checked>
                    Globale Farben &amp; Einstellungen importieren
                </label>
                <p style="margin:6px 0 0 26px; font-size:12px; opacity:0.7;">Überschreibt globalColors, defaultFloorId und labelZoomThreshold.</p>
            </div>

            <!-- Stockwerke -->
            <div id="fm-import-floors-list" style="display:flex; flex-direction:column; gap:10px;"></div>

            <div style="margin-top:20px; display:flex; gap:10px; align-items:center;">
                <button class="button button-primary" onclick="fmImportExecute()">Ausgewähltes importieren</button>
                <button class="button" onclick="fmImportReset()">Abbrechen</button>
            </div>
        </div>

        <div id="fm-import-result" style="margin-top:12px;"></div>
    </div>

    <!-- ===== TAB: HILFE ===== -->
    <div id="tab-help" class="fm-tab" style="display:none;">
        <h2>Hilfe & Dokumentation</h2>
        
        <div style="max-width:800px; background:#fff; padding:20px; border:1px solid #ccd0d4; border-radius:4px;">
            <h3>Shortcode Verwendung</h3>
            <p>Verwenden Sie den folgenden Shortcode, um die Karte auf einer beliebigen Seite oder in einem Beitrag einzubinden:</p>
            <code>[wp_floormap]</code>

            <h4 style="margin-top:20px;">Parameter</h4>
            <table class="widefat fixed striped" style="margin-top:10px;">
                <thead>
                    <tr>
                        <th style="width:120px;">Parameter</th>
                        <th>Beschreibung</th>
                        <th style="width:150px;">Beispiel</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>height</code></td>
                        <td>Die Höhe des Kartencontainers. Unterstützt Werte wie <code>px</code>, <code>%</code>, <code>vh</code> sowie <code>auto</code> (füllt den verfügbaren Platz des umgebenden Containers). Standard: <code>600px</code>.</td>
                        <td><code>height="500px"</code><br><code>height="auto"</code></td>
                    </tr>
                    <tr>
                        <td><code>theme</code></td>
                        <td>Farbschema der Karte. Mögliche Werte: <code>auto</code>, <code>light</code>, <code>dark</code>. Standard: <code>auto</code>.</td>
                        <td><code>theme="dark"</code></td>
                    </tr>
                    <tr>
                        <td><code>floor</code></td>
                        <td>Die ID des Stockwerks, das beim Laden der Karte zuerst angezeigt werden soll. Wird der Parameter weggelassen, wird das in der Konfiguration festgelegte Standard-Stockwerk verwendet.</td>
                        <td><code>floor="12"</code></td>
                    </tr>
                    <tr>
                        <td><code>find</code></td>
                        <td>Ein Suchbegriff, der beim Laden der Karte automatisch gesucht wird. Ist genau ein Treffer vorhanden, wird direkt dorthin gezoomt.</td>
                        <td><code>find="Haupthalle"</code></td>
                    </tr>
                </tbody>
            </table>

            <h4 style="margin-top:20px;">Beispiele</h4>
            <ul style="list-style:disc; margin-left:20px;">
                <li><code>[wp_floormap height="800px"]</code> - Karte mit einer Höhe von 800 Pixeln.</li>
                <li><code>[wp_floormap theme="dark" floor="1"]</code> - Karte im Dark-Mode, die mit Stockwerk ID 1 startet.</li>
                <li><code>[wp_floormap find="WC"]</code> - Karte öffnen und sofort nach "WC" suchen.</li>
            </ul>

            <h4 style="margin-top:20px;">Elementor</h4>
            <p>Wenn Sie Elementor nutzen, finden Sie das Widget <strong>"WP Floormap"</strong> in der Widget-Liste. Dort können Sie die gleichen Einstellungen bequem über die Benutzeroberfläche vornehmen.</p>
        </div>
    </div>
</div>

<!-- ===== MODAL: STOCKWERK BEARBEITEN ===== -->
<div id="fm-floor-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:100000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:8px; padding:32px; width:480px; max-width:95vw; max-height:90vh; overflow-y:auto; position:relative;">
        <!-- Lade-Overlay -->
        <div id="fm-floor-modal-processing" class="fm-processing-overlay" style="display:none;">
            <div class="fm-spinner"></div>
            <div style="font-weight:600; color:#2271b1;">Bild wird verarbeitet...</div>
            <div style="font-size:12px; opacity:0.7; margin-top:4px;">Dies kann bei großen Bildern einen Moment dauern.</div>
        </div>

        <h2 id="fm-floor-modal-title" style="margin-top:0;">Stockwerk</h2>
        <input type="hidden" id="fm-floor-original-id">

        <table class="form-table" style="margin:0;">
            <tr id="fm-floor-id-row" style="display:none;">
                <th><label for="fm-floor-id">ID</label></th>
                <td><input type="number" id="fm-floor-id" class="regular-text" readonly></td>
            </tr>
            <tr>
                <th><label for="fm-floor-label">Kürzel</label></th>
                <td><input type="text" id="fm-floor-label" class="regular-text" placeholder="z.B. EG"></td>
            </tr>
            <tr>
                <th><label for="fm-floor-name">Name</label></th>
                <td><input type="text" id="fm-floor-name" class="regular-text" placeholder="z.B. Erdgeschoss"></td>
            </tr>
            <tr>
                <th><label for="fm-floor-map-upload">Kartenbild</label></th>
                <td>
                    <div id="fm-floor-map-preview" style="display:none; margin-bottom:8px;">
                        <img id="fm-floor-map-preview-img" src="" style="max-width:200px; max-height:120px; border:1px solid #ddd; border-radius:4px; display:block; margin-bottom:4px;">
                        <span id="fm-floor-map-preview-url" style="font-size:11px; opacity:0.6; word-break:break-all;"></span>
                    </div>
                    <input type="hidden" id="fm-floor-image">
                    <label class="button" for="fm-floor-map-upload" style="cursor:pointer;">Bild hochladen / ersetzen</label>
                    <input type="file" id="fm-floor-map-upload" accept=".jpg,.jpeg,.png,.webp,.svg,image/jpeg,image/png,image/webp,image/svg+xml" style="display:none;" onchange="fmFloorMapSelected(this)">
                    <span id="fm-floor-map-upload-name" style="margin-left:8px; font-size:12px; opacity:0.7;"></span>
                    <p class="description">Unterstützte Formate: JPG, PNG, WebP, SVG. SVGs werden automatisch für bessere Performance optimiert.</p>
                </td>
            </tr>
            <tr>
                <th><label for="fm-floor-is-default">Standard-Stockwerk</label></th>
                <td>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" id="fm-floor-is-default" style="width:20px; height:20px;">
                        <span>Als Standard beim Karten-Aufruf anzeigen</span>
                    </label>
                </td>
            </tr>
            <tr>
                <th><label for="fm-floor-width">Breite (px)</label></th>
                <td><input type="number" id="fm-floor-width" class="regular-text" value="0"> <p class="description">Wird beim Upload automatisch ermittelt, kann aber manuell korrigiert werden.</p></td>
            </tr>
            <tr>
                <th><label for="fm-floor-height">Höhe (px)</label></th>
                <td><input type="number" id="fm-floor-height" class="regular-text" value="0"> <p class="description">Wird beim Upload automatisch ermittelt, kann aber manuell korrigiert werden.</p></td>
            </tr>
        </table>

        <div id="fm-floor-modal-feedback" style="display:none; margin:12px 0; padding:10px; border-radius:4px;"></div>

        <div style="display:flex; gap:8px; margin-top:20px; justify-content:flex-end;">
            <button class="button" onclick="fmCloseFloorModal()">Abbrechen</button>
            <button id="fm-floor-delete-btn" class="button" style="background:#dc3232; color:#fff; border-color:#dc3232; display:none;" onclick="fmDeleteFloor()">Löschen</button>
            <button class="button button-primary" onclick="fmSaveFloor()">Speichern</button>
        </div>
    </div>
</div>

<script>
// ===== INITIALISIERUNG =====
document.addEventListener('DOMContentLoaded', function() {
    fmLoadFloors();
    fmLoadIcons();
    fmLoadSettings();
});

function fmShowTab(name, el) {
    document.querySelectorAll('.fm-tab').forEach(t => t.style.display = 'none');
    document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('nav-tab-active'));
    document.getElementById('tab-' + name).style.display = 'block';
    el.classList.add('nav-tab-active');
    if (name === 'icons') fmLoadIcons();
    return false;
}

function fmNotice(msg, type) {
    var el = document.getElementById('floormap-notice');
    el.className = 'notice notice-' + (type || 'success') + ' is-dismissible';
    el.innerHTML = '<p>' + msg + '</p>';
    el.style.display = 'block';
    setTimeout(() => el.style.display = 'none', 4000);
}

async function fmApi(method, endpoint, body) {
    var opts = {
        method: method,
        headers: { 'X-WP-Nonce': WPFloormap.nonce }
    };
    if (body !== undefined) {
        opts.headers['Content-Type'] = 'application/json';
        opts.body = JSON.stringify(body);
    }
    try {
        var res = await fetch(WPFloormap.apiBase + endpoint, opts);
        var text = await res.text();
        var data = {};
        try {
            data = text ? JSON.parse(text) : {};
        } catch (e) {
            // Nicht-JSON Antwort (z.B. 204 No Content)
            data = {};
        }
        if (!res.ok) {
            return Object.assign({ success: false, status: res.status }, data, {
                message: (data && data.message) ? data.message : ('HTTP ' + res.status)
            });
        }
        // Erfolg
        return Object.assign({ success: true }, data);
    } catch (err) {
        return { success: false, message: 'Netzwerkfehler: ' + err.message };
    }
}

// ===== STOCKWERKE =====
async function fmLoadFloors() {
    var data = await fmApi('GET', '/config');
    WPFloormap.appConfig = data;
    var tbody = document.getElementById('fm-floors-body');
    if (!data.floors || data.floors.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10">Noch keine Stockwerke vorhanden.</td></tr>';
        return;
    }
    tbody.innerHTML = data.floors.map((f, index) => {
        var isDefault = parseInt(data.defaultFloorId) === parseInt(f.id);
        return `
        <tr ondragover="fmFloorDragOver(event)" ondrop="fmFloorDrop(event, ${index})" ondragend="fmFloorDragEnd(event)" ondragleave="fmFloorDragLeave(event)">
            <td>${f.id}</td>
            <td style="text-align: center; color: #f59e0b; font-size: 18px;">${isDefault ? '★' : ''}</td>
            <td>${f.label}</td>
            <td>${f.name}</td>
            <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="${f.imageUrl || ''}">${f.imageUrl || '–'}</td>
            <td>${f.width}</td>
            <td>${f.height}</td>
            <td>
                <button class="button button-small" onclick="fmMoveFloor(${index}, -1)" ${index === 0 ? 'disabled' : ''}>&uarr;</button>
                <button class="button button-small" onclick="fmMoveFloor(${index}, 1)" ${index === data.floors.length - 1 ? 'disabled' : ''}>&darr;</button>
            </td>
            <td>
                <button class="button button-small" onclick='fmOpenFloorModal(${JSON.stringify(f)})'>Bearbeiten</button>
            </td>
            <td>
                <div class="fm-drag-handle" draggable="true" ondragstart="fmFloorDragStart(event, ${index})">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="5" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="19" r="1"/></svg>
                </div>
            </td>
        </tr>
    `; }).join('');
}

function fmFloorDragStart(e, index) {
    try { 
        e.dataTransfer.effectAllowed = 'move'; 
        e.dataTransfer.setData('text/plain', String(index));
        // Die ganze Zeile als Drag-Image setzen, falls der Browser es unterstützt
        var row = e.target.closest('tr');
        if (row && e.dataTransfer.setDragImage) {
            e.dataTransfer.setDragImage(row, 20, 20);
        }
    } catch(_) {}
    var row = e.target.closest('tr');
    if (row) row.style.opacity = '0.4';
}

function fmFloorDragOver(e) {
    e.preventDefault();
    try { e.dataTransfer.dropEffect = 'move'; } catch(_) {}
    var row = e.currentTarget;
    if (row) {
        var rect = row.getBoundingClientRect();
        var next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
        row.classList.remove('fm-drag-over-top', 'fm-drag-over-bottom');
        row.classList.add(next ? 'fm-drag-over-bottom' : 'fm-drag-over-top');
    }
}

function fmFloorDragLeave(e) {
    var row = e.currentTarget;
    if (row) {
        row.classList.remove('fm-drag-over-top', 'fm-drag-over-bottom');
    }
}

async function fmFloorDrop(e, targetIndex) {
    e.preventDefault();
    var row = e.currentTarget;
    if (row) row.classList.remove('fm-drag-over-top', 'fm-drag-over-bottom');
    
    var fromStr = '';
    try { fromStr = e.dataTransfer.getData('text/plain'); } catch(_) { fromStr = ''; }
    var from = parseInt(fromStr, 10);
    if (isNaN(from)) return;

    // Bestimmen, ob über oder unter der Zielzeile abgelegt wurde
    var rect = row.getBoundingClientRect();
    var next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
    var dropIndex = targetIndex;
    if (from < targetIndex && !next) dropIndex = targetIndex - 1;
    if (from > targetIndex && next) dropIndex = targetIndex + 1;
    
    if (from === dropIndex) return;

    var floors = WPFloormap.appConfig.floors;
    var item = floors.splice(from, 1)[0];
    floors.splice(dropIndex, 0, item);

    // IDs in neuer Reihenfolge an API senden
    var ids = floors.map(f => f.id);
    var res = await fmApi('POST', '/floors/reorder', { ids: ids });
    if (res.success) {
        fmLoadFloors();
    } else {
        alert('Fehler beim Sortieren: ' + (res.message || 'Unbekannter Fehler'));
    }
}
function fmFloorDragEnd(e) {
    document.querySelectorAll('#fm-floors-body tr').forEach(tr => {
        tr.style.opacity = '';
        tr.classList.remove('fm-drag-over-top', 'fm-drag-over-bottom');
    });
}

async function fmMoveFloor(index, direction) {
    var floors = WPFloormap.appConfig.floors;
    if (!floors) return;
    
    var newIndex = index + direction;
    if (newIndex < 0 || newIndex >= floors.length) return;
    
    // Tauschen
    var temp = floors[index];
    floors[index] = floors[newIndex];
    floors[newIndex] = temp;
    
    // IDs in neuer Reihenfolge an API senden
    var ids = floors.map(f => f.id);
    var res = await fmApi('POST', '/floors/reorder', { ids: ids });
    
    if (res.success) {
        fmLoadFloors();
    } else {
        alert('Fehler beim Sortieren: ' + (res.message || 'Unbekannter Fehler'));
    }
}

function fmOpenFloorModal(floor) {
    var modal = document.getElementById('fm-floor-modal');
    document.getElementById('fm-floor-modal-feedback').style.display = 'none';
    document.getElementById('fm-floor-map-upload').value = '';
    document.getElementById('fm-floor-map-upload-name').innerText = '';
    if (floor) {
        document.getElementById('fm-floor-modal-title').innerText = 'Stockwerk bearbeiten';
        document.getElementById('fm-floor-original-id').value = floor.id;
        document.getElementById('fm-floor-id-row').style.display = 'table-row';
        document.getElementById('fm-floor-id').value = floor.id;
        document.getElementById('fm-floor-label').value = floor.label;
        document.getElementById('fm-floor-name').value = floor.name;
        document.getElementById('fm-floor-image').value = floor.imageUrl || '';
        document.getElementById('fm-floor-is-default').checked = (parseInt(WPFloormap.appConfig.defaultFloorId) === parseInt(floor.id));
        document.getElementById('fm-floor-width').value = floor.width || 0;
        document.getElementById('fm-floor-height').value = floor.height || 0;
        document.getElementById('fm-floor-delete-btn').style.display = 'inline-block';
        // Vorschau aktuelles Bild
        if (floor.imageUrl) {
            document.getElementById('fm-floor-map-preview-img').src = floor.imageUrl;
            document.getElementById('fm-floor-map-preview-url').innerText = floor.imageUrl;
            document.getElementById('fm-floor-map-preview').style.display = 'block';
        } else {
            document.getElementById('fm-floor-map-preview').style.display = 'none';
        }
    } else {
        document.getElementById('fm-floor-modal-title').innerText = 'Neues Stockwerk';
        document.getElementById('fm-floor-original-id').value = '';
        document.getElementById('fm-floor-id-row').style.display = 'none';
        document.getElementById('fm-floor-id').value = '';
        document.getElementById('fm-floor-label').value = '';
        document.getElementById('fm-floor-name').value = '';
        document.getElementById('fm-floor-image').value = '';
        document.getElementById('fm-floor-is-default').checked = (WPFloormap.appConfig.floors && WPFloormap.appConfig.floors.length === 0);
        document.getElementById('fm-floor-width').value = 0;
        document.getElementById('fm-floor-height').value = 0;
        document.getElementById('fm-floor-delete-btn').style.display = 'none';
        document.getElementById('fm-floor-map-preview').style.display = 'none';
    }
    modal.style.display = 'flex';
}

function fmFloorMapSelected(input) {
    var name = input.files && input.files[0] ? input.files[0].name : '';
    document.getElementById('fm-floor-map-upload-name').innerText = name ? ('Ausgewählt: ' + name) : '';
}

function fmCloseFloorModal() {
    document.getElementById('fm-floor-modal').style.display = 'none';
}

async function fmSaveFloor() {
    var originalId = document.getElementById('fm-floor-original-id').value;
    var uploadInput = document.getElementById('fm-floor-map-upload');
    var processingOverlay = document.getElementById('fm-floor-modal-processing');
    
    var payload = {
        label:     document.getElementById('fm-floor-label').value,
        name:      document.getElementById('fm-floor-name').value,
        imageUrl:  document.getElementById('fm-floor-image').value,
        width:     parseInt(document.getElementById('fm-floor-width').value) || 0,
        height:    parseInt(document.getElementById('fm-floor-height').value) || 0,
    };

    if (!payload.label || !payload.name) {
        fmFloorFeedback('Kürzel und Name sind Pflichtfelder.', false);
        return;
    }

    // Wenn ein Bild hochgeladen wird, zeigen wir den Spinner
    var isUploading = uploadInput.files && uploadInput.files[0];
    if (isUploading) {
        processingOverlay.style.display = 'flex';
    }

    try {
        // 1. Stockwerk speichern (anlegen oder aktualisieren)
        var method   = originalId ? 'PUT' : 'POST';
        var endpoint = originalId ? '/floors/' + originalId : '/floors';
        var result   = await fmApi(method, endpoint, payload);

        if (!result.success) {
            fmFloorFeedback('Fehler: ' + (result.message || 'Unbekannter Fehler'), false);
            processingOverlay.style.display = 'none';
            return;
        }

        var targetId = originalId ? parseInt(originalId) : (result.id || null);

        // 2. Falls "Standard-Stockwerk" angehakt ist, Config aktualisieren
        if (document.getElementById('fm-floor-is-default').checked && targetId) {
            await fmApi('POST', '/config', { key: 'defaultFloorId', value: targetId });
        } else if (!document.getElementById('fm-floor-is-default').checked && targetId && parseInt(WPFloormap.appConfig.defaultFloorId) === targetId) {
            await fmApi('POST', '/config', { key: 'defaultFloorId', value: 0 });
        }

        // 3. Bild hochladen, falls eine Datei ausgewählt wurde
        if (isUploading) {
            if (!targetId) {
                fmFloorFeedback('Stockwerk gespeichert, aber ID für den Bild-Upload fehlt.', false);
                fmLoadFloors();
                processingOverlay.style.display = 'none';
                return;
            }
            var form = new FormData();
            form.append('map', uploadInput.files[0]);
            var uploadRes = await fetch(WPFloormap.apiBase + '/floors/' + targetId + '/map-upload', {
                method: 'POST',
                headers: { 'X-WP-Nonce': WPFloormap.nonce },
                body: form
            });
            var uploadData = await uploadRes.json();

            if (!uploadData.success) {
                fmFloorFeedback('Stockwerk gespeichert, aber Bild-Upload fehlgeschlagen: ' + (uploadData.message || 'Fehler'), false);
                fmLoadFloors();
                processingOverlay.style.display = 'none';
                return;
            }
            // Neue Maße in die Felder übernehmen (optional, da wir eh schließen)
            document.getElementById('fm-floor-width').value = uploadData.width || 0;
            document.getElementById('fm-floor-height').value = uploadData.height || 0;
        }

        // Erfolg
        fmCloseFloorModal();
        fmNotice('Stockwerk gespeichert.');
        fmLoadFloors();
        
    } catch (err) {
        console.error(err);
        fmFloorFeedback('Ein unerwarteter Fehler ist aufgetreten.', false);
    } finally {
        processingOverlay.style.display = 'none';
    }
}

async function fmDeleteFloor() {
    var id = document.getElementById('fm-floor-original-id').value;
    if (!id) return;
    if (!confirm('Stockwerk und alle zugehörigen Elemente wirklich löschen?')) return;
    var result = await fmApi('DELETE', '/floors/' + id);
    if (result && result.success) {
        fmCloseFloorModal();
        fmNotice('Stockwerk gelöscht.');
        fmLoadFloors();
    } else {
        var msg = (result && result.message) ? result.message : 'Fehler beim Löschen.';
        fmFloorFeedback(msg, false);
    }
}

function fmFloorFeedback(msg, ok) {
    var el = document.getElementById('fm-floor-modal-feedback');
    el.style.display = 'block';
    el.style.background = ok ? '#d4edda' : '#f8d7da';
    el.style.color = ok ? '#155724' : '#721c24';
    el.innerText = msg;
}

// ===== GLOBALE FARBEN =====
var fmColors = [];

function fmLoadSettings() {
    fmApi('GET', '/config').then(data => {
        WPFloormap.appConfig = data;
        fmColors = (data.globalColors || []).map(c => Object.assign({}, c));
        fmRenderColors();
        document.getElementById('fm-zoom-threshold').value = data.labelZoomThreshold || 0;
        
        var showAttr = data.showAttribution !== undefined ? (data.showAttribution === "true" || data.showAttribution === true) : true;
        document.getElementById('fm-show-attribution').checked = showAttr;
        document.getElementById('fm-attribution-notice').style.display = showAttr ? 'none' : 'block';

        var keepOnUninstall = data.keepDataOnUninstall !== undefined ? (data.keepDataOnUninstall === "true" || data.keepDataOnUninstall === true) : true;
        var keepCb = document.getElementById('fm-keep-on-uninstall');
        if (keepCb) keepCb.checked = keepOnUninstall;

        document.getElementById('fm-show-attribution').onchange = function() {
            document.getElementById('fm-attribution-notice').style.display = this.checked ? 'none' : 'block';
        };
    });
}

function fmRenderColors() {
    var list = document.getElementById('fm-colors-list');
    if (fmColors.length === 0) {
        list.innerHTML = '<p style="opacity:0.6;">Noch keine Farben definiert.</p>';
        return;
    }
    list.innerHTML = fmColors.map((c, i) => `
        <div ondragover="fmColorDragOver(event)" ondrop="fmColorDrop(event, ${i})" ondragend="fmColorDragEnd(event)" ondragleave="fmColorDragLeave(event)"
             style="display:flex; gap:8px; align-items:center; margin-bottom:8px; padding:4px; border:1px solid transparent; border-radius:6px;">
            <div class="fm-drag-handle" draggable="true" ondragstart="fmColorDragStart(event, ${i})">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="5" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="19" r="1"/></svg>
            </div>
            <input type="color" value="${c.hex}" onchange="fmColors[${i}].hex=this.value"
                style="width:40px; height:36px; border:1px solid #ddd; padding:2px; cursor:pointer; border-radius:4px; flex-shrink:0;">
            <input type="text" value="${c.name}" placeholder="Bezeichnung" onchange="fmColors[${i}].name=this.value"
                style="flex:1; padding:6px 10px; border:1px solid #ddd; border-radius:4px;">
            <button class="button" onclick="fmColors.splice(${i},1); fmRenderColors();">✕</button>
        </div>
    `).join('');
}

function fmColorDragStart(e, index) {
    try { 
        e.dataTransfer.effectAllowed = 'move'; 
        e.dataTransfer.setData('text/plain', String(index));
        var item = e.target.closest('div');
        if (item && e.dataTransfer.setDragImage) {
            e.dataTransfer.setDragImage(item, 20, 20);
        }
    } catch(_) {}
    var item = e.target.closest('div');
    if (item) item.style.opacity = '0.5';
}

function fmColorDragOver(e) {
    e.preventDefault();
    try { e.dataTransfer.dropEffect = 'move'; } catch(_) {}
    var item = e.currentTarget;
    if (item) {
        var rect = item.getBoundingClientRect();
        var next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
        item.classList.remove('fm-drag-over-top', 'fm-drag-over-bottom');
        item.classList.add(next ? 'fm-drag-over-bottom' : 'fm-drag-over-top');
    }
}

function fmColorDragLeave(e) {
    var item = e.currentTarget;
    if (item) {
        item.classList.remove('fm-drag-over-top', 'fm-drag-over-bottom');
    }
}

function fmColorDrop(e, targetIndex) {
    e.preventDefault();
    var item = e.currentTarget;
    if (item) item.classList.remove('fm-drag-over-top', 'fm-drag-over-bottom');
    
    var fromStr = '';
    try { fromStr = e.dataTransfer.getData('text/plain'); } catch(_) { fromStr = ''; }
    var from = parseInt(fromStr, 10);
    if (isNaN(from)) return;

    var rect = item.getBoundingClientRect();
    var next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
    var dropIndex = targetIndex;
    if (from < targetIndex && !next) dropIndex = targetIndex - 1;
    if (from > targetIndex && next) dropIndex = targetIndex + 1;

    if (from === dropIndex) return;

    var colorItem = fmColors.splice(from, 1)[0];
    fmColors.splice(dropIndex, 0, colorItem);
    fmRenderColors();
}

function fmColorDragEnd(e) {
    document.querySelectorAll('#fm-colors-list > div').forEach(item => {
        item.style.opacity = '';
        item.classList.remove('fm-drag-over-top', 'fm-drag-over-bottom');
    });
}

function fmAddColor() {
    fmColors.push({ hex: '#bc0009', name: '' });
    fmRenderColors();
}

async function fmSaveColors() {
    fmColors.forEach(c => { if (!c.id) c.id = 'gc-' + Date.now() + '-' + Math.random().toString(36).slice(2,7); });
    var result = await fmApi('POST', '/config', { key: 'globalColors', value: fmColors });
    if (result.success) {
        fmNotice('Farben gespeichert.');
        fmRenderColors();
    } else {
        fmNotice('Fehler beim Speichern.', 'error');
    }
}

// ===== EINSTELLUNGEN =====
async function fmSaveSettings() {
    var threshold = parseFloat(document.getElementById('fm-zoom-threshold').value) || 0;
    var showAttr = document.getElementById('fm-show-attribution').checked;
    var keepOnUninstall = document.getElementById('fm-keep-on-uninstall').checked;
    
    var res1 = await fmApi('POST', '/config', { key: 'labelZoomThreshold', value: threshold });
    var res2 = await fmApi('POST', '/config', { key: 'showAttribution', value: showAttr });
    var res3 = await fmApi('POST', '/config', { key: 'keepDataOnUninstall', value: keepOnUninstall });
    
    if (res1.success && res2.success && res3.success) {
        fmNotice('Einstellungen gespeichert.');
    } else {
        fmNotice('Fehler beim Speichern der Einstellungen.', 'error');
    }
}

async function fmDeleteAllData() {
    if (!confirm('Sind Sie sicher? Dies löscht ALLE Stockwerke, Elemente, Konfigurationen und hochgeladene Bilder unwiderruflich!')) return;
    if (!confirm('WIRKLICH ALLES LÖSCHEN? Dieser Vorgang kann nicht rückgängig gemacht werden.')) return;
    
    var res = await fmApi('DELETE', '/all-data');
    if (res.success) {
        alert('Alle Daten wurden gelöscht. Die Seite wird nun neu geladen.');
        location.reload();
    } else {
        fmNotice('Fehler beim Löschen der Daten: ' + (res.message || 'Unbekannter Fehler'), 'error');
    }
}

// ===== ICONS =====
async function fmLoadIcons() {
    var data = await fmApi('GET', '/icons');
    var grid = document.getElementById('fm-icons-grid');
    if (!data.icons || data.icons.length === 0) {
        grid.innerHTML = '<div style="opacity:0.6;">Noch keine Icons hochgeladen.</div>';
        return;
    }
    grid.innerHTML = data.icons.map(icon => `
        <div style="border:1px solid #ddd; border-radius:6px; padding:10px; text-align:center; background:#fafafa;">
            <img src="${icon.url}" style="width:48px; height:48px; object-fit:contain; display:block; margin:0 auto 6px;">
            <div style="font-size:10px; word-break:break-all; opacity:0.7; margin-bottom:6px;">${icon.filename}</div>
            <button class="button button-small" onclick="fmCopyUrl('${icon.url}')">URL kopieren</button>
            <button class="button button-small" style="color:#dc3232;" onclick="fmDeleteIcon('${icon.filename}', this)">✕</button>
        </div>
    `).join('');
}

async function fmUploadIcon(input) {
    if (!input.files || !input.files[0]) return;
    var form = new FormData();
    form.append('icon', input.files[0]);
    var res = await fetch(WPFloormap.apiBase + '/icons', {
        method: 'POST',
        headers: { 'X-WP-Nonce': WPFloormap.nonce },
        body: form
    });
    var data = await res.json();
    if (data.success) {
        fmNotice('Icon hochgeladen: ' + data.filename);
        fmLoadIcons();
    } else {
        fmNotice('Fehler: ' + (data.message || 'Upload fehlgeschlagen'), 'error');
    }
    input.value = '';
}

async function fmDeleteIcon(filename, btn) {
    if (!confirm('Icon "' + filename + '" wirklich löschen?')) return;
    var result = await fmApi('DELETE', '/icons/' + encodeURIComponent(filename));
    if (result.success) {
        fmNotice('Icon gelöscht.');
        fmLoadIcons();
    } else {
        fmNotice('Fehler beim Löschen.', 'error');
    }
}

function fmCopyUrl(url) {
    navigator.clipboard.writeText(url).then(() => fmNotice('URL kopiert: ' + url));
}


// ===== EXPORT / IMPORT =====
async function fmExport() {
    var res  = await fetch(WPFloormap.apiBase + '/export', { headers: { 'X-WP-Nonce': WPFloormap.nonce } });
    var data = await res.json();
    var blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    var url  = URL.createObjectURL(blob);
    var a    = document.createElement('a');
    a.href   = url;
    a.download = 'wp-floormap-export-' + new Date().toISOString().slice(0,10) + '.json';
    a.click();
    URL.revokeObjectURL(url);
}

// Hält die eingelesene JSON im Speicher (wird NICHT gespeichert)
var fmImportData = null;
var fmImportPreviewData = null;

async function fmImportPreview(input) {
    var file = input.files[0];
    if (!file) return;

    document.getElementById('fm-import-filename').textContent = file.name;
    document.getElementById('fm-import-result').innerHTML = '';
    document.getElementById('fm-import-selection').style.display = 'none';

    var text;
    try { text = await file.text(); } catch(e) { fmImportShowError('Datei konnte nicht gelesen werden.'); return; }

    try { fmImportData = JSON.parse(text); } catch(e) { fmImportShowError('Ungültige JSON-Datei.'); return; }

    // Preview vom Server anfordern (analysiert die JSON, speichert nichts)
    var preview = await fmApi('POST', '/import/preview', fmImportData);
    if (!preview || preview.code) { fmImportShowError('Fehler beim Analysieren: ' + (preview.message || 'Unbekannter Fehler')); return; }

    fmImportPreviewData = preview;
    fmImportRenderSelection(preview);
}

function fmImportRenderSelection(preview) {
    // Config-Zeile
    var configRow = document.getElementById('fm-import-config-row');
    configRow.style.display = preview.has_config ? 'block' : 'none';

    // Stockwerke
    var list = document.getElementById('fm-import-floors-list');
    list.innerHTML = '';

    if (!preview.floors || preview.floors.length === 0) {
        list.innerHTML = '<p style="opacity:0.6;">Keine Stockwerke in der JSON gefunden.</p>';
    } else {
        var existingOptions = (preview.existing_floors || []).map(function(f) {
            return '<option value="' + f.id + '">' + f.label + ' – ' + f.name + ' (ID ' + f.id + ')</option>';
        }).join('');

        preview.floors.forEach(function(floor, idx) {
            var existsBadge = floor.exists_in_db
                ? '<span style="background:#d4edda; color:#155724; padding:2px 6px; border-radius:3px; font-size:11px; margin-left:6px;">bereits vorhanden</span>'
                : '<span style="background:#fff3cd; color:#856404; padding:2px 6px; border-radius:3px; font-size:11px; margin-left:6px;">neu</span>';

            var targetOptions = existingOptions;
            // Option für neues Stockwerk (aus JSON übernehmen)
            targetOptions = '<option value="__new__">Neues Stockwerk anlegen (ID ' + floor.id + ')</option>' + targetOptions;

            // Vorauswahl: wenn Stockwerk bereits existiert, dieses vorauswählen
            var selectedNew = floor.exists_in_db ? '' : 'selected';
            var selectedExisting = '';

            var div = document.createElement('div');
            div.style.cssText = 'padding:14px; border:1px solid #ddd; border-radius:4px; background:#fafafa;';
            div.innerHTML = `
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px; flex-wrap:wrap;">
                    <strong style="font-size:14px;">${floor.label} – ${floor.name}</strong>
                    ${existsBadge}
                    <span style="opacity:0.6; font-size:12px;">${floor.element_count} Element(e)</span>
                </div>
                <div style="display:flex; flex-direction:column; gap:8px; padding-left:4px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" id="fm-imp-floor-${idx}" checked>
                        <span>Stockwerkdaten importieren (Name, Kürzel, Kartenbild, Dimensionen)</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" id="fm-imp-elements-${idx}" checked>
                        <span>Kartenelemente importieren (${floor.element_count} Stück)</span>
                    </label>
                    <div style="display:flex; align-items:center; gap:8px; margin-top:4px; flex-wrap:wrap;">
                        <label for="fm-imp-target-${idx}" style="font-size:12px; white-space:nowrap;">Zuordnen zu Stockwerk:</label>
                        <select id="fm-imp-target-${idx}" style="flex:1; min-width:200px; padding:4px 8px; border:1px solid #ccc; border-radius:3px; font-size:13px;">
                            <option value="__new__" ${selectedNew}>Neues Stockwerk anlegen (ID ${floor.id})</option>
                            ${(preview.existing_floors || []).map(function(f) {
                                var sel = (floor.exists_in_db && f.id == floor.id) ? 'selected' : '';
                                return '<option value="' + f.id + '" ' + sel + '>' + f.label + ' – ' + f.name + ' (ID ' + f.id + ')</option>';
                            }).join('')}
                        </select>
                    </div>
                </div>
            `;
            div.dataset.jsonFloorId = floor.id;
            div.dataset.idx = idx;
            list.appendChild(div);
        });
    }

    document.getElementById('fm-import-selection').style.display = 'block';
}

async function fmImportExecute() {
    if (!fmImportData || !fmImportPreviewData) return;

    var importConfig = document.getElementById('fm-import-config-cb') && document.getElementById('fm-import-config-cb').checked;
    var floorDivs = document.querySelectorAll('#fm-import-floors-list > div[data-json-floor-id]');

    var floors = [];
    floorDivs.forEach(function(div) {
        var idx = div.dataset.idx;
        var jsonFloorId = parseInt(div.dataset.jsonFloorId);
        var importFloor    = document.getElementById('fm-imp-floor-' + idx) && document.getElementById('fm-imp-floor-' + idx).checked;
        var importElements = document.getElementById('fm-imp-elements-' + idx) && document.getElementById('fm-imp-elements-' + idx).checked;
        var targetVal      = document.getElementById('fm-imp-target-' + idx) ? document.getElementById('fm-imp-target-' + idx).value : '__new__';
        var targetFloorId  = targetVal === '__new__' ? jsonFloorId : parseInt(targetVal);

        if (importFloor || importElements) {
            floors.push({
                json_floor_id:   jsonFloorId,
                import_floor:    importFloor,
                import_elements: importElements,
                target_floor_id: targetFloorId
            });
        }
    });

    if (!importConfig && floors.length === 0) {
        alert('Bitte mindestens einen Abschnitt zum Importieren auswählen.');
        return;
    }

    var result_div = document.getElementById('fm-import-result');
    result_div.innerHTML = '<div style="padding:10px; background:#e2e3e5; border-radius:4px;">Importiere...</div>';

    var result = await fmApi('POST', '/import', {
        import_data:   fmImportData,
        import_config: importConfig,
        floors:        floors
    });

    if (result.success) {
        var msg = '✓ Import abgeschlossen!';
        if (result.imported_floors > 0)   msg += ' ' + result.imported_floors + ' Stockwerk(e) importiert.';
        if (result.imported_elements > 0) msg += ' ' + result.imported_elements + ' Element(e) importiert.';
        fmImportReset();
        result_div.innerHTML = '<div style="padding:10px; background:#d4edda; border-radius:4px; color:#155724;">' + msg + '</div>';
        fmLoadFloors();
        fmLoadSettings();
    } else {
        result_div.innerHTML = '<div style="padding:10px; background:#f8d7da; border-radius:4px; color:#721c24;">Fehler: ' + (result.message || 'Import fehlgeschlagen') + '</div>';
    }
}

function fmImportReset() {
    fmImportData = null;
    fmImportPreviewData = null;
    document.getElementById('fm-import-file').value = '';
    document.getElementById('fm-import-filename').textContent = '';
    document.getElementById('fm-import-selection').style.display = 'none';
    document.getElementById('fm-import-floors-list').innerHTML = '';
}

function fmImportShowError(msg) {
    document.getElementById('fm-import-result').innerHTML =
        '<div style="padding:10px; background:#f8d7da; border-radius:4px; color:#721c24;">' + msg + '</div>';
    fmImportReset();
}
</script>
