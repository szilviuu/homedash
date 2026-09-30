# Homelab Dashboard

> [!NOTE]
> **Project Status:** This dashboard is feature-complete for my setup, and I won't be actively developing or maintaining new versions. Feel free to fork it and make it your own!

A lightweight, self-hosted start page for a homelab. It is a single PHP
application with vanilla JavaScript and CSS: no framework, database, package
manager, build step, or external API key is required.

The dashboard provides quick links, live weather, host reachability, local and
remote system metrics, configurable backgrounds, and an in-browser manager.
All dashboard settings are stored in a JSON file, making the application easy
to copy, back up, and run from a standard PHP web root.

## Features

- Responsive glass-style dashboard with configurable accent color, text color,
  font, and text scale.
- Live clock, date, and time-of-day greeting.
- Current weather and seven-day forecast from
  [Open-Meteo](https://open-meteo.com/), without an API key.
- Searchable quick links, organized into editable categories.
- Drag-and-drop link ordering.
- TCP host reachability checks with latency measurements.
- Optional remote host CPU and disk metrics.
- Up to ten remote mount paths per host, each showing free space, total
  capacity, and a usage bar directly in the host card.
- Local server CPU load, memory, disk, and uptime statistics.
- Image, GIF, SVG, and video backgrounds with upload, selection, random
  startup, timed rotation, dimming, looping, and mute options.
- In-browser manager for dashboard settings, links, hosts, assets, and the
  admin password.

## Requirements

- PHP 8.1 or newer.
- Apache or Nginx with PHP enabled.
- PHP sessions enabled.
- The PHP/web-server user must be able to write to:
  - `data/`
  - `backgrounds/`
  - `assets/icons/`

The dashboard targets Linux hosts for local and remote system statistics.
CPU, memory, and uptime data are read from `/proc`; unavailable values are
omitted gracefully.

## Installation

1. Copy the project into a web-accessible directory:

   ```bash
   sudo mkdir -p /var/www/html/homedash
   sudo cp -a . /var/www/html/homedash
   ```

2. Grant the web server write access to dashboard data and uploads:

   ```bash
   sudo chown -R www-data:www-data \
     /var/www/html/homedash/data \
     /var/www/html/homedash/backgrounds \
     /var/www/html/homedash/assets/icons

   sudo chmod -R 775 \
     /var/www/html/homedash/data \
     /var/www/html/homedash/backgrounds \
     /var/www/html/homedash/assets/icons
   ```

3. Open the dashboard in a browser.

4. Select the gear icon in the bottom-right corner and sign in with the
   initial password:

   ```text
   homelab
   ```

5. Change the password immediately from **Manager -> Security**.

## Web server notes

### Apache

`data/.htaccess` contains:

```apacheconf
Require all denied
```

Ensure Apache allows that directory-level configuration (`AllowOverride`) or
add an equivalent rule to the virtual host.

### Nginx

Nginx does not read `.htaccess` files. Add an explicit rule to deny direct
access to persisted dashboard data:

```nginx
location ^~ /data/ {
    deny all;
}
```

Use HTTPS whenever the dashboard is reachable outside a trusted network.

## Using the manager

Open the gear icon to access the manager after authenticating.

| Tab | What it manages |
| --- | --- |
| **General** | Display name, accent and text colors, font, text scale, weather location and units, host refresh interval |
| **Links** | Categories, links, URLs, icons, descriptions, and drag ordering |
| **Hosts** | Host address, TCP port, optional link, remote metrics endpoint, and remote mount paths |
| **Assets** | Background and icon uploads, active background, rotation, video playback, and background dimming |
| **Security** | Admin password |

### Links

Links are organized by category. Categories can be renamed or removed, and
links can be dragged by their handle to change display order. Every link can
have a name, URL, icon path, and optional description.

The dashboard automatically opens link destinations in a new tab with
`noopener` protection.

### Backgrounds and icons

Upload backgrounds or icons from **Manager -> Assets**.

Supported background formats:

- `jpg`, `jpeg`, `png`, `gif`, `webp`, `svg`
- `mp4`, `webm`

Supported icon formats:

- `jpg`, `jpeg`, `png`, `gif`, `webp`, `svg`, `ico`

For background files, you can:

- Select the active background by clicking its card.
- Choose a random background at dashboard startup.
- Rotate through backgrounds at a configured interval.
- Control video looping and muting.
- Adjust the readability overlay with background dimming.

## Host monitoring

Each configured host is tested by the dashboard server with a TCP connection
to its configured address and port. The default timeout is 1.5 seconds.

The configured **Status interval** controls how often the browser refreshes
host state. The default is 10 seconds and the minimum is 5 seconds.

Host status cards show:

- Online or offline state.
- TCP connection latency for online hosts.
- Optional remote CPU and root-disk utilization.
- Optional capacity details for configured remote mount paths.

## Remote metrics and mount points

The dashboard cannot inspect filesystems that only exist on another server.
To report remote host storage, deploy the included `metrics.php` file to the
remote PHP host.

### Setup

1. Copy `metrics.php` to the remote server, for example:

   ```text
   https://server.example.internal/metrics.php
   ```

2. In **Manager -> Hosts**, configure that host's **Metrics URL** with the
   remote endpoint URL.

3. Optionally add remote mount paths in the multi-line mount-path field:

   ```text
   /
   /mnt/media
   /mnt/backups
   ```

   Enter one path per line. A host supports up to ten unique paths.

4. Save the host configuration.

The dashboard queries the endpoint once for each configured path. The path is
evaluated on the **remote host**, never on the dashboard host. Each returned
mount is shown in the corresponding host card with free capacity, total
capacity, and a usage bar.

### `metrics.php` response

The bundled endpoint returns JSON in this shape:

```json
{
  "cpu": 12.5,
  "disk_path": "/mnt/media",
  "disk": 41.2,
  "disk_free_gb": 548.7,
  "disk_total_gb": 932.5,
  "disk_percent": 41.2
}
```

The dashboard calls the endpoint with a `path` query parameter for each
configured mount path. Without that parameter, the remote endpoint reports
the root filesystem (`/`).

> [!IMPORTANT]
> Restrict access to `metrics.php` to your trusted network, reverse proxy, or
> dashboard host. Its `path` parameter lets a caller request filesystem
> capacity information for paths readable by the PHP process.

## Weather

Weather uses Open-Meteo's geocoding and forecast APIs. The selected location,
coordinates, and temperature unit are stored in `data/config.json`.

Condition icons distinguish clear, mostly clear, partly cloudy, and overcast
states. Select the weather card to open the seven-day forecast.

Internet access from the browser is required for weather data. If the API is
unavailable, the dashboard displays a fallback message without affecting the
rest of the page.

## Configuration and backup

The primary configuration file is:

```text
data/config.json
```

It contains theme preferences, weather settings, links, categories, hosts,
background settings, and local disk paths. Changes made through the manager
are saved automatically.

The administrator password hash is stored separately in:

```text
data/auth.json
```

To back up or move an installation, preserve:

- `data/config.json`
- `data/auth.json`
- `backgrounds/`
- `assets/icons/`

Copying the complete project directory is the simplest migration method.

## Security considerations

- Change the default `homelab` password immediately.
- Run the dashboard behind HTTPS when it is accessible beyond a private
  network.
- Protect `data/` from direct web access. Apache is covered by the included
  `.htaccess`; Nginx requires its own deny rule.
- Keep the manager password private. An authenticated manager can change
  links, hosts, uploaded assets, and dashboard settings.
- Host status checks make TCP connections to the hosts and ports that you
  configure. Only add targets you intend the dashboard server to reach.
- Remote metrics endpoints should be available only to trusted clients.
- Consider an additional authentication layer, such as a reverse proxy with
  basic authentication, an identity-aware proxy, or a VPN, when exposing the
  dashboard remotely.

## Project structure

```text
.
├── index.php                         # Dashboard page shell
├── metrics.php                       # Deployable remote metrics endpoint
├── api/
│   ├── auth.php                      # Login, session status, password changes
│   ├── assets.php                    # Asset listing and uploads
│   ├── common.php                    # Sessions, config I/O, response helpers
│   ├── config.php                    # Configuration read/write endpoint
│   ├── stats.php                     # Local server statistics endpoint
│   └── status.php                    # Host and remote metrics checks
├── assets/
│   ├── css/
│   │   ├── background-overlay.css
│   │   ├── dashboard.css
│   │   ├── manager-editor.css
│   │   ├── typography.css
│   │   └── weather-icons.css
│   ├── icons/                        # Built-in and uploaded link icons
│   └── js/dashboard.js               # Dashboard and manager behavior
├── backgrounds/                      # Built-in and uploaded backgrounds
└── data/
    ├── .htaccess                     # Apache access protection
    └── config.json                   # Persisted dashboard configuration
```

## API endpoints

All endpoints return JSON.

| Endpoint | Method | Authentication | Purpose |
| --- | --- | --- | --- |
| `api/config.php` | `GET` | No | Read dashboard configuration |
| `api/config.php` | `POST` | Yes | Save dashboard configuration |
| `api/auth.php?action=status` | `GET` | No | Check manager session state |
| `api/auth.php?action=login` | `POST` | No | Start a manager session |
| `api/auth.php?action=password` | `POST` | Yes | Change the manager password |
| `api/assets.php` | `GET` | No | List available backgrounds and icons |
| `api/assets.php` | `POST` | Yes | Upload a background or icon |
| `api/status.php` | `GET` | No | Check configured hosts and remote metrics |
| `api/stats.php` | `GET` | No | Read local dashboard-server metrics |

The public read endpoints are required for an unauthenticated dashboard to
render. Put the entire dashboard behind network access controls if exposing
host names, URLs, or status data is not appropriate for your environment.

## License

This project is MIT-licensed. 
