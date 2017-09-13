# MoodleBox Moodle plugin Release Notes

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/) and follow principles of [keep a changelog](http://keepachangelog.com).

## Version 1.7.1, 2017-09-13
### Fixed
- Re-added removed strings to fix issue #25.

## Version 1.7, 2017-09-11
### Added
- Possibility to have an open (i.e. without password) Wi-Fi network (issue #22). Feature dedicated to @christian-westphal.
- Wi-Fi channel selection depends on regulatory domain defined in `/etc/hostapd/hostapd` (issue #23)

### Changed
- Wi-Fi settings display refactored (issue #20).
- Better display the DHCP clients (issue #24).

### Fixed
- Required Moodle version number updated (issue #21).
- Change way to parse config files, to avoid potential bugs (issue #19).

## Version 1.6, 2017-09-05
### Added
- Wi-Fi SSID can now be changed.
- Wi-Fi channel can now be changed.

### Changed
- Changelog adapted to principles of http://keepachangelog.com.

### Fixed
- Change moodle installation directory, to cope with last version of MoodleBox.

## Version 1.5.4, 2017-06-23
### Changed
- Hardware test refactored
- Continuous integration via Travis added
- Scripts updated

## Version 1.5.3, 2017-04-29
### Added
- Hardware specification added, for RPi3 support (new kernel)

## Version 1.5.2, 2017-04-29
### Added
- Some tests added to fix issue #16 

### Removed
- Language files removed, fixing issue #15

## Version 1.5.1, 2017-04-21
### Changed
- Some small documentation enhancements (mainly release notes)

### Added
- Several language files added (de, es, es_mx)
- Missing string added

### Fixed
- Cosmetic fixes for Moodle code checker

## Version 1.5, 2017-04-14
### Changed
- Code doesn't use eval() any more, issue #12
- Javascript code refactored, using AMD, issue #11
- Documentation updated (release notes have their own file now), issue #13

### Added
- Plugin is now published in the [Moodle plugin database](https://moodle.org/plugins/tool_moodlebox)

## Version 1.4.4, 2017-04-11
### Fixed
- PHP warning due to direct inclusion of version.php fixed, issue #10
- Other cosmetic fixes

## Version 1.4.3, 2016-12-06
### Fixed
- Adapted for Moodle 3.2
- Bug fixed for use with Moodle 3.2, issue #9

## Version 1.4.2, 2016-10-08
### Added
- Display of warnings when the plugin installation is not complete, issue #8

## Version 1.4.1, 2016-09-25
### Fixed
- Fix: MoodleBox Wi-Fi network password cannot be changed to empty, issue #7

## Version 1.4, 2016-09-18
### Added
- New option enabling to change the MoodleBox Wi-Fi network password

## Version 1.3, 2016-09-10
### Added
- New option enabling to change the MoodleBox password
- Warnings displayed when plugin isn't used on a Raspberry Pi, issue #6

## Version 1.2, 2016-08-09
### Changed
- Changed to admin tool plugin (from local plugin), issue #3
- Code refactored

## Version 1.1, 2016-08-06
### Added
- Added display of free space on SD card

## Version 1.0, 2016-07-11
### Added
- Added time setting feature

## Version 1.0b (beta), 2016-06-26
### Added
- Added two folder as RAM disks, for better performance

## Version 1.0a2 (alpha), 2016-06-19
### Changed
- Reorganisation of project

## Version 1.0a1 (alpha), 2016-06-16
### Added
- First version
