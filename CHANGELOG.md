# MoodleBox Moodle plugin Release Notes

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](https://semver.org/) and follow principles of [keep a changelog](https://keepachangelog.com).

## Version 2.17.4, 2024-01-02

### Changed
- Remove deprecated strings (issue #129).
- Update several strings (no issue number, commits 131fa61, a778c32 and 5f0112a).

### Fixed
- Fix deprecation warning in function `get_connected_ip_adresses()` (issue #130).

## Version 2.17.3, 2023-12-24

### Changed
- New code for displaying the wireless clients info, which is now immediately updated upon client connexion; the info displayed is now the IP address and MAC address of the client, and the client name isn't displayed anymore (issue #128).

## Version 2.17.2, 2023-11-28

### Add
- New code added to workaround a bug with wifi protocol on RPi3B (issue https://github.com/moodlebox/moodlebox/issues/319).

### Fixed
- Rename wrong filename in script and add routine to fix the buggy filename on devices (issue #127).

## Version 2.17.1, 2023-10-05

### Fixed
- Fix bad branch name in supported version (issue #125).

## Version 2.17.0, 2023-10-01

### Add
- Add support of NetworkManager for upcoming Bookworm-based RPi OS version (PR #123).
- Add detection of just announced new Raspberry Pi 5 (issue #122).

### Changed
- Tested against upcoming Moodle 4.3 (issue #124).
- Better implementation of MoodleBox settings at top level of administration navigation (see #115, commit b4c94dc).

### Fixed
- Fix completely script to work with released versions of MoodleBox image (issue #114).
- Prevent more PHPDocs errors (no issue number, commits dd38ff0, 9915274 and 04aaf38).

## Version 2.16.3, 2023-09-17

### Fixed
- Fix script to work with released versions of MoodleBox image (issue #114).
- Multiple PHPDocs warnings for deprecated syntax (issue #121).

## Version 2.16.2, 2023-09-16

### Fixed
- CPU load formatting problem in MoodleBox dashboard (issue #119).
- Multiple issues with PHPDocs in plugin validation (issue #120).

## Version 2.16.1, 2023-08-24

### Changed
- Update routine to check registration country (no issue number, commits  (no issue number, commit bdb9ab7 and ae25ffa).
- Add function to get MoodleBox version and date (no issue number, commit 3ee5edd).
- Update dependencies (no issue number, commit 496a1f4).

### Fixed
- Workaround potential execution permission (issue #118).
- Fix coding style issues (no issue number, commit 1d33e7a).

## Version 2.16.0, 2023-07-30

### Add
- Enable displaying date and time buttons in the footer of any page for users with adequate role (issue #94).

### Changed
- Move MoodleBox settings to top level of administration navigation (issue #115).
- Adapt wireless edit script for MoodleBox image change (issue #114).

### Fixed
- Change `dnsmasq` config file name (issue #114).
- Fix deprecation error when using PHP8.2 (issue #116).
- Behat tests corrected (no issue number).

## Version 2.15.0, 2023-05-04

### Add
- Detect CM4S model (issue #112).

### Changed
- Tested against upcoming Moodle 4.2 (issue #113).

### Fixed
- Fix regression introduced when fixing #73 (issue #110).

## Version 2.14.2, 2022-11-20

### Fixed
- Fix regression introduced when fixing #73 (issue #110).

## Version 2.14.1, 2022-11-19

### Add
- Add better graphical separation of footer buttons (no issue number, commit 21da4fadf108b76e467690ac06d5c2db6c8c01a1).

### Changed
- Tested against upcoming Moodle 4.1. (issue #109).

### Fixed
- Fix several strings (no issue number, commits 1057305479a9778a4d6f11c9dbcf83d9e8c945c0, b1c8ae4c53a89e9a5bcb6a1673d8f4c9104bb598).
- Fix comments (no issue number, commits 08af42ecbe7d386de45548c8e32050693e5cfd89, 61c09dc0cc7a6090475fe9e812e0ee129dc310d8).

## Version 2.14.0, unreleased

## Version 2.13.1, 2022-08-04

### Changed
- Remote deprecated strings (issue #106).
- Update code to up-to-date Moodle coding style (issue #107).

### Fixed
- Update strings with up-to-date info about RPi power supplies (isse #105).

## Version 2.13.0, 2022-08-02

### Changed
- Make the footer buttons visible again (issue #103).

## Version 2.12.0, 2022-04-23

### Fixed
- Fix multiple warnings when running on incompatible hardware (no issue number, commits f028c19 and 25feec4).

### Changed
- Tested against upcoming Moodle 4.0 (issue #91).

## Version 2.11.1, 2021-12-01

### Fixed
- Fix `changepassword.sh` script, which was buggy (issue #102).

## Version 2.11.0, 2021-11-08

### Added
- Add detection of Raspberry Pi Zero 2 W (issue #99).

### Changed
- Update installation instructions (no issue number, commits 2ebaa0f, ec09ab7).
- Update date/time command for robustness (no issue number, commit a1b8fc5).

### Fixed
- Optimize admin tree loading (no issue number, commit 8f524c6).
- Remove French language file (issue #100).

## Version 2.10.0, 2021-07-27

### Added
- Add workflow to release plugin in the Moodle's plugins directory (no issue number, see commits f3d4b2 and ec444a8).
- New setting allowing to set the fixed IP address and DHCP range of the Wi-Fi network (issue #96).

### Changed
- Test plugin against Moodle 3.11 (issue #95).
- Empty lease file when updating WiFi settings (issue #98).

## Version 2.9.0, 2021-02-21

### Added
- Detection of Raspberry Pi 400, preliminary tentative (issue #93).

### Changed
- Travis CI dropped in favor of Github actions (no issue number, see commits 602560e, a300850, da73b9c, f0fcdd2, f3c3fda, 596adc8 and 123e7f7).

## Version 2.8.0, 2020-11-03

### Changed
- Tested against upcoming Moodle 3.10 (issue #91).
- Detection of Raspberry Pi CM4 module (issue #90).
- Travis configuration updated (no issue number).

### Fixed
- PHP warning raised when no dnsmasq lease yet (issue #92).

## Version 2.7.0, 2020-07-23

### Changed
- New Raspberry Pi OS name adopted (issue #88).
- Travis configuration updated (no issue number).

### Fixed
- Raspberry Pi 4B+ model with 8GB detection works now correctly (issue #89).
- Typo in string (no issue number)

## Version 2.6.0, 2020-06-08

### Added
- Raspberry Pi 4B+ model with 8GB supported (issue #87).
- Tested for upcoming Moodle version 3.9 (no issue number).

### Changed
- Different messages are displayed depending on undervoltage situation (issue #86).

### Fixed
- Coding style adapted to new standard (no issue number).

## Version 2.5.0, 2020-02-08

### Added
- Dashboard displays MoodleBox ethernet IP and default gateway addresses (issue #82).
- Dashboard displays Moodle version (issue #81).
- Moodle compatibility info added (issue #83).

### Changed
- Refactor of the dashboard information section (issue #84).

## Version 2.4.2, 2019-10-07

### Added
- Wi-Fi password and SSID validation with meaningful message when invalid input, for better UX (issue #79).

### Fixed
- Moodle forms do not works correctly (issue #78).
- Wi-Fi password validation to lax ( related to issue #77).

## Version 2.4.1, 2019-10-05

### Fixed
- Wi-Fi password with uppercase characters were rejected (issue #77).

## Version 2.4.0, 2019-08-17

### Added
- Support of [PiJuice HAT](https://uk.pi-supply.com/products/pijuice-standard) (issue #75). This feature was contributed by Vincent Widmer (@smallhacks).

### Fixed
- Some fixes for CI support (no issue number).

## Version 2.3.0, 2019-07-15

### Added
- Raspberry Pi 3A+ is now supported (#73). This enhancement was possible thanks to the sponsoring of Visvanath Ratnaweera (@ratnavis), who kindly offered a Raspberry Pi 3A+.

### Fixed
- Lang strings brushed up (#74).

## Version 2.2.1, 2019-07-13
### Fixed
- Raspberry Pi 4B RAM size is displayed with wrong unit (#72).

## Version 2.2.0, 2019-06-10
### Added
- Raspberry Pi 4B and other models are now supported (#70).

## Version 2.1.0, 2019-05-29
### Added
- Manager can now use restart and shutdown buttons in the footer (#68).

## Version 2.0.1, 2019-05-19
### Added
- Plugin is compatible with Moodle 3.7.0 (issue #67).

### Fixed
- Fix warning in PHP7.2 and later (issue #67).

## Version 2.0.0, 2019-04-19
### Added
- Restart and shutdown buttons can be displayed to the administrator on the footer of any Moodle page (issue #33). Warm thanks to [Adrian Perez Rodriguez](https://github.com/adpe) for most of the implementation.
- A warning is displayed to the administrator on any Moodle page if under voltage is detected (issue #65).
- Links to documentation website and support forum are displayed on the new MoodleBox settings page (issue #54).

### Changed
- Moodle 3.6.0 or later is now required (due to implementation of #54).
- Configuration and dashboard pages were refactored (part of issue #33).
- Collapsed status of collapsible regions is now saved between sessions (issue #60).

### Fixed
- Bad permissions on shell script (issue #61).
- Typos fixed in lang strings (no issue number).

## Version 1.12.2, 2018-12-01
### Added
- Each section is now collapsible (issue #55)
- New feature allowing to hide Wi-Fi SSID (issue #56)

### Changed
- Supports Moodle 3.6 (issue #58)

### Fixed
- Type mismatch PHP warnings fixed (no issue number)

## Version 1.12.1, 2018-08-28
### Fixed
- Error in help string fixed (issue #53).

## Version 1.12.0, 2018-08-16
### Added
- Wi-Fi regulatory country can now be set in the GUI (issue #48).
- Section to resize SD card doesn't display when partition already resized (issue #50).
- Help icons and texts added to enhance usability (issue #51).
- Function get_wireless_interface_name() added (part of issue #32).

### Changed
- Wi-Fi default channel changed to 11 (part of issue #48).
- Form definitions moved to separate file (issue #52).

### Fixed
- Variable name fixed (part of issue #32).

## Version 1.11.0, 2018-07-09
### Added
- New feature to resize SD card (issue #45). Idea by Ralf Krause.

### Fixed
- Wrong display of table row background (issue #47).

## Version 1.10.4, 2018-06-12
### Added
- Correctly detect Raspberry Pi Zero W (issue #44).

### Changed
- Better Raspberry Pi model detection (#43).

## Version 1.10.3, 2018-05-26
### Fixed
- Error in privacy provider prevents privacy registry display (#42).

## Version 1.10.2, 2018-04-29
### Fixed
- Impossible to enter SSIDs with less than 8 bytes (#41).

## Version 1.10.1, 2018-03-23
### Fixed
- Lang string with unsupported char breaks plugin when debugging level set to "Developer" (issue #40).

## Version 1.10, 2018-03-21
### Added
- Moodle privacy API implementation complete (issue #34).
- Arbitrary characters, included spaces, are now allowed in SSID (issues #31 and #38).
- Support for Raspberry 3 B+ (issue #39).

### Changed
- Requires now Moodle 3.3 or later.
- Hidden files used as trigger for plugin actions are now bundled (#37).

### Fixed
- Nothing yet.

## Version 1.9, 2018-02-18
### Added
- Moodle privacy API implementation (issue #34).

### Changed
- Now uses classes and auto-loading instead of local library (issue #35)

### Fixed
- Better parsing of version numbers (issue #36)

## Version 1.8, 2017-11-01
### Added
- Display of MoodleBox image version (issue #28).
- Compatible with Moodle 3.4 (issue #27).

### Fixed
- Now works even in unusual Moodle installation directory (issue #29).
- Wi-Fi invalid SSIDs aren't allowed anymore (issue #30).
- Prevent potential bugs due to not validated settings (issue #30).

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
- Changelog adapted to principles of [keep a changelog](https://keepachangelog.com).

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
