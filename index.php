<?php
?><!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Homelab Dashboard</title><link rel="icon" href="assets/icons/favicon.svg" type="image/svg+xml"><link rel="stylesheet" href="assets/css/dashboard.css"><link rel="stylesheet" href="assets/css/search-results.css"><link rel="stylesheet" href="assets/css/manager-editor.css"><link rel="stylesheet" href="assets/css/weather-icons.css"><link rel="stylesheet" href="assets/css/background-overlay.css"><link rel="stylesheet" href="assets/css/typography.css"></head>
<body>
<div id="bg"></div><video id="bg-video" autoplay loop muted playsinline></video><div id="bg-overlay"></div>
<div class="container">
<header class="header"><div class="greeting" id="greeting">Welcome</div><div class="clock" id="clock">00:00:00</div><div class="date-str" id="date-str"></div>
<div class="search-wrapper"><form class="search-form" id="search-form"><span>⌕</span><input id="search-input" placeholder="Search for a service..." autocomplete="off"></form><div class="search-results" id="search-results"></div></div></header>
<div class="widgets-row"><section class="card weather-card" id="weather-card"><div class="card-title">Weather</div><div id="weather-content">Loading...</div></section><div id="links-container" class="links-grid"></div></div>
<section><div class="section-title">Hosts</div><div id="hosts-container" class="hosts-grid"></div></section>
<section><div class="section-title">This server</div><div id="stats-container" class="stats-grid"></div></section>
<footer class="footer">Homelab Dashboard · refreshed <span id="refreshed-at"></span></footer></div>
<button class="settings-btn" id="settings-btn" title="Manage dashboard">⚙</button>
<div class="overlay" id="manager-overlay"><div class="manager"><div class="manager-head"><h2>Dashboard manager</h2><button class="close" id="manager-close">✕</button></div>
<div id="login-pane"><p class="muted">Enter the admin password.</p><input class="input" id="login-password" type="password" placeholder="Password"><button class="btn" id="login-btn">Unlock</button><div class="error" id="login-error"></div></div>
<div id="manager-pane" hidden>
<div class="tabs"><button class="tab active" data-tab="general">General</button><button class="tab" data-tab="links">Links</button><button class="tab" data-tab="hosts">Hosts</button><button class="tab" data-tab="assets">Assets</button><button class="tab" data-tab="security">Security</button></div>
<div class="tabpane active" id="tab-general"><div class="form-grid"><label>Name<input class="input" id="cfg-name"></label><label>Accent<input class="input color" id="cfg-accent" type="color"></label><label>Text color<input class="input color" id="cfg-text-color" type="color"></label><label>Font<select class="input" id="cfg-font-family"><option value="inter">Modern sans</option><option value="system">System UI</option><option value="serif">Serif</option><option value="mono">Monospace</option></select></label><label>Text size <output id="cfg-font-scale-value">100%</output><input class="input" id="cfg-font-scale" type="range" min="50" max="200" value="100"></label><label>Weather location<input class="input" id="cfg-city"></label><label>Units<select class="input" id="cfg-units"><option value="celsius">Celsius</option><option value="fahrenheit">Fahrenheit</option></select></label><label>Status interval (seconds)<input class="input" id="cfg-interval" type="number" min="5"></label></div><button class="btn" id="save-general">Save general settings</button></div>
<div class="tabpane" id="tab-links"><div class="editor-section"><div class="card-title">Categories</div><div id="categories-editor"></div><button class="btn secondary" id="add-category">+ Add category</button></div><div class="editor-section"><div class="card-title">Links</div><p class="muted editor-help">Drag links by the handle to set their display order.</p><div id="links-editor"></div><button class="btn secondary" id="add-link">+ Add link</button><button class="btn" id="save-links">Save links</button></div></div>
<div class="tabpane" id="tab-hosts"><div id="hosts-editor"></div><button class="btn secondary" id="add-host">+ Add host</button><button class="btn" id="save-hosts">Save hosts</button></div>
<div class="tabpane" id="tab-assets">
<div class="bg-options">
<label><input type="checkbox" id="cfg-bg-random"> Random background on startup</label>
<label>Rotate every <input class="input" id="cfg-bg-rotation" type="number" min="0" value="0"> minutes (0 = off)</label>
<label><input type="checkbox" id="cfg-video-loop"> Loop videos</label>
<label><input type="checkbox" id="cfg-video-muted"> Mute videos</label>
<label>Background dimming <input class="input" id="cfg-bg-overlay" type="range" min="0" max="85" value="40"> <output id="cfg-bg-overlay-value">40%</output></label>
<button class="btn secondary" id="save-bg-options">Save background options</button>
</div>
<div class="upload-row"><select class="input" id="upload-kind"><option value="background">Background</option><option value="icon">Icon</option></select><input class="input" id="upload-file" type="file"><button class="btn" id="upload-btn">Upload</button></div>
<div class="card-title" style="margin-top:18px">Backgrounds &mdash; click to select</div>
<div id="asset-list-bg" class="asset-list"></div>
<div class="card-title" style="margin-top:18px">Icons</div>
<div id="asset-list-icons" class="asset-list"></div>
</div>
<div class="tabpane" id="tab-security"><input class="input" id="new-password" type="password" placeholder="New admin password"><button class="btn" id="password-btn">Change password</button></div>
<div id="manager-message" class="message"></div></div></div></div>
<div class="forecast-overlay" id="forecast-overlay"><div class="forecast-modal"><div class="manager-head"><h2 id="forecast-title">7-day forecast</h2><button class="close" id="forecast-close">✕</button></div><div class="forecast-days" id="forecast-days"></div></div></div>
<script src="assets/js/dashboard.js"></script></body></html>