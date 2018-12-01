<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'tool_moodlebox', language 'fr' (French)
 *
 * @package    tool_moodlebox
 * @copyright  2016 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['changepassworderror'] = 'Le mot de passe de la MoodleBox n\'a pas été modifié. Les mots de passe donnés ne concordent pas.';
$string['changepasswordmessage'] = 'Le mot de passe principal de la MoodleBox (compte Unix), de la base de données et de phpMyAdmin ont été modifiés.<br /><br />Attention ! Le mot de passe de l\'utilisateur Admin du Moodle <b>n\'a pas été modifié</b>. Pour le modifier, veuillez passer par la page des préférences de cet utilisateur.';
$string['changewifisettings'] = 'Changer les réglages Wi-Fi';
$string['cpufrequency'] = 'Fréquence du processeur';
$string['cpuload'] = 'Charge du processeur';
$string['cputemperature'] = 'Température du processeur';
$string['datetime'] = 'Date et heure';
$string['datetime_help'] = 'Si la MoodleBox n\'est pas connectée à Internet, elle ne sera pas à l\'heure. Il est possible de la mettre à l\'heure manuellement au moyen de ce réglage.';
$string['datetimemessage'] = 'L\'horloge de la MoodleBox a été mise à l\'heure. Pour une précision encore meilleure, il est recommandé de brancher la MoodleBox à un réseau connecté à Internet, au moyen d\'un câble ethernet.';
$string['datetimeset'] = 'Changer la date et l\'heure';
$string['datetimesetmessage'] = 'L\'horloge de la MoodleBox n\'est pas à l\'heure. Il est vivement recommandé de régler la date et l\'heure à leurs valeurs actuelles.';
$string['datetimesetting'] = 'Date et heure';
$string['dhcpclientinfo'] = 'Adresse IP et nom du client';
$string['dhcpclientnumber'] = 'nombre de clients';
$string['dhcpclients'] = 'Clients DHCP';
$string['hidden'] = 'Caché';
$string['information'] = 'Information';
$string['kernelversion'] = 'Version du noyau';
$string['moodleboxinfo'] = 'Version de la MoodleBox';
$string['moodleboxinfofileerror'] = 'Information non disponible';
$string['moodleboxpluginversion'] = 'Version du plugin MoodleBox';
$string['missingconfigurationerror'] = 'Cette section n\'est pas disponble, car l\'installation du plugin n\'est pas complète. Le réglage ne peut donc pas être traité par la MoodleBox. Veuillez consulter la <a href="https://github.com/moodlebox/moodle-tool_moodlebox/blob/master/README.md" target="_blank">documentation d\'installation</a> afin de corriger cette erreur.';
$string['parameter'] = 'Paramètre';
$string['passwordprotected'] = 'Protégé par mot de passe';
$string['passwordsetting'] = 'Mot de passe de la MoodleBox';
$string['passwordsetting_help'] = 'Le mot de passe principal de MoodleBox peut être changé ici. __Il est vivement déconseillé de conserver le mot de passe défini par défaut__. Vous __devez__ absolument le changer comme mesure de sécurité minimale.';
$string['pluginname'] = 'MoodleBox';
$string['privacy:metadata'] = 'Le plugin MoodleBox affiche certaines informations du Raspberry Pi et permet quelques modifications de configuration, mais ne touche ni ne stocke aucune donnée personnelle.';
$string['resizepartition'] = 'Redimensionner la partition de la carte SD';
$string['resizepartition_help'] = 'Utiliser ce bouton pour redimensionner la partition de la carte SD.';
$string['resizepartitionmessage'] = 'La partition de la carte SD a été redimensionnée à sa taille maximale. La MoodleBox redémarre maintenant. Elle sera à nouveau accessible dans quelques instants.';
$string['resizepartitionsetting'] = 'Redimensionnement de la partition de la carte SD';
$string['raspberryhardware'] = 'Modèle Raspberry Pi';
$string['raspbianversion'] = 'Version de Raspbian';
$string['restart'] = 'Redémarrer la MoodleBox';
$string['restartmessage'] = 'La MoodleBox va redémarrer. Elle sera à nouveau accessible dans quelques instants.';
$string['restartstop'] = 'Redémarrage et arrêt';
$string['restartstop_help'] = 'Utilisez ces boutons pour redémarrer ou éteindre la MoodleBox. Il n\'est pas recommandé de débrancher l\'alimentation pour éteindre le MoodleBox.';
$string['rpi1'] = 'Raspberry Pi 1';
$string['rpi2'] = 'Raspberry Pi 2B';
$string['rpi3'] = 'Raspberry Pi 3B';
$string['rpi3bplus'] = 'Raspberry Pi 3B+';
$string['rpizerow'] = 'Raspberry Pi Zero W';
$string['sdcardavailablespace'] = 'Espace libre sur la carte SD';
$string['shutdown'] = 'Arrêter la MoodleBox';
$string['shutdownmessage'] = 'La MoodleBox va s\'arrêter. Veuillez attendre quelques secondes avant de retirer l\'alimentation.';
$string['systeminfo'] = 'Informations MoodleBox';
$string['systeminfo_help'] = 'Le panneau d\'informations MoodleBox affiche plusieurs données importantes sur la MoodleBox. Ces informations comprennent :

