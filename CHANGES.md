# Changelog

All notable changes to this plugin are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and versions follow
[Semantic Versioning](https://semver.org/).

## [Unreleased]

### Changed

- The Human2Human page shows the connection as three steps (Connect, Finish
  setup, Ready for teachers) in a branded, width-limited layout rendered from a
  Mustache template, with related pages beside it.
- Installing or upgrading the plugin no longer stops on a "New settings" page to
  review the Registration URL. It is no longer an admin setting: Connect uses
  the hosted Human2Human unless an address is set under the collapsed
  "Advanced: connect to a different Human2Human" section of the Human2Human
  page, offered before connecting. Only an override is stored, and clearing it
  returns to the hosted service. `admin/cli/cfg.php` still sets it. The address
  ignores surrounding whitespace and explains what a valid one looks like.
- The Human2Human page names the registration address only when it is not the
  hosted service.

### Removed

- The hidden Human2Human settings page and its link from the Human2Human page.

## [0.2.0]

### Added

- Connect Human2Human: one action that starts LTI 1.3 Dynamic Registration
  through Moodle core, so no URLs or identifiers are copied between the two
  sites.
- Finish setup: activates the registered tool and adds it to the activity
  chooser, where a dynamically registered tool does not appear on its own. Also
  turns on activity selection (Deep Linking) and grade sync, launches in a new
  window, and sends each participant's name but not their email address.
- Connection status on the Human2Human page, and a Registration URL setting for
  connecting to a development or staging Human2Human.

### Changed

- The plugin is beta rather than alpha, and the admin page no longer carries an
  alpha warning.
- The Human2Human page and its settings require "Configure site"
  (`moodle/site:config`). The `tool/human2human:configure` capability is gone:
  only holders of `moodle/site:config` could ever reach the page, and core's
  Dynamic Registration requires it too, so the Connect action dead-ended for
  anyone else.
- The Registration URL page is hidden, leaving one Human2Human entry under Admin
  tools. It is still reachable from the Human2Human page and settable with
  `admin/cli/cfg.php`.
- The admin page introduction describes what the page does instead of repeating
  the pricing, and the README documents installing and connecting.

### Fixed

- The Human2Human page reports it when the External tool activity (`mod_lti`) is
  disabled, rather than offering actions that cannot work.

## [0.1.0]

### Added

- Admin tool scaffold: Human2Human page under Site administration > Plugins >
  Admin tools, the `tool/human2human:configure` capability, and the privacy
  provider.
- CI with moodle-plugin-ci on Moodle 4.5 to 5.3, PostgreSQL, MySQL and MariaDB.
