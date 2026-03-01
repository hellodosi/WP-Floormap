/**
 * WP Floormap – Frontend JavaScript
 * Initialisierung: WPFloormapInit(mapId, appConfig, apiBase, theme, initialFind, isDevMode)
 */

function WPFloormapInit(mapId, APP_CONFIG, API_BASE, THEME, INITIAL_FIND, IS_DEV_MODE) {

    var wrap = document.getElementById(mapId).closest('.wp-floormap-wrap') || document.getElementById(mapId).parentElement;

    // Theme anwenden
    if (THEME === 'dark' || (THEME !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        wrap.classList.add('fm-dark');
    }

    // ===== UI AUFBAUEN =====
    var mapEl = document.getElementById(mapId);
    mapEl.className = 'fm-map';

    // Suchleiste
    var searchHtml = '<div class="fm-search-container">'
        + '<div class="fm-ui-panel fm-search-bar">'
        + '<button class="fm-menu-btn" id="' + mapId + '-menu-btn">'
        + '<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>'
        + '</button>'
        + '<div class="fm-search-input-wrap">'
        + '<svg width="14" height="14" style="opacity:0.4;margin-right:6px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>'
        + '<input type="text" class="fm-search-input" id="' + mapId + '-search" autocomplete="off" placeholder="Suchen..." value="' + (INITIAL_FIND || '') + '">'
        + '<button class="fm-clear-btn" id="' + mapId + '-clear-btn" style="display:none;">'
        + '<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
        + '</button>'
        + '</div></div>'
        + '<div class="fm-search-results fm-ui-panel" id="' + mapId + '-results"><div style="padding:8px;" id="' + mapId + '-results-list"></div></div>'
        + '</div>';

    // Stockwerk-Switcher
    var switcherHtml = '<div class="fm-floor-switcher-container"><div class="fm-floor-switcher" id="' + mapId + '-switcher"></div></div>';

    // Info-Sheet
    var infoHtml = '<div class="fm-info-sheet" id="' + mapId + '-info">'
        + '<div class="fm-info-card">'
        + '<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;">'
        + '<h3 id="' + mapId + '-info-title" style="margin:0;font-size:20px;font-weight:900;line-height:1.2;color:var(--text-main);">–</h3>'
        + '<button id="' + mapId + '-info-close" style="padding:8px;background:var(--input-bg);border-radius:50%;border:none;color:var(--text-muted);cursor:pointer;display:flex;">'
        + '<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
        + '</button></div>'
        + '<p id="' + mapId + '-info-desc" style="margin:0;font-size:14px;line-height:1.6;opacity:0.8;font-style:italic;color:var(--text-main);">–</p>'
        + '</div></div>';

    wrap.insertAdjacentHTML('beforeend', searchHtml + switcherHtml + infoHtml);

    // Dev-Tools HTML (nur im Dev-Modus)
    if (IS_DEV_MODE) {
        var devHtml = '<div class="fm-dev-tools" id="' + mapId + '-dev-tools">'
            + '<div class="fm-ui-panel" style="padding:12px;border-radius:16px;display:flex;flex-direction:column;gap:8px;width:192px;color:var(--text-main);">'
            + '<div style="display:flex;justify-content:space-between;align-items:center;">'
            + '<span style="font-weight:900;color:var(--brand-color);font-size:10px;text-transform:uppercase;letter-spacing:0.1em;">Editor</span>'
            + '<div style="font-size:8px;font-family:monospace;opacity:0.5;text-align:right;">'
            + '<div id="' + mapId + '-coord-display">[0, 0]</div>'
            + '<div>Zoom: <span id="' + mapId + '-zoom-display">-</span></div>'
            + '</div></div>'
            + '<button id="' + mapId + '-start-btn" style="background:var(--brand-color);color:white;padding:8px 12px;border-radius:12px;font-weight:700;border:none;cursor:pointer;font-size:10px;">Start</button>'
            + '<button id="' + mapId + '-stop-btn" style="display:none;background:#737373;color:white;padding:8px 12px;border-radius:12px;font-weight:700;border:none;cursor:pointer;font-size:10px;">Stopp</button>'
            + '<div id="' + mapId + '-status-text" style="font-size:9px;opacity:0.7;text-align:center;">Inaktiv</div>'
            + '</div></div>';

        // Editor-Modal
        var editorModalHtml = '<div id="' + mapId + '-editor-modal" class="fm-modal-overlay" style="display:none;">'
            + '<div class="fm-modal-content fm-editor-modal">'
            + '<div class="fm-modal-header">'
            + '<h2 id="' + mapId + '-modal-title" class="fm-modal-title">Element bearbeiten</h2>'
            + '<button onclick="fm_closeModal(\'' + mapId + '\')" class="fm-modal-close-btn" title="Schließen">'
            + '<svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>'
            + '</button>'
            + '</div>'
            + '<div class="fm-modal-body">'
            + '<div style="margin-bottom:12px;"><label style="display:block;font-size:10px;font-weight:800;text-transform:uppercase;opacity:0.5;margin-bottom:4px;">Name</label>'
            + '<input type="text" id="' + mapId + '-el-name" class="fm-input-field" placeholder="z.B. Haupthalle"></div>'
            + '<div style="margin-bottom:12px;"><label style="display:block;font-size:10px;font-weight:800;text-transform:uppercase;opacity:0.5;margin-bottom:4px;">Beschreibung</label>'
            + '<textarea id="' + mapId + '-el-desc" class="fm-input-field" style="height:80px;resize:vertical;"></textarea></div>'
            + '<div style="margin-bottom:12px;"><label style="display:flex;align-items:center;gap:8px;cursor:pointer;">'
            + '<input type="checkbox" id="' + mapId + '-el-interactive" checked style="width:18px;height:18px;">'
            + '<span style="font-size:13px;">Interaktiv (anklickbar &amp; in Suche sichtbar)</span></label></div>'
            + '<div id="' + mapId + '-hide-label-section" style="display:none;margin-bottom:12px;"><label style="display:flex;align-items:center;gap:8px;cursor:pointer;">'
            + '<input type="checkbox" id="' + mapId + '-el-hide-label" style="width:18px;height:18px;">'
            + '<span style="font-size:13px;">Beschriftung ausblenden</span></label></div>'
            + '<div style="margin-bottom:12px;"><label style="display:block;font-size:10px;font-weight:800;text-transform:uppercase;opacity:0.5;margin-bottom:4px;">Darstellung</label>'
            + '<div style="display:flex;gap:8px;">'
            + '<button type="button" id="' + mapId + '-rep-color-btn" onclick="fm_switchRep(\'' + mapId + '\',\'color\')" style="flex:1;padding:8px;border:2px solid var(--border-color);background:var(--ui-bg);color:var(--text-main);border-radius:8px;font-weight:600;cursor:pointer;">Farbe</button>'
            + '<button type="button" id="' + mapId + '-rep-icon-btn" onclick="fm_switchRep(\'' + mapId + '\',\'icon\')" style="flex:1;padding:8px;border:2px solid var(--border-color);background:var(--ui-bg);color:var(--text-main);border-radius:8px;font-weight:600;cursor:pointer;">Icon</button>'
            + '</div></div>'
            + '<div id="' + mapId + '-color-section" style="margin-bottom:12px;">'
            + '<label style="display:block;font-size:10px;font-weight:800;text-transform:uppercase;opacity:0.5;margin-bottom:4px;">Farbe</label>'
            + '<div style="display:flex;gap:8px;align-items:center;">'
            + '<input type="color" id="' + mapId + '-el-color" value="#bc0009" style="width:40px;height:36px;border:1px solid var(--border-color);padding:2px;background:transparent;cursor:pointer;border-radius:6px;flex-shrink:0;">'
            + '<input type="text" id="' + mapId + '-el-color-hex" value="#bc0009" maxlength="7" placeholder="#rrggbb" style="flex:1;padding:7px 10px;border:1px solid var(--border-color);background:var(--input-bg);color:var(--text-main);border-radius:8px;font-size:13px;outline:none;font-family:monospace;">'
            + '</div>'
            + (function() {
                var colors = (APP_CONFIG.globalColors || []);
                if (!colors.length) return '';
                var swatches = colors.map(function(c) {
                    return '<button type="button" title="' + (c.name || c.label || c.id) + '" onclick="fm_pickGlobalColor(\'' + mapId + '\',\'' + c.id + '\',\'' + c.hex + '\')" style="width:24px;height:24px;border-radius:50%;border:2px solid transparent;background:' + c.hex + ';cursor:pointer;flex-shrink:0;" data-color-id="' + c.id + '"></button>';
                }).join('');
                return '<div id="' + mapId + '-color-palette" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;">' + swatches + '</div>';
            })()
            + '</div>'
            + '<div id="' + mapId + '-icon-section" style="display:none;margin-bottom:12px;">'
            + '<label style="display:block;font-size:10px;font-weight:800;text-transform:uppercase;opacity:0.5;margin-bottom:4px;">Icon</label>'
            + '<button type="button" onclick="fm_openIconBrowser(\'' + mapId + '\')" style="width:100%;padding:10px;border:1px solid var(--border-color);background:var(--ui-bg);color:var(--text-main);border-radius:8px;font-weight:600;cursor:pointer;">Icon auswählen</button>'
            + '<div id="' + mapId + '-icon-preview" style="display:none;margin-top:8px;padding:10px;border:1px solid var(--border-color);border-radius:8px;background:var(--input-bg);display:none;align-items:center;gap:10px;">'
            + '<img id="' + mapId + '-icon-preview-img" src="" style="width:32px;height:32px;object-fit:contain;">'
            + '<span id="' + mapId + '-icon-preview-name" style="flex:1;font-size:12px;font-weight:600;color:var(--text-main);"></span>'
            + '<button type="button" onclick="fm_clearIcon(\'' + mapId + '\')" style="padding:4px 8px;background:#ef4444;color:white;border:none;border-radius:4px;font-size:10px;cursor:pointer;">✕</button>'
            + '</div></div>'
            + '<div style="margin-bottom:12px;"><label style="display:block;font-size:10px;font-weight:800;text-transform:uppercase;opacity:0.5;margin-bottom:4px;">Label-Richtung</label>'
            + '<select id="' + mapId + '-el-label-dir" style="width:100%;padding:10px;border:1px solid var(--border-color);background:var(--input-bg);color:var(--text-main);border-radius:8px;font-size:14px;">'
            + '<option value="auto">Automatisch (Punkt)</option><option value="center">Zentriert (Fläche)</option>'
            + '<option value="right">Rechts</option><option value="left">Links</option><option value="top">Oben</option><option value="bottom">Unten</option>'
            + '</select></div>'
            + '<div id="' + mapId + '-display-mode-section" style="display:none;margin-bottom:12px;"><label style="display:block;font-size:10px;font-weight:800;text-transform:uppercase;opacity:0.5;margin-bottom:4px;">Anzeige auf Karte</label>'
            + '<select id="' + mapId + '-el-display-mode" style="width:100%;padding:10px;border:1px solid var(--border-color);background:var(--input-bg);color:var(--text-main);border-radius:8px;font-size:14px;">'
            + '<option value="name">Name anzeigen</option><option value="icon">Icon anzeigen</option>'
            + '</select></div>'
            + '<div id="' + mapId + '-modal-feedback" style="display:none;padding:10px;border-radius:8px;margin-bottom:12px;font-size:12px;font-weight:600;text-align:center;"></div>'
            + '<div id="' + mapId + '-replace-choice" style="display:none;padding:16px;border:1px solid var(--border-color);border-radius:12px;margin-bottom:12px;">'
            + '<p style="font-size:11px;font-weight:800;text-transform:uppercase;opacity:0.5;margin-bottom:8px;text-align:center;">Geometrie ändern</p>'
            + '<div style="display:flex;gap:8px;">'
            + '<button onclick="fm_doReplace(\'' + mapId + '\',\'redraw\')" style="flex:1;padding:10px;border:none;background:#f59e0b;color:white;border-radius:8px;font-weight:700;cursor:pointer;font-size:12px;">Neu zeichnen</button>'
            + '<button onclick="fm_doReplace(\'' + mapId + '\',\'move\')" style="flex:1;padding:10px;border:none;background:#3b82f6;color:white;border-radius:8px;font-weight:700;cursor:pointer;font-size:12px;">Punkte verschieben</button>'
            + '</div>'
            + '<button onclick="fm_cancelReplace(\'' + mapId + '\')" style="width:100%;margin-top:8px;padding:6px;border:none;background:transparent;color:var(--text-muted);font-weight:700;cursor:pointer;font-size:11px;">Abbrechen</button>'
            + '</div>'
            + '</div>'
            + '<div class="fm-modal-footer">'
            + '<div id="' + mapId + '-modal-actions-create" class="fm-modal-actions">'
            + '<button id="' + mapId + '-save-btn" onclick="fm_saveElement(\'' + mapId + '\')" style="flex:1;padding:12px;border:none;background:#10b981;color:white;border-radius:12px;font-weight:700;cursor:pointer;font-size:14px;">Element speichern</button>'
            + '</div>'
            + '<div id="' + mapId + '-modal-actions-edit" class="fm-modal-actions fm-edit-actions" style="display:none;">'
            + '<button id="' + mapId + '-replace-btn" onclick="fm_startReplace(\'' + mapId + '\')" title="Geometrie ändern" style="flex:1;height:48px;border:none;background:#f59e0b;color:white;border-radius:12px;font-weight:700;cursor:pointer;font-size:13px;display:flex;align-items:center;justify-content:center;gap:6px;"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg><span>Position</span></button>'
            + '<button id="' + mapId + '-delete-btn" onclick="fm_deleteElement(\'' + mapId + '\')" style="flex:1;height:48px;border:none;background:#ef4444;color:white;border-radius:12px;font-weight:700;cursor:pointer;font-size:13px;">Löschen</button>'
            + '<button id="' + mapId + '-update-btn" onclick="fm_updateElement(\'' + mapId + '\')" style="flex:1;height:48px;border:none;background:#10b981;color:white;border-radius:12px;font-weight:700;cursor:pointer;font-size:13px;">Speichern</button>'
            + '</div>'
            + '</div>'
            + '</div></div>';

        // Icon-Browser-Modal
        var iconBrowserHtml = '<div id="' + mapId + '-icon-browser" class="fm-modal-overlay" style="display:none;">'
            + '<div class="fm-modal-content" style="max-width:600px;">'
            + '<h2 style="font-size:20px;font-weight:900;margin-bottom:20px;color:var(--text-main);">Icon auswählen</h2>'
            + '<div id="' + mapId + '-icon-browser-list" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:12px;max-height:400px;overflow-y:auto;padding:12px;background:var(--input-bg);border-radius:12px;border:1px solid var(--border-color);">Lade...</div>'
            + '<div style="margin-top:20px;">'
            + '<button onclick="fm_closeIconBrowser(\'' + mapId + '\')" style="width:100%;padding:12px;border:none;background:var(--brand-color);color:white;border-radius:12px;font-weight:700;cursor:pointer;">Schließen</button>'
            + '</div></div></div>';

        wrap.insertAdjacentHTML('beforeend', devHtml + editorModalHtml + iconBrowserHtml);
    }

    // ===== KARTEN-STATE =====
    var map, roomLayers, currentOverlay = null, activePopup = null;
    var highlightLayer, previewLayer;
    var collectedPoints = [], isRecording = false;
    var allFloorsCache = {}, currentFloorId = APP_CONFIG.defaultFloorId;
    var expandedSections = new Set([APP_CONFIG.defaultFloorId]);
    var currentElements = [];
    var currentEditElement = null, isReplaceMode = false;
    var selectedIconPath = null, currentRepresentation = 'color';
    var isDraggingPoint = false, draggingPointIndex = -1;

    var ICON_SIZE_BY_ZOOM = { '-1': 16, '0': 20, '1': 24, '2': 35, '3': 64 };

    function getIconSize(base, zoom) {
        var key = String(Math.round(zoom));
        var factor = (ICON_SIZE_BY_ZOOM[key] !== undefined ? ICON_SIZE_BY_ZOOM[key] : 24) / 24;
        return Math.round(base * factor);
    }

    function g(suffix) { return document.getElementById(mapId + '-' + suffix); }

    // ===== KARTE INITIALISIEREN =====
    var mapOptions = {
        crs: L.CRS.Simple, minZoom: -1, maxZoom: 3,
        zoomControl: true, attributionControl: true, tap: true, preferCanvas: false
    };
    
    // Attribution ein/ausblenden
    var showAttr = APP_CONFIG.showAttribution !== undefined ? (APP_CONFIG.showAttribution === "true" || APP_CONFIG.showAttribution === true) : true;
    if (!showAttr) {
        mapOptions.attributionControl = false;
    }

    map = L.map(mapId, mapOptions);

    if (showAttr) {
        var showPluginAttr = APP_CONFIG.showPluginAttribution !== undefined ? (APP_CONFIG.showPluginAttribution === "true" || APP_CONFIG.showPluginAttribution === true) : true;
        if (showPluginAttr) {
            map.attributionControl.addAttribution('<a href="https://github.com/hellodosi/WP-Floormap" target="_blank">WP-Floormap-Plugin</a>');
        }
    }

    map.createPane('decorativePane'); map.getPane('decorativePane').style.zIndex = 445; map.getPane('decorativePane').style.pointerEvents = 'none';
    map.createPane('polygonPane');    map.getPane('polygonPane').style.zIndex = 450;
    map.createPane('pointPane');      map.getPane('pointPane').style.zIndex = 460;
    map.createPane('previewPane');    map.getPane('previewPane').style.zIndex = 470;

    roomLayers    = L.layerGroup();
    highlightLayer = L.layerGroup().addTo(map);
    previewLayer   = L.layerGroup().addTo(map);

    map.zoomControl.setPosition('bottomright');
    map.on('zoomend', function() { 
        updateLabelVisibility(); 
        updateIconSizes(); 
    });

    if (IS_DEV_MODE) {
        map.on('zoom', function() {
            var zd = g('zoom-display'); if (zd) zd.innerText = map.getZoom().toFixed(2);
        });
        map.on('mousemove', function(e) {
            var cd = g('coord-display'); if (cd) cd.innerText = '[' + Math.round(e.latlng.lat) + ', ' + Math.round(e.latlng.lng) + ']';
            if (isDraggingPoint && draggingPointIndex >= 0) {
                collectedPoints[draggingPointIndex] = [Math.round(e.latlng.lat), Math.round(e.latlng.lng)];
                updatePreview();
            }
        });
        map.on('mouseup', function() { if (isDraggingPoint) { isDraggingPoint = false; draggingPointIndex = -1; map.dragging.enable(); map.getContainer().style.cursor = ''; } });

        var startBtn = g('start-btn'), stopBtn = g('stop-btn');
        if (startBtn) startBtn.addEventListener('click', function(e) { L.DomEvent.stop(e); startRecording(); });
        if (stopBtn)  stopBtn.addEventListener('click',  function(e) { L.DomEvent.stop(e); stopRecording(); });
    }

    // Click-Propagation für UI-Elemente deaktivieren
    ['fm-search-container', 'fm-floor-switcher-container', 'fm-dev-tools', 'fm-info-sheet'].forEach(function(cls) {
        var els = wrap.querySelectorAll('.' + cls);
        els.forEach(function(el) { L.DomEvent.disableClickPropagation(el); });
    });

    map.on('click', function(e) {
        if (isRecording) { if (isDraggingPoint) return; collectedPoints.push([Math.round(e.latlng.lat), Math.round(e.latlng.lng)]); updatePreview(); }
        else { closeInfo(); toggleSearchMenu(true); }
    });

    // ===== SUCHE =====
    var searchInput = g('search'), clearBtn = g('clear-btn');

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            updateSearchResults(e.target.value);
            if (clearBtn) clearBtn.style.display = e.target.value.trim() ? 'flex' : 'none';
        });
        searchInput.addEventListener('focus', function() { /* Liste nicht automatisch öffnen bei Fokus */ });
        searchInput.addEventListener('keydown', function(e) { if (e.key === 'Enter') { toggleSearchMenu(true); searchInput.blur(); } });
    }
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = ''; clearBtn.style.display = 'none';
            highlightLayer.clearLayers(); updateSearchResults('');
        });
    }
    var menuBtn = g('menu-btn');
    if (menuBtn) menuBtn.addEventListener('click', function() { toggleSearchMenu(); });

    var infoCloseBtn = g('info-close');
    if (infoCloseBtn) infoCloseBtn.addEventListener('click', closeInfo);

    // ===== STOCKWERK-BUTTONS =====
    function renderFloorButtons() {
        var switcher = g('switcher'); if (!switcher) return;
        switcher.innerHTML = '';
        if (APP_CONFIG.floors.length <= 1) {
            console.log('WP Floormap: Switcher ausgeblendet, da nur 1 Stockwerk vorhanden.');
            switcher.parentElement.style.display = 'none';
            return;
        }
        console.log('WP Floormap: Zeige Switcher für ' + APP_CONFIG.floors.length + ' Stockwerke.');
        switcher.parentElement.style.display = 'block';
        APP_CONFIG.floors.forEach(function(f) {
            var btn = document.createElement('button');
            btn.id = mapId + '-btn-f' + f.id;
            btn.className = 'fm-floor-btn';
            btn.innerText = f.label;
            btn.addEventListener('click', function() { switchFloor(f.id, true); });
            switcher.appendChild(btn);
        });
    }

    // ===== FLOOR LADEN =====
    async function preloadAllFloors() {
        var promises = APP_CONFIG.floors.map(async function(floor) {
            try {
                var res = await fetch(API_BASE + '/floor/' + floor.id);
                allFloorsCache[floor.id] = await res.json();
            } catch(e) { allFloorsCache[floor.id] = { name: floor.name, imageUrl: '', elements: [] }; }
        });
        await Promise.all(promises);
    }

    async function reloadFloorCache(fId) {
        try {
            var res = await fetch(API_BASE + '/floor/' + fId);
            allFloorsCache[fId] = await res.json();
        } catch(e) { allFloorsCache[fId] = { name: fId, imageUrl: '', elements: [] }; }
    }


    async function switchFloor(fId, keepView) {
        var info = APP_CONFIG.floors.find(function(f) { return f.id === fId; });
        if (!info) return;
        var savedCenter = keepView ? map.getCenter() : null;
        var savedZoom   = keepView ? map.getZoom() : null;
        try {
            if (!allFloorsCache[fId]) await reloadFloorCache(fId);
            var config = allFloorsCache[fId];
            currentFloorId = fId;
            var searchVal = searchInput ? searchInput.value : '';
            if (!searchVal) { expandedSections.clear(); expandedSections.add(fId); }
            wrap.querySelectorAll('.fm-floor-btn').forEach(function(btn) {
                btn.classList.remove('active');
                if (btn.id === mapId + '-btn-f' + fId) btn.classList.add('active');
            });
            await new Promise(function(resolve) {
                var img = new Image();
                img.onload = function() {
                    var bounds = L.latLngBounds([0, 0], [info.height, info.width]);
                    
                    if (currentOverlay) map.removeLayer(currentOverlay);
                    currentOverlay = L.imageOverlay(config.imageUrl, bounds).addTo(map);

                    if (keepView && savedCenter && savedZoom !== null) {
                        map.setView(savedCenter, savedZoom);
                    } else {
                        map.fitBounds(bounds);
                    }
                    
                    drawElements(config.elements);
                    updateLabelVisibility();
                    updateSearchResults(searchVal, true);
                    resolve();
                };
                img.onerror = function() { resolve(); };
                img.src = config.imageUrl;
            });
        } catch(e) { console.error('Fehler beim Stockwerkwechsel:', e); }
        closeInfo();
    }

    // ===== ELEMENTE ZEICHNEN =====
    function resolveColor(el) {
        if (el.colorId) {
            var gc = (APP_CONFIG.globalColors || []).find(function(c) { return c.id === el.colorId; });
            if (gc) return gc.hex;
        }
        return el.color || '#bc0009';
    }

    function updateIconSizes() {
        if (!map || currentElements.length === 0) return;
        var zoom = map.getZoom();
        roomLayers.eachLayer(function(layer) {
            if (layer._iconSizeBase && layer.setIcon) {
                var s = getIconSize(layer._iconSizeBase, zoom);
                layer.setIcon(layer._iconFactory(s));
            }
        });
    }

    function drawElements(elements) {
        currentElements = elements;
        roomLayers.clearLayers();
        elements.forEach(function(el) {
            var layer;
            var isInteractive = el.interactive !== 0;
            if (el.type === 'polygon') {
                var showLabel = isInteractive || !el.hide_label;
                var polygonPane = (!IS_DEV_MODE && !isInteractive) ? 'decorativePane' : 'polygonPane';
                layer = L.polygon(el.coords, {
                    pane: polygonPane, color: resolveColor(el), fillColor: resolveColor(el),
                    weight: 0.5, fillOpacity: 0.8, interactive: IS_DEV_MODE || isInteractive, bubblingMouseEvents: true
                });
                if (showLabel) {
                    if (el.display_mode === 'icon' && el.icon) {
                        var bounds = L.polygon(el.coords).getBounds();
                        var center = bounds.getCenter();
                        var iconSizeBase = 32;
                        var iconSize = getIconSize(iconSizeBase, map.getZoom());
                        var makePolyIcon = function(s) { return L.divIcon({ html: '<img src="' + el.icon + '" style="width:' + s + 'px;height:' + s + 'px;object-fit:contain;">', className: 'polygon-icon-marker', iconSize: [s, s], iconAnchor: [s/2, s/2] }); };
                        var iconMarker = L.marker(center, { icon: makePolyIcon(iconSize), pane: 'markerPane' });
                        iconMarker._iconSizeBase = iconSizeBase; iconMarker._iconFactory = makePolyIcon;
                        iconMarker.addTo(roomLayers);
                        if (IS_DEV_MODE || isInteractive) {
                            iconMarker.on('click', function(e) {
                                if (isRecording) return; L.DomEvent.stopPropagation(e);
                                if (IS_DEV_MODE) { openEditorModal('edit', el); } else if (isInteractive) { showInfo(el); highlightLayer.clearLayers(); }
                            });
                        }
                    } else {
                        var polyDir = el.label_direction && el.label_direction !== 'auto' && el.label_direction !== 'center' ? el.label_direction : 'center';
                        layer.bindTooltip(el.name, { permanent: true, direction: polyDir, className: polyDir === 'center' ? 'room-label' : 'room-label-point' });
                    }
                }
            } else if (el.type === 'point') {
                var showTextLabel = isInteractive || !el.hide_label;
                if (el.icon) {
                    var ptBase = 24, ptSize = getIconSize(ptBase, map.getZoom());
                    var makePtIcon = function(s) { return L.icon({ iconUrl: el.icon, iconSize: [s, s], iconAnchor: [s/2, s/2], tooltipAnchor: [0, 0] }); };
                    layer = L.marker(el.coords, { icon: makePtIcon(ptSize), pane: 'markerPane' });
                    layer._iconSizeBase = ptBase; layer._iconFactory = makePtIcon; layer._isIconMarker = true;
                } else {
                    layer = L.circleMarker(el.coords, { pane: 'pointPane', radius: 8, fillColor: resolveColor(el), color: '#fff', weight: 2, opacity: 1, fillOpacity: 0.9 });
                }
                if (showTextLabel) {
                    var labelDir = el.label_direction || 'auto';
                    var r = 8;
                    var offsetMap = { right: [r+4,0], left: [-(r+4),0], top: [0,-(r+4)], bottom: [0,r+4], auto: [r+4,0] };
                    layer.bindTooltip(el.name, { permanent: true, direction: labelDir === 'auto' ? 'right' : labelDir, offset: offsetMap[labelDir] || [r+4,0], className: 'room-label-point' });
                    if (labelDir === 'auto') layer._isPointLabel = true;
                }
            }
            if (layer) {
                layer.addTo(roomLayers);
                if (IS_DEV_MODE || isInteractive) {
                    layer.on('click', function(e) {
                        if (isRecording) return; L.DomEvent.stopPropagation(e);
                        if (IS_DEV_MODE) { openEditorModal('edit', el); }
                        else if (isInteractive) { showInfo(el); highlightLayer.clearLayers(); }
                    });
                    if (isInteractive) {
                        layer.on('mouseover', function() { var tt = this.getTooltip(); if (tt) tt.setOpacity(1); });
                        layer.on('mouseout', function() { var tt = this.getTooltip(); var zoom = map.getZoom(); var thr = APP_CONFIG.labelZoomThreshold !== undefined ? APP_CONFIG.labelZoomThreshold : 0.8; if (tt) tt.setOpacity(zoom >= thr ? 1 : 0); });
                    }
                }
            }
        });
        map.addLayer(roomLayers);
        setTimeout(deconflictPointLabels, 50);
    }

    function updateLabelVisibility() {
        try {
            var zoom = map.getZoom();
            var thr = APP_CONFIG.labelZoomThreshold !== undefined ? APP_CONFIG.labelZoomThreshold : 0.8;
            roomLayers.eachLayer(function(layer) { var tt = layer.getTooltip(); if (tt) tt.setOpacity(zoom >= thr ? 1 : 0); });
        } catch(e) {}
    }

    function deconflictPointLabels() {
        var pointLayers = [];
        roomLayers.eachLayer(function(layer) { if (layer._isPointLabel && layer.getTooltip()) pointLayers.push(layer); });
        if (pointLayers.length === 0) return;
        var directions = ['right', 'top', 'left', 'bottom'];
        var r = 8, PAD = 6;
        var offsetMap = { right: [r+4,0], left: [-(r+4),0], top: [0,-(r+4)], bottom: [0,r+4] };
        function getLayerPixel(layer) { if (layer.getLatLng) return map.latLngToContainerPoint(layer.getLatLng()); return null; }
        function getBBox(px, dir, text) {
            var w = Math.max(40, text.length * 7.5) + 16, h = 22;
            var o = { right: {x:px.x+14,y:px.y-h/2}, left: {x:px.x-14-w,y:px.y-h/2}, top: {x:px.x-w/2,y:px.y-14-h}, bottom: {x:px.x-w/2,y:px.y+14} }[dir] || {x:px.x+14,y:px.y-h/2};
            return { x: o.x-PAD, y: o.y-PAD, w: w+PAD*2, h: h+PAD*2 };
        }
        function overlaps(a, b) { return !(a.x+a.w<=b.x||b.x+b.w<=a.x||a.y+a.h<=b.y||b.y+b.h<=a.y); }
        var chosen = [];
        pointLayers.forEach(function(layer) {
            var px = getLayerPixel(layer); if (!px) return;
            var text = layer.getTooltip().getContent();
            var bestDir = 'right', bestScore = Infinity;
            directions.forEach(function(dir) {
                var bbox = getBBox(px, dir, text), score = 0;
                chosen.forEach(function(prev) { if (overlaps(bbox, prev)) score += 10; });
                if (dir === 'left' || dir === 'bottom') score += 0.5;
                if (score < bestScore) { bestScore = score; bestDir = dir; }
            });
            layer.unbindTooltip();
            layer.bindTooltip(text, { permanent: true, direction: bestDir, offset: offsetMap[bestDir] || [r+4,0], className: 'room-label-point' });
            chosen.push(getBBox(px, bestDir, text));
        });
        updateLabelVisibility();
    }

    // ===== SUCHE =====
    function addPoiHighlight(el) {
        if (el.type === 'polygon') {
            L.polygon(el.coords, { className: 'poi-highlight-poly', color: '#bc0009', weight: 2, fill: false, interactive: false }).addTo(highlightLayer);
        } else {
            L.circleMarker(el.coords, { className: 'poi-highlight', radius: el.icon ? 16 : 8, color: '#bc0009', fillColor: '#bc0009', fillOpacity: 0.3, weight: 2, interactive: false }).addTo(highlightLayer);
        }
    }

    function updateSearchResults(q, suppressOpen) {
        var list = g('results-list'); if (!list) return;
        var resultsContainer = g('results');
        var isSearching = q.length > 0;
        list.innerHTML = '';
        var queryClean = q.toLowerCase().trim();
        highlightLayer.clearLayers();
        var hasResults = false;
        APP_CONFIG.floors.forEach(function(floor) {
            var data = allFloorsCache[floor.id]; if (!data) return;
            var interactiveEls = data.elements.filter(function(el) { return el.interactive !== 0; });
            var filtered = interactiveEls.filter(function(el) { return el.name.toLowerCase().includes(queryClean) || (el.description || '').toLowerCase().includes(queryClean); });
            if (isSearching && floor.id === currentFloorId) filtered.forEach(addPoiHighlight);
            if (filtered.length > 0 || (!isSearching && interactiveEls.length > 0)) {
                hasResults = true;
                var isExpanded = isSearching || expandedSections.has(floor.id);
                var section = document.createElement('div');
                var displayEls = isSearching ? filtered : interactiveEls;
                section.innerHTML = '<div style="border-bottom:1px solid var(--border-color);">'
                    + '<button class="fm-floor-group-header" onclick="(function(){var inst=window.WPFloormapInstances[\'' + mapId + '\']; if(inst.expandedSections.has(' + floor.id + '))inst.expandedSections.delete(' + floor.id + ');else inst.expandedSections.add(' + floor.id + ');inst.updateSearchResults(document.getElementById(\'' + mapId + '-search\').value, true);})()">'
                    + '<span>' + data.name + (floor.id === currentFloorId ? ' <em style="opacity:0.6;font-size:9px;">(Aktuell)</em>' : '') + '</span>'
                    + '<svg width="12" height="12" style="transform:' + (isExpanded ? 'rotate(180deg)' : 'rotate(0deg)') + '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>'
                    + '</button>'
                    + '<div class="fm-accordion-content ' + (isExpanded ? 'expanded' : '') + '">'
                    + displayEls.map(function(el) {
                        var visual = el.icon ? '<img src="' + el.icon + '" style="width:16px;height:16px;object-fit:contain;">' : '<div style="width:10px;height:10px;border-radius:50%;background:' + (el.color || '#bc0009') + ';"></div>';
                        return '<button class="fm-result-item" onclick="window.WPFloormapInstances[\'' + mapId + '\'].navigateToElement(' + floor.id + ',\'' + el.id + '\')">'
                            + '<div style="width:24px;height:24px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">' + visual + '</div>'
                            + '<div style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + el.name + '</div>'
                            + '</button>';
                    }).join('')
                    + '</div></div>';
                list.appendChild(section);
            }
        });
        if (resultsContainer && !suppressOpen) {
            if (isSearching) resultsContainer.classList.add('visible');
            else if (!isSearching && !hasResults) resultsContainer.classList.remove('visible');
        }
    }

    // Globale Instanzen für den Zugriff aus HTML-onclick-Handlern
    if (!window.WPFloormapInstances) window.WPFloormapInstances = {};
    window.WPFloormapInstances[mapId] = {
        expandedSections: expandedSections,
        updateSearchResults: updateSearchResults,
        navigateToElement: navigateToElement
    };

    function toggleSearchMenu(forceHide) {
        var container = g('results'); if (!container) return;
        if (forceHide === true) { container.classList.remove('visible'); return; }
        if (container.classList.contains('visible')) container.classList.remove('visible');
        else { container.classList.add('visible'); updateSearchResults(searchInput ? searchInput.value : ''); }
    }

    async function navigateToElement(fId, elId, fromInitial) {
        if (!fromInitial) toggleSearchMenu(true);
        if (currentFloorId !== fId) {
            await switchFloor(fId, true);
        }
        
        // Suche das Element in den aktuell gezeichneten Layern von roomLayers, 
        // oder direkt im Cache falls noch nicht gezeichnet.
        var targetEl = allFloorsCache[fId] && allFloorsCache[fId].elements.find(function(e) { return e.id === elId; });
        if (!targetEl) return;
        
        // Wir warten kurz, bis die Layer tatsächlich auf der Karte sind
        var attempts = 0;
        var tryZoom = function() {
            if (targetEl.type === 'polygon') {
                var poly = L.polygon(targetEl.coords);
                map.fitBounds(poly.getBounds(), { padding: [50, 50], maxZoom: 2 });
            } else {
                map.setView(targetEl.coords, 2);
            }
            showInfo(targetEl);
            highlightLayer.clearLayers();
            allFloorsCache[fId].elements.filter(function(e) { return e.name === targetEl.name; }).forEach(addPoiHighlight);
        };

        if (currentFloorId === fId) {
            tryZoom();
        } else {
            // Falls wir gerade erst gewechselt haben, geben wir dem Rendering etwas Zeit
            setTimeout(tryZoom, 100);
        }
    }

    function showInfo(room) {
        if (window.innerWidth >= 768) {
            // Desktop: Leaflet Popup
            if (activePopup) {
                map.closePopup(activePopup);
            }
            
            var popupContent = '<div class="fm-popup-content">' +
                               '<h3 class="fm-popup-title">' + room.name + '</h3>' +
                               (room.description ? '<p class="fm-popup-desc">' + room.description + '</p>' : '') +
                               '</div>';
            
            var popupCoords;
            if (room.type === 'polygon') {
                var poly = L.polygon(room.coords);
                popupCoords = poly.getBounds().getCenter();
            } else {
                popupCoords = room.coords;
            }
            
            activePopup = L.popup({
                closeButton: true,
                autoClose: false,
                closeOnClick: false,
                className: 'fm-custom-popup',
                offset: [0, -10]
            })
            .setLatLng(popupCoords)
            .setContent(popupContent)
            .openOn(map);
            
            // Wenn das Popup geschlossen wird, Referenz leeren
            activePopup.on('remove', function() {
                activePopup = null;
            });

            // Sicherstellen, dass das mobile Info-Sheet geschlossen ist
            var sheet = g('info'); if (sheet) sheet.classList.remove('visible');
        } else {
            // Mobile: Bestehendes Info-Sheet
            var title = g('info-title'), desc = g('info-desc'), sheet = g('info');
            if (title) title.innerText = room.name;
            if (desc)  desc.innerText  = room.description || '';
            if (sheet) sheet.classList.add('visible');
            
            // Sicherstellen, dass ein eventuelles Desktop-Popup geschlossen wird
            if (activePopup) {
                map.closePopup(activePopup);
                activePopup = null;
            }
        }
    }

    function closeInfo() {
        var sheet = g('info'); if (sheet) sheet.classList.remove('visible');
        if (activePopup) {
            map.closePopup(activePopup);
            activePopup = null;
        }
    }

    // ===== DEV-MODUS: RECORDING =====
    function updatePreview() {
        previewLayer.clearLayers();
        var color = g('el-color') ? g('el-color').value : '#bc0009';
        collectedPoints.forEach(function(p, index) {
            var marker = L.circleMarker(p, { radius: 7, color: '#fff', weight: 2, fillColor: color, fillOpacity: 1, bubblingMouseEvents: false, pane: 'previewPane' }).addTo(previewLayer);
            marker.on('mousedown', function(e) { L.DomEvent.stopPropagation(e); isDraggingPoint = true; draggingPointIndex = index; map.dragging.disable(); map.getContainer().style.cursor = 'grabbing'; });
            marker.on('mouseup', function() { if (isDraggingPoint) { isDraggingPoint = false; draggingPointIndex = -1; map.dragging.enable(); map.getContainer().style.cursor = ''; } });
        });
        if (collectedPoints.length >= 2) L.polyline(collectedPoints, { color: color, dashArray: '5,10' }).addTo(previewLayer);
        if (collectedPoints.length >= 3) L.polyline([collectedPoints[collectedPoints.length-1], collectedPoints[0]], { color: color, dashArray: '5,5', weight: 2, opacity: 0.8 }).addTo(previewLayer);
        var st = g('status-text'); if (st) st.innerText = collectedPoints.length + ' Punkt(e) gesetzt';
    }

    function startRecording() {
        isRecording = true; previewLayer.clearLayers(); highlightLayer.clearLayers(); closeInfo();
        map.getContainer().classList.add('recording-mode');
        var sb = g('start-btn'), stb = g('stop-btn'), st = g('status-text');
        if (sb) sb.style.display = 'none'; if (stb) stb.style.display = 'block'; if (st) st.innerText = 'Erfasse Punkte...';
        ['polygonPane','pointPane','markerPane','tooltipPane','shadowPane'].forEach(function(p) { var pane = map.getPane(p); if (pane) pane.style.pointerEvents = 'none'; });
        var pp = map.getPane('previewPane'); if (pp) pp.style.pointerEvents = 'auto';
        // collectedPoints werden erst nach dem Speichern geleert
    }

    function stopRecording() {
        if (collectedPoints.length === 0) {
            if (isReplaceMode) { isReplaceMode = false; delete allFloorsCache[currentFloorId]; switchFloor(currentFloorId, true).then(function() { openEditorModal('edit', currentEditElement); }); }
            resetRecordingUI(); return;
        }
        if (collectedPoints.length === 2) { alert('Ungültige Anzahl: 2 Punkte ergeben keine gültige Fläche.'); return; }
        isRecording = false; resetRecordingUI();
        if (isReplaceMode) finishReplace(); else openEditorModal('create', null);
    }

    function resetRecordingUI() {
        isRecording = false; map.getContainer().classList.remove('recording-mode');
        var sb = g('start-btn'), stb = g('stop-btn'), st = g('status-text');
        if (sb) sb.style.display = 'block'; if (stb) stb.style.display = 'none'; if (st) st.innerText = 'Inaktiv';
        ['polygonPane','pointPane','markerPane','tooltipPane','shadowPane'].forEach(function(p) { var pane = map.getPane(p); if (pane) pane.style.pointerEvents = 'auto'; });
    }

    async function finishReplace() {
        if (!currentEditElement || collectedPoints.length === 0) { isReplaceMode = false; return; }
        var type = collectedPoints.length >= 3 ? 'polygon' : 'point';
        var updatedEl = Object.assign({}, currentEditElement, { type: type, coords: type === 'polygon' ? collectedPoints : collectedPoints[0] });
        isReplaceMode = false;
        try {
            await apiRequest('PUT', '/elements/' + currentEditElement.id, { element: updatedEl });
            previewLayer.clearLayers(); collectedPoints = []; currentEditElement = null;
            delete allFloorsCache[currentFloorId]; await switchFloor(currentFloorId, true);
        } catch(err) { openEditorModal('edit', currentEditElement); showModalFeedback('Fehler: ' + err.message, false); }
    }

    // ===== EDITOR MODAL =====
    function openEditorModal(mode, element) {
        var modal = g('editor-modal'); if (!modal) return;
        modal.style.display = 'flex';
        var title = g('modal-title');
        var actCreate = g('modal-actions-create'), actEdit = g('modal-actions-edit'), choice = g('replace-choice');
        if (choice) choice.style.display = 'none';
        hideModalFeedback();
        if (mode === 'create') {
            if (title) title.innerText = 'Neues Element';
            if (actCreate) actCreate.style.display = 'flex'; if (actEdit) actEdit.style.display = 'none';
            var nameEl = g('el-name'), descEl = g('el-desc'), colorEl = g('el-color'), hexEl = g('el-color-hex');
            if (nameEl) nameEl.value = ''; if (descEl) descEl.value = '';
            if (colorEl) { colorEl.value = '#bc0009'; colorEl.dataset.colorId = ''; }
            if (hexEl) hexEl.value = '#bc0009';
            var palette = g('color-palette');
            if (palette) { palette.querySelectorAll('button').forEach(function(btn) { btn.style.borderColor = 'transparent'; }); }
            var intEl = g('el-interactive'), hlEl = g('el-hide-label');
            if (intEl) intEl.checked = true; if (hlEl) hlEl.checked = false;
            var dmEl = g('el-display-mode'), ldEl = g('el-label-dir');
            if (dmEl) dmEl.value = 'name';
            var type = collectedPoints.length >= 3 ? 'polygon' : 'point';
            if (ldEl) ldEl.value = type === 'point' ? 'auto' : 'center';
            selectedIconPath = null; fm_clearIcon(mapId);
            fm_switchRep(mapId, 'color');
            updateHideLabelVisibility();
        } else {
            currentEditElement = element;
            if (title) title.innerText = 'Element bearbeiten';
            if (actCreate) actCreate.style.display = 'none'; if (actEdit) actEdit.style.display = 'flex';
            var nameEl = g('el-name'), descEl = g('el-desc'), colorEl = g('el-color'), hexEl = g('el-color-hex');
            if (nameEl) nameEl.value = element.name || '';
            if (descEl) descEl.value = element.description || '';
            var hex = element.color || '#bc0009';
            if (element.colorId) { var gc = (APP_CONFIG.globalColors || []).find(function(c) { return c.id === element.colorId; }); if (gc) hex = gc.hex; }
            if (colorEl) { colorEl.value = hex; colorEl.dataset.colorId = element.colorId || ''; }
            if (hexEl) hexEl.value = hex;
            var palette = g('color-palette');
            if (palette) { palette.querySelectorAll('button').forEach(function(btn) { btn.style.borderColor = element.colorId && btn.dataset.colorId === element.colorId ? 'var(--text-main)' : 'transparent'; }); }
            var intEl = g('el-interactive'), hlEl = g('el-hide-label'), dmEl = g('el-display-mode'), ldEl = g('el-label-dir');
            if (intEl) intEl.checked = element.interactive !== 0;
            if (hlEl) hlEl.checked = element.hide_label === 1;
            if (dmEl) dmEl.value = element.display_mode || 'name';
            if (ldEl) ldEl.value = element.label_direction || (element.type === 'point' ? 'auto' : 'center');
            if (element.icon) { selectedIconPath = element.icon; fm_switchRep(mapId, 'icon'); fm_showIconPreview(mapId, element.icon); }
            else { selectedIconPath = null; fm_switchRep(mapId, 'color'); fm_clearIcon(mapId); }
            updateHideLabelVisibility();
        }
    }

    function updateHideLabelVisibility() {
        var intEl = g('el-interactive'), hlSection = g('hide-label-section');
        if (!intEl || !hlSection) return;
        if (!intEl.checked) { hlSection.style.display = 'block'; }
        else { hlSection.style.display = 'none'; var hlEl = g('el-hide-label'); if (hlEl) hlEl.checked = false; }
    }

    var intCheckbox = g('el-interactive');
    if (intCheckbox) intCheckbox.addEventListener('change', updateHideLabelVisibility);

    var colorInput = g('el-color'), hexInput = g('el-color-hex');
    if (colorInput) colorInput.addEventListener('input', function() { if (hexInput) hexInput.value = colorInput.value; updatePreview(); });
    if (hexInput) hexInput.addEventListener('input', function() { if (/^#[0-9a-fA-F]{6}$/.test(hexInput.value.trim())) { if (colorInput) colorInput.value = hexInput.value.trim(); updatePreview(); } });

    function getColorId() {
        if (!colorInput) return null;
        var hex = colorInput.value.toLowerCase(), storedId = colorInput.dataset.colorId;
        if (storedId) { var match = (APP_CONFIG.globalColors || []).find(function(c) { return c.id === storedId; }); if (match && match.hex.toLowerCase() === hex) return storedId; }
        var found = (APP_CONFIG.globalColors || []).find(function(c) { return c.hex.toLowerCase() === hex; });
        return found ? found.id : null;
    }

    function showModalFeedback(msg, ok) {
        var fb = g('modal-feedback'); if (!fb) return;
        fb.style.display = 'block'; fb.style.background = ok ? '#10b981' : '#ef4444'; fb.style.color = 'white';
        fb.innerText = (ok ? '✓ ' : '✗ ') + msg;
    }
    function hideModalFeedback() { var fb = g('modal-feedback'); if (fb) fb.style.display = 'none'; }

    // ===== API =====
    async function apiRequest(method, endpoint, body) {
        var opts = { method: method, headers: { 'Content-Type': 'application/json' } };
        if (IS_DEV_MODE && typeof WPFloormap !== 'undefined' && WPFloormap.nonce) opts.headers['X-WP-Nonce'] = WPFloormap.nonce;
        if (body !== undefined) opts.body = JSON.stringify(body);
        var res = await fetch(API_BASE + endpoint, opts);
        var result = await res.json();
        if (result.success === false) throw new Error(result.message || 'Fehler');
        return result;
    }

    // ===== ELEMENT SPEICHERN =====
    async function saveElement() {
        var nameEl = g('el-name'), descEl = g('el-desc'), colorEl = g('el-color'), saveBtn = g('save-btn');
        var type = collectedPoints.length >= 3 ? 'polygon' : 'point';
        var element = {
            id: 'id-' + Date.now(),
            name: nameEl ? nameEl.value || 'Unbenannt' : 'Unbenannt',
            type: type,
            description: descEl ? descEl.value : '',
            coords: type === 'polygon' ? collectedPoints : collectedPoints[0],
            interactive: g('el-interactive') && g('el-interactive').checked ? 1 : 0,
            hide_label: g('el-hide-label') && g('el-hide-label').checked ? 1 : 0,
            display_mode: g('el-display-mode') ? g('el-display-mode').value : 'name',
            label_direction: g('el-label-dir') ? g('el-label-dir').value : (type === 'point' ? 'auto' : 'center'),
        };
        if (type === 'point') {
            if (currentRepresentation === 'icon' && selectedIconPath) { element.icon = selectedIconPath; element.color = null; element.colorId = null; element.icon_size = 24; }
            else { element.color = colorEl ? colorEl.value : '#bc0009'; element.colorId = getColorId(); element.icon = null; element.icon_size = null; }
        } else {
            element.color = colorEl ? colorEl.value : '#bc0009'; element.colorId = getColorId();
            element.icon = selectedIconPath || null; element.icon_size = currentRepresentation === 'icon' ? 32 : null;
        }
        hideModalFeedback(); if (saveBtn) { saveBtn.disabled = true; saveBtn.innerText = 'Speichere...'; }
        try {
            await apiRequest('POST', '/elements', { floorId: currentFloorId, element: element });
            showModalFeedback('Element gespeichert!', true);
            previewLayer.clearLayers(); collectedPoints = [];
            delete allFloorsCache[currentFloorId]; await switchFloor(currentFloorId, true);
            setTimeout(function() { if (saveBtn) { saveBtn.disabled = false; saveBtn.innerText = 'Speichern'; } fm_closeModal(mapId); }, 1500);
        } catch(err) { showModalFeedback('Fehler: ' + err.message, false); if (saveBtn) { saveBtn.disabled = false; saveBtn.innerText = 'Speichern'; } }
    }

    async function updateElement() {
        if (!currentEditElement) return;
        var nameEl = g('el-name'), descEl = g('el-desc'), colorEl = g('el-color'), updateBtn = g('update-btn');
        var updatedEl = {
            id: currentEditElement.id, type: currentEditElement.type, coords: currentEditElement.coords,
            name: nameEl ? nameEl.value || 'Unbenannt' : 'Unbenannt',
            description: descEl ? descEl.value : '',
            interactive: g('el-interactive') && g('el-interactive').checked ? 1 : 0,
            hide_label: g('el-hide-label') && g('el-hide-label').checked ? 1 : 0,
            display_mode: g('el-display-mode') ? g('el-display-mode').value : 'name',
            label_direction: g('el-label-dir') ? g('el-label-dir').value : (currentEditElement.type === 'point' ? 'auto' : 'center'),
        };
        if (currentEditElement.type === 'point') {
            if (currentRepresentation === 'icon' && selectedIconPath) { updatedEl.icon = selectedIconPath; updatedEl.color = null; updatedEl.colorId = null; updatedEl.icon_size = 24; }
            else { updatedEl.color = colorEl ? colorEl.value : '#bc0009'; updatedEl.colorId = getColorId(); updatedEl.icon = null; updatedEl.icon_size = null; }
        } else {
            updatedEl.color = colorEl ? colorEl.value : '#bc0009'; updatedEl.colorId = getColorId();
            updatedEl.icon = selectedIconPath || null; updatedEl.icon_size = currentRepresentation === 'icon' ? 32 : null;
        }
        hideModalFeedback(); if (updateBtn) { updateBtn.disabled = true; updateBtn.innerText = 'Aktualisiere...'; }
        try {
            await apiRequest('PUT', '/elements/' + currentEditElement.id, { element: updatedEl });
            showModalFeedback('Element aktualisiert!', true);
            delete allFloorsCache[currentFloorId]; await switchFloor(currentFloorId, true);
            setTimeout(function() { if (updateBtn) { updateBtn.disabled = false; updateBtn.innerText = 'Speichern'; } fm_closeModal(mapId); }, 1500);
        } catch(err) { showModalFeedback('Fehler: ' + err.message, false); if (updateBtn) { updateBtn.disabled = false; updateBtn.innerText = 'Speichern'; } }
    }

    async function deleteElement() {
        if (!currentEditElement) return;
        if (!confirm('Element "' + currentEditElement.name + '" wirklich löschen?')) return;
        var deleteBtn = g('delete-btn');
        hideModalFeedback(); if (deleteBtn) { deleteBtn.disabled = true; deleteBtn.innerText = 'Lösche...'; }
        try {
            await apiRequest('DELETE', '/elements/' + currentEditElement.id);
            showModalFeedback('Element gelöscht!', true);
            delete allFloorsCache[currentFloorId]; await switchFloor(currentFloorId, true);
            setTimeout(function() { if (deleteBtn) { deleteBtn.disabled = false; deleteBtn.innerText = 'Löschen'; } fm_closeModal(mapId); }, 1500);
        } catch(err) { showModalFeedback('Fehler: ' + err.message, false); if (deleteBtn) { deleteBtn.disabled = false; deleteBtn.innerText = 'Löschen'; } }
    }

    function startReplace() {
        if (!currentEditElement) return;
        if (currentEditElement.type === 'polygon') {
            var actEdit = g('modal-actions-edit'), choice = g('replace-choice');
            if (actEdit) actEdit.style.display = 'none';
            if (choice) choice.style.display = 'block';
        } else {
            doReplace('redraw');
        }
    }

    function cancelReplace() {
        var actEdit = g('modal-actions-edit'), choice = g('replace-choice');
        if (actEdit) actEdit.style.display = 'flex';
        if (choice) choice.style.display = 'none';
    }

    function doReplace(mode) {
        if (!currentEditElement) return;
        isReplaceMode = true;
        var modal = g('editor-modal'); if (modal) modal.style.display = 'none';
        var cachedFloor = allFloorsCache[currentFloorId];
        if (cachedFloor) drawElements(cachedFloor.elements.filter(function(el) { return el.id !== currentEditElement.id; }));
        
        if (mode === 'move') {
            collectedPoints = JSON.parse(JSON.stringify(currentEditElement.type === 'polygon' ? currentEditElement.coords : [currentEditElement.coords]));
            startRecording();
            updatePreview();
        } else {
            collectedPoints = [];
            startRecording();
            updatePreview(); // Zeigt 0 Punkte an
        }
    }

    // Globale Funktionen für onclick-Handler im generierten HTML
    window['fm_saveElement_' + mapId]  = saveElement;
    window['fm_updateElement_' + mapId] = updateElement;
    window['fm_deleteElement_' + mapId] = deleteElement;
    window['fm_startReplace_' + mapId]  = startReplace;
    window['fm_doReplace_' + mapId]     = doReplace;
    window['fm_cancelReplace_' + mapId] = cancelReplace;

    // Buttons verdrahten
    var saveBtn = g('save-btn');     if (saveBtn)   saveBtn.addEventListener('click',   saveElement);
    var updateBtn = g('update-btn'); if (updateBtn) updateBtn.addEventListener('click', updateElement);
    var deleteBtn = g('delete-btn'); if (deleteBtn) deleteBtn.addEventListener('click', deleteElement);
    var replaceBtn = g('replace-btn'); if (replaceBtn) replaceBtn.addEventListener('click', startReplace);

    // ===== ICON BROWSER =====
    async function openIconBrowser() {
        var modal = g('icon-browser'), list = g('icon-browser-list');
        if (!modal || !list) return;
        modal.style.display = 'flex'; list.innerHTML = 'Lade Icons...';
        try {
            var opts = { method: 'GET', headers: {} };
            if (typeof WPFloormap !== 'undefined' && WPFloormap.nonce) opts.headers['X-WP-Nonce'] = WPFloormap.nonce;
            var res = await fetch(API_BASE + '/icons', opts);
            var data = await res.json();
            if (data.success && data.icons.length > 0) {
                list.innerHTML = data.icons.map(function(icon) {
                    return '<div onclick="fm_selectIcon(\'' + mapId + '\',\'' + icon.url + '\')" style="cursor:pointer;padding:10px;border:2px solid var(--border-color);border-radius:8px;display:flex;flex-direction:column;align-items:center;gap:6px;background:var(--ui-bg);" onmouseover="this.style.borderColor=\'var(--brand-color)\'" onmouseout="this.style.borderColor=\'var(--border-color)\'">'
                        + '<img src="' + icon.url + '" style="width:40px;height:40px;object-fit:contain;">'
                        + '<span style="font-size:9px;text-align:center;word-break:break-all;opacity:0.7;">' + icon.filename + '</span>'
                        + '</div>';
                }).join('');
            } else {
                list.innerHTML = '<div style="opacity:0.6;grid-column:1/-1;text-align:center;padding:20px;">Noch keine Icons hochgeladen</div>';
            }
        } catch(e) { list.innerHTML = '<div style="color:#ef4444;grid-column:1/-1;text-align:center;padding:20px;">Fehler beim Laden</div>'; }
    }

    window['fm_openIconBrowser'] = window['fm_openIconBrowser'] || {};
    window['fm_openIconBrowser_' + mapId] = openIconBrowser;
    window['fm_selectIcon_' + mapId] = function(iconUrl) {
        selectedIconPath = iconUrl;
        // Beim Icon-Picken automatisch auf Darstellung "Icon" umschalten
        currentRepresentation = 'icon';
        fm_switchRepUI(mapId, 'icon');
        // Anzeige-Modus auf "icon" setzen, falls vorhanden
        var dmEl = g('el-display-mode');
        if (dmEl) dmEl.value = 'icon';
        fm_showIconPreview(mapId, iconUrl);
        fm_closeIconBrowser(mapId);
    };
    // Icon-Auswahl zurücksetzen (auch selectedIconPath leeren)
    window['fm_clearIcon_' + mapId] = function() {
        selectedIconPath = null;
        fm_clearIcon(mapId);
    };
    window['fm_switchRep_' + mapId] = function(type) {
        currentRepresentation = type;
        fm_switchRepUI(mapId, type);
    };
    window['fm_pickGlobalColor_' + mapId] = function(colorId, hex) {
        var colorInput = g('el-color'), hexInput = g('el-color-hex');
        if (colorInput) { colorInput.value = hex; colorInput.dataset.colorId = colorId; }
        if (hexInput) hexInput.value = hex;
        var palette = g('color-palette');
        if (palette) { palette.querySelectorAll('button').forEach(function(btn) { btn.style.borderColor = btn.dataset.colorId === colorId ? 'var(--text-main)' : 'transparent'; }); }
    };

    // ===== STARTEN =====
    renderFloorButtons();
    preloadAllFloors().then(async function() {
        // Suchparameter aus URL oder Attributen
        var initialSearchTerm = INITIAL_FIND ? INITIAL_FIND.trim() : '';
        
        // Falls kein INITIAL_FIND via Parameter kam, prüfen wir die URL ?find=...
        if (!initialSearchTerm) {
            var urlParams = new URLSearchParams(window.location.search);
            initialSearchTerm = (urlParams.get('find') || '').trim();
            if (initialSearchTerm) {
                var searchInp = g('search');
                if (searchInp) searchInp.value = initialSearchTerm;
                var clearB = g('clear-btn');
                if (clearB) clearB.style.display = 'flex';
            }
        }

        var initialFloorId = APP_CONFIG.defaultFloorId;
        var uniqueResult = null;
        if (initialSearchTerm) {
            var allResults = [];
            APP_CONFIG.floors.forEach(function(floor) {
                var data = allFloorsCache[floor.id]; if (!data) return;
                data.elements.filter(function(el) { return el.interactive !== 0 && (el.name.toLowerCase().includes(initialSearchTerm.toLowerCase()) || (el.description || '').toLowerCase().includes(initialSearchTerm.toLowerCase())); })
                    .forEach(function(el) { allResults.push({ floorId: floor.id, element: el }); });
            });
            if (allResults.length === 1) { uniqueResult = allResults[0]; initialFloorId = uniqueResult.floorId; }
        }
        await switchFloor(initialFloorId);
        if (uniqueResult) {
            setTimeout(function() { navigateToElement(uniqueResult.floorId, uniqueResult.element.id, true); }, 100);
        } else if (initialSearchTerm) {
            updateSearchResults(initialSearchTerm); // Liste initial bei Suche öffnen
        }
    });
}

// ===== GLOBALE HILFSFUNKTIONEN FÜR ONCLICK-HANDLER =====

function fm_closeModal(mapId) {
    var modal = document.getElementById(mapId + '-editor-modal');
    if (modal) modal.style.display = 'none';
}

function fm_switchRepUI(mapId, type) {
    var colorSection = document.getElementById(mapId + '-color-section');
    var iconSection  = document.getElementById(mapId + '-icon-section');
    var colorBtn     = document.getElementById(mapId + '-rep-color-btn');
    var iconBtn      = document.getElementById(mapId + '-rep-icon-btn');
    var dmSection    = document.getElementById(mapId + '-display-mode-section');

    if (type === 'color') {
        if (colorSection) colorSection.style.display = 'block';
        if (iconSection)  iconSection.style.display  = 'none';
        if (colorBtn) { colorBtn.style.borderColor = 'var(--brand-color)'; colorBtn.style.background = 'rgba(188,0,9,0.1)'; }
        if (iconBtn)  { iconBtn.style.borderColor  = 'var(--border-color)'; iconBtn.style.background  = 'var(--ui-bg)'; }
        if (dmSection) dmSection.style.display = 'none';
    } else {
        if (colorSection) colorSection.style.display = 'none';
        if (iconSection)  iconSection.style.display  = 'block';
        if (iconBtn)  { iconBtn.style.borderColor  = 'var(--brand-color)'; iconBtn.style.background  = 'rgba(188,0,9,0.1)'; }
        if (colorBtn) { colorBtn.style.borderColor = 'var(--border-color)'; colorBtn.style.background = 'var(--ui-bg)'; }
        if (dmSection) dmSection.style.display = 'block';
    }
}

function fm_pickGlobalColor(mapId, colorId, hex) {
    if (typeof window['fm_pickGlobalColor_' + mapId] === 'function') {
        window['fm_pickGlobalColor_' + mapId](colorId, hex);
    }
}
function fm_switchRep(mapId, type) {
    if (typeof window['fm_switchRep_' + mapId] === 'function') {
        window['fm_switchRep_' + mapId](type);
    } else {
        fm_switchRepUI(mapId, type);
    }
}
function fm_showIconPreview(mapId, iconPath) {
    var preview = document.getElementById(mapId + '-icon-preview');
    var img     = document.getElementById(mapId + '-icon-preview-img');
    var name    = document.getElementById(mapId + '-icon-preview-name');
    if (img)  img.src = iconPath;
    if (name) name.innerText = iconPath.split('/').pop();
    if (preview) preview.style.display = 'flex';
}

function fm_clearIcon(mapId) {
    if (typeof window['fm_clearIcon_' + mapId] === 'function') {
        window['fm_clearIcon_' + mapId]();
        return;
    }
    var preview = document.getElementById(mapId + '-icon-preview');
    if (preview) preview.style.display = 'none';
}

function fm_selectIcon(mapId, iconUrl) {
    if (typeof window['fm_selectIcon_' + mapId] === 'function') {
        window['fm_selectIcon_' + mapId](iconUrl);
    } else {
        fm_showIconPreview(mapId, iconUrl);
        fm_closeIconBrowser(mapId);
    }
}

function fm_closeIconBrowser(mapId) {
    var modal = document.getElementById(mapId + '-icon-browser');
    if (modal) modal.style.display = 'none';
}

function fm_openIconBrowser(mapId) {
    if (typeof window['fm_openIconBrowser_' + mapId] === 'function') {
        window['fm_openIconBrowser_' + mapId]();
    }
}
function fm_saveElement(mapId) {
    if (typeof window['fm_saveElement_' + mapId] === 'function') {
        window['fm_saveElement_' + mapId]();
    }
}
function fm_updateElement(mapId) {
    if (typeof window['fm_updateElement_' + mapId] === 'function') {
        window['fm_updateElement_' + mapId]();
    }
}
function fm_deleteElement(mapId) {
    if (typeof window['fm_deleteElement_' + mapId] === 'function') {
        window['fm_deleteElement_' + mapId]();
    }
}
function fm_startReplace(mapId) {
    if (typeof window['fm_startReplace_' + mapId] === 'function') {
        window['fm_startReplace_' + mapId]();
    }
}

function fm_doReplace(mapId, mode) {
    if (typeof window['fm_doReplace_' + mapId] === 'function') {
        window['fm_doReplace_' + mapId](mode);
    }
}

function fm_cancelReplace(mapId) {
    if (typeof window['fm_cancelReplace_' + mapId] === 'function') {
        window['fm_cancelReplace_' + mapId]();
    }
}
