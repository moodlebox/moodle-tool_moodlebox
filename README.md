# MoodleBox

A project to build a Moodle server and Wi-Fi router on a Raspberry Pi 3.

The project includes a local plugin for Moodle 3.0 or later, provided to help the administrator of the MoodleBox to monitor some hardware settings and allow restart and shutdown of the MoodleBox.

The documentation is included in the plugin `doc` folder, as a LaTeX document (in french; sorry, no english version as of now, pull request highly desirable).

## Availability

The code is available at [https://github.com/martignoni/moodlebox](https://github.com/martignoni/moodlebox).

An [prepared disk image](https://fricloud.ch/index.php/s/bjU65EHe6jdFVT6/download) for your Raspberry Pi 3 is [available](https://fricloud.ch/index.php/s/bjU65EHe6jdFVT6/download).

SHA1 fingerprint of the disk image: becd79e10d48d9d2b8e0d20a1cfc49ab102cabc7

### Release notes

* 2016-06-19, version 1.0a2 (alpha): reorganisation of project
* 2016-06-16, version 1.0a1 (alpha): first version

## Building and installation

To build a MoodleBox from scratch, you need a Raspberri Pi 3 (Wi-Fi!) and follow the [instructions given in the documentation](https://github.com/martignoni/moodlebox/blob/master/doc/Moodlebox.pdf) (in french).

The local plugin needs to be installed in the Moodle tree of the MoodleBox, in the _local_ folder. Once installed, an new option _MoodleBox administration_ will be available in Moodle, under _Site administration > Server_ in the _Administration_ block.

## Features

(Sorry, only in french, pull request needed!)

* Point d'accès sans fil. Le nom du réseau Wi-Fi fourni est _MoodleBox_ ; le mot de passe de connexion est _moodlebox_.
* Plateforme Moodle 3.1.x accessible via Wi-Fi ([http://moodlebox.local/](http://moodlebox.local/)), dans sa configuration de base vierge de toute personnalisation. L'unique compte utilisateur du Moodle est un compte administrateur, nom d'utilisateur : _admin_, mot de passe : _Moodlebox4$_. La plateforme est configurée pour accepter les clients de l'[app mobile officielle](https://download.moodle.org/mobile/) de Moodle. La taille maximale des dépôts de fichiers est fixée à 50 Mo. Le cron est lancé toutes les 3 minutes.
* Lorsqu'une clef USB est insérée dans la MoodleBox, les fichiers qu'elle contient sont accessibles pour les administrateurs et enseignants de la plateforme via le dépôt _Système de fichiers_ du Moodle.
* Possibilité de déposer des fichiers par SFTP directement sur la MoodleBox (nom d'utilisateur : _moodlebox_, mot de passe : _Moodlebox4$_); ces fichiers sont accessibles pour les administrateurs et enseignants de la plateforme via le dépôt _Système de fichiers_ du Moodle.
* Accès à Internet : si la MoodleBox est connectée par câble à un réseau relié à Internet, elle agit comme routeur et les clients Wi-Fi ont accès à Internet.
* [PhpMyAdmin](http://moodlebox.local/phpmyadmin) installé, avec un compte administrateur, nom d'utilisateur : _root_, mot de passe : _Moodlebox4$_.


## Usage of the MoodleBox

See the [user manual](https://moodle.org/mod/book/view.php?id=8265), in french.

## Thanks

* To Daniel Méthot, for the [idea of a MoodleBox](https://moodle.org/mod/forum/discuss.php?d=278493)
* To Christian Westphal, for the [first POC](https://moodle.org/mod/forum/discuss.php?d=331170) of a MoodleBox
* To the [Raspberry Pi Foundation](https://www.raspberrypi.org/), for a splendid small computer
* To [Martin Dougiamas](https://en.wikipedia.org/wiki/Martin_Dougiamas), for giving us Moodle, and to the [Moodle community](https://moodle.org/)

## License

Copyright © 2016 onwards, Nicolas Martignoni <nicolas@martignoni.net>

* All the source code is licensed under GPL 3 or any later version
* The documentation is licensed under Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International.

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version. This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.


