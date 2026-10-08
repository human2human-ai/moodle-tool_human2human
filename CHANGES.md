# Changelog

All notable changes to this plugin are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and versions follow
[Semantic Versioning](https://semver.org/).

## [Unreleased]

### Changed

- The Registration URL setting ignores whitespace around a pasted address and
  explains what a valid one looks like, instead of "This value is not valid".

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

- The admin page introduction matches the README, and the page shows that the
  plugin is in alpha.

## [0.1.0]

### Added

- Admin tool scaffold: Human2Human page under Site administration > Plugins >
  Admin tools, the `tool/human2human:configure` capability, and the privacy
  provider.
- CI with moodle-plugin-ci on Moodle 4.5 to 5.3, PostgreSQL, MySQL and MariaDB.