* des données essentielles au fonctionnement de votre MoodleBox, telles que l\'espace disque restant sur la carte SD et la charge, température et fréquence du processeur ;
* les réglages actuels du réseau Wi-Fi fourni par la MoodleBox ;
* le nombre, l\'adresse IP et le nom de tous les appareils connectés à la MoodleBox ;
* le modèle et le système d\'exploitation de la Raspberry Pi ;
* la version de la MoodleBox et du plugin MoodleBox.
';
$string['unknownmodel'] = 'Modèle de Raspberry Pi inconnu';
$string['unsupportedhardware'] = 'Matériel serveur non compatible détecté ! Ce plugin ne fonctionne que sur Raspberry Pi';
$string['uptime'] = 'Durée de fonctionnement du système';
$string['visible'] = 'Visible';
$string['wifichannel'] = 'Canal Wi-Fi';
$string['wifichannel_help'] = 'Il n\'est pas nécessaire de changer le canal de diffusion Wi-Fi, sauf en cas de mauvaises performances dues à des interférences.';
$string['wificountry'] = 'Pays de régulation Wi-Fi';
$string['wificountry_help'] = 'Pour des raisons juridiques, il est recommandé de sélectionner votre pays comme pays de régulation Wi-Fi.';
$string['wifipassword'] = 'Mot de passe Wi-Fi';
$string['wifipassword_help'] = 'Si vous avez choisi un réseau Wi-Fi protégé par mot de passe, pour éviter que des intrus utilisent le réseau Wi-Fi de la MoodleBox, il est recommandé de modifier son mot de passe par défaut. Le mot de passe du réseau Wi-Fi doit comporter entre 8 et 63 caractères.';
$string['wifipassworderror'] = 'Le mot de passe du réseau Wi-Fi doit comporter entre 8 et 63 caractères.';
$string['wifipasswordon'] = 'Protection réseau Wi-Fi';
$string['wifipasswordon_help'] = 'Si ce réglage est activé, les utilisateurs doivent saisir un mot de passe pour se connecter au réseau Wi-Fi de la MoodleBox.';
$string['wifisettings'] = 'Réglages Wi-Fi';
$string['wifisettingsmessage'] = 'Les réglages du réseau Wi-Fi ont été modifié. N\'oubliez pas de communiquer le nom du réseau (SSID) et le mot de passe à vos étudiants.';
$string['wifissid'] = 'Nom du réseau Wi-Fi';
$string['wifissid_help'] = 'Le nom du réseau Wi-Fi (SSID) de la MoodleBox. Il doit s\'agir d\'une chaîne de 1 octet au minimum et de 32 octets au maximum. N\'oubliez pas que certains caractères, comme les émojis, utilisent plus d\'un octet.';
$string['wifissidhidden'] = 'Réseau Wi-Fi caché';
$string['wifissidhiddenstate'] = 'Visibilité du SSID Wi-Fi';
$string['wifissidhiddenstate_help'] = 'Si ce réglage est activé, le SSID Wi-Fi sera caché pour les utilisateurs, qui ne sauront pas qu\'il y une MoodleBox aux environs. Ceci diminuera considérablement la convivialité de l\'appareil, mais améliorera très légèrement sa sécurité.';
