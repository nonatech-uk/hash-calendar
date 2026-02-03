# GH3 Hash Runs

A custom WordPress plugin for managing Hash House Harriers runs. Built for [Guildford H3](https://guildfordh3.org.uk).

## Features

- **Custom Post Type** (`hash_run`) with hash-specific fields
- **Admin Interface** with organised meta boxes for run data entry
- **Auto-incrementing Run Numbers** with suggested next number
- **Auto-generated Titles** from Hare and Location fields
- **Shortcode** `[gh3_upcoming_runs]` for displaying upcoming runs
- **Click-to-expand** details on upcoming run listings
- **What3Words & Google Maps** integration for locations
- **Non-standard start time alerts** - only displayed when different from the default 19:30
- **GitHub auto-updater** - receives updates directly from GitHub releases

## Custom Fields

| Field | Description |
|-------|-------------|
| Run Number | Sequential run number |
| Run Date | Date of the hash run |
| Start Time | Start time (default 19:30, only displayed if different) |
| Hare(s) | Who is laying the trail |
| Location | Start location description |
| What3Words | What3Words address with link |
| Google Maps URL | Link to Google Maps |
| On Inn | Pub/venue after the run |
| Notes | Additional information |

## Shortcode Usage

```
[gh3_upcoming_runs count="5"]
```

- `count` - Number of upcoming runs to display (default: 5)
- First run is displayed as a featured card with full details
- Subsequent runs shown in a compact list, click to expand details
- Only shows runs with a date >= today, sorted by date ascending

## Installation

1. Upload the `gh3-hash-runs` folder to `/wp-content/plugins/`
2. Activate the plugin through the WordPress Plugins menu
3. Hash Runs will appear in the admin sidebar
4. Add `[gh3_upcoming_runs]` to any page or post

## Updates

The plugin checks GitHub for new releases and updates via the WordPress dashboard. Tag a new release on GitHub to push an update.

## File Structure

```
gh3-hash-runs/
├── gh3-hash-runs.php              # Main plugin file
├── includes/
│   ├── class-gh3-post-type.php    # Custom post type registration
│   ├── class-gh3-admin.php        # Admin meta boxes & list columns
│   ├── class-gh3-shortcode.php    # Frontend shortcode
│   └── class-gh3-updater.php      # GitHub auto-updater
├── assets/
│   ├── css/
│   │   ├── admin.css              # Admin styling
│   │   └── frontend.css           # Frontend display styling
│   └── js/
│       └── frontend.js            # Expand/collapse functionality
├── templates/
│   └── upcoming-runs.php          # Shortcode template
├── LICENSE
└── README.md
```

## License

This plugin is free for non-commercial use. See [LICENSE](LICENSE) for details.
