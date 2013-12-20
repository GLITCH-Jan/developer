<?php

/**
 * File: update_411-420.php.
 * Author: Ulrich Block
 * Date: 24.11.13
 * Time: 12:51
 * Contact: <ulrich.block@easy-wi.com>
 *
 * This file is part of Easy-WI.
 *
 * Easy-WI is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Easy-WI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy-WI.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Diese Datei ist Teil von Easy-WI.
 *
 * Easy-WI ist Freie Software: Sie koennen es unter den Bedingungen
 * der GNU General Public License, wie von der Free Software Foundation,
 * Version 3 der Lizenz oder (nach Ihrer Wahl) jeder spaeteren
 * veroeffentlichten Version, weiterverbreiten und/oder modifizieren.
 *
 * Easy-WI wird in der Hoffnung, dass es nuetzlich sein wird, aber
 * OHNE JEDE GEWAEHELEISTUNG, bereitgestellt; sogar ohne die implizite
 * Gewaehrleistung der MARKTFAEHIGKEIT oder EIGNUNG FUER EINEN BESTIMMTEN ZWECK.
 * Siehe die GNU General Public License fuer weitere Details.
 *
 * Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
 * Programm erhalten haben. Wenn nicht, siehe <http://www.gnu.org/licenses/>.
 */

if (isset($include) and $include == true) {
    $query = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('4.11','<div align=\"right\">15.12.2013</div>
Leider haben sich in der 4.10 einige Fehler eingeschlichen. Dazu hat sie deutlich aufgezeigt, das viele Admins noch alte PHP Versionen nutzen und Easy-WI inkompatibel geworden ist.<br>
<br>
4.11 ist ein Hotfix Release, dass diese Probleme addressiert.<br>
<br>
<b>Änderungen:</b><br/>
<ul>
<li>Passwort Hash Fallback from Fallback</li>
<li>register_globals wird deaktiviert wenn an</li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br/>
<ul>
<li>Vertreter Login schlägt fehl.</li>
<li>GS Backup Templates enthalten falsche Variable server_id</li>
<li>Falscher Link im Adminpanel für ESXI Host</li>
<li>Minecraft Query funktioniert nicht</li>
<li>Falscher tsdns_settings.ini Syntax</li>
</ul>
','<div align=\"right\">12.15.2013</div>
Unfortunately errors have slipped in 4.10. In addition the update revealed that there are still admins with old PHP versions around. Those admins could not login anymore since.<br>
<br>
4.11 is a hotfix release which addresses these problems.<br>
<br>
<b>Changes:</b><br/>
<ul>
<li>password hash fallback from fallback</li>
<li>deaktivate register_globals if on</li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br/>
<ul>
<li>Substitute login failing</li>
<li>gs backup templates with incorrect variable server_id</li>
<li>incorrect link at esxi host link</li>
<li>Minecraft Query not working</li>
<li>wrong tsdns_settings.ini syntax</li>
</ul>
')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

    $query = $sql->prepare("SELECT `active` FROM `page_settings` LIMIT 1");
    $query->execute();
    if ($query->fetchColumn() == 'N') {
        $query = $sql->prepare("INSERT INTO `modules` (`id`,`file`,`get`,`sub`,`type`,`active`) VALUES (9,'','pn','','C','N') ON DUPLICATE KEY UPDATE `active`=VALUES(`active`)");
        $query->execute();
    }
    $query = $sql->prepare("SELECT `active` FROM `lendsettings` WHERE `resellerid`=0 LIMIT 1");
    $query->execute();
    if ($query->fetchColumn() == 'N') {
        $query = $sql->prepare("INSERT INTO `modules` (`id`,`file`,`get`,`sub`,`type`,`active`) VALUES (5,'','le','','C','N') ON DUPLICATE KEY UPDATE `active`=VALUES(`active`)");
        $query->execute();
    }

    $query = $sql->prepare("ALTER TABLE `servertypes` ADD COLUMN `gameq` varchar(255) NULL AFTER `qstat`");
    $query->execute();

    require_once(EASYWIDIR . '/install/addonslist.php');

    $query2 = $sql->prepare("SELECT `id` FROM `addons` WHERE `addon`=? AND `resellerid`=? LIMIT 1");
    $query3 = $sql->prepare("INSERT INTO `addons` (`active`,`depending`,`paddon`,`addon`,`type`,`folder`,`menudescription`,`configs`,`cmd`,`rmcmd`,`resellerid`) VALUES ('Y',?,?,?,?,?,?,?,?,?,?)");
    $query4 = $sql->prepare("SELECT `id` FROM `servertypes` WHERE `shorten`=? AND `resellerid`=? LIMIT 1");
    $query5 = $sql->prepare("INSERT INTO `addons_allowed` (`addon_id`,`servertype_id`,`reseller_id`) VALUES (?,?,?)");

    $query = $sql->prepare("SELECT `resellerid` FROM `userdata` WHERE `accounttype` IN ('a','r')");
    $query->execute();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

        // add additional game images
        $query = $sql->prepare("SELECT 1 FROM `servertypes` WHERE `shorten`='killingfloor252' AND `resellerid`=? LIMIT 1");
        $query->execute(array($row['resellerid']));
        if ($query->rowCount() == 0) {
            $query = $sql->prepare("INSERT INTO `servertypes` (`steamgame`, `appID`, `steamVersion`, `updates`, `shorten`, `description`, `type`, `gamebinary`, `binarydir`, `modfolder`, `fps`, `slots`, `map`, `cmd`, `modcmds`, `tic`, `qstat`, `gamemod`, `gamemod2`, `configs`, `configedit`, `qstatpassparam`, `portStep`, `portMax`, `portOne`, `portTwo`, `portThree`, `portFour`, `portFive`, `protected`, `resellerid`, `gameq`, `os`, `ftpAccess`, `ramLimited`, `downloadPath`, `protectedSaveCFGs`, `iptables`, `mapGroup`) VALUES ('N', NULL, NULL, 1, 'killingfloor252', 'KillingFloor 2.52', '', 'ucc-bin', 'System', 'KFMod20', NULL, 12, 'KF-Offices.ut2', './%binary% server KF-Offices?game=KFMod.KFGameType?GameStats=True?MaxPlayers=%slots% -mod=\"KFMod20\" -log=../Logs/KF_Server.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir', NULL, NULL, NULL, 'N', NULL, 'KFMod20/System/KFMod20.ini', '[autoexec.cfg] cfg\r\nsv_max_clients %slots%\r\nsv_bindaddr %ip%\r\nsv_port %port%\r\n\r\n[config_ctf.cfg] cfg\r\nsv_max_clients %slots%\r\nsv_bindaddr %ip%\r\nsv_port %port%\r\n\r\n[config_dm.cfg] cfg\r\nsv_max_clients %slots%\r\nsv_bindaddr %ip%\r\nsv_port %port%\r\n\r\n[config_tdm.cfg] cfg\r\nsv_max_clients %slots%\r\nsv_bindaddr %ip%\r\nsv_port %port%', NULL, 10, 3, 7777, 7787, 7778, NULL, NULL, 'N', ?, 'killingfloor', 'L', 'Y', 'N', NULL, NULL, NULL, NULL)");
            $query->execute(array($row['resellerid']));
        }
        $query = $sql->prepare("SELECT 1 FROM `servertypes` WHERE `shorten`='sauerbratenremod' AND `resellerid`=? LIMIT 1");
        $query->execute(array($row['resellerid']));
        if ($query->rowCount() == 0) {
            $query = $sql->prepare("INSERT INTO `servertypes` (`steamgame`, `appID`, `steamVersion`, `updates`, `shorten`, `description`, `type`, `gamebinary`, `binarydir`, `modfolder`, `fps`, `slots`, `map`, `cmd`, `modcmds`, `tic`, `qstat`, `gamemod`, `gamemod2`, `configs`, `configedit`, `qstatpassparam`, `portStep`, `portMax`, `portOne`, `portTwo`, `portThree`, `portFour`, `portFive`, `protected`, `resellerid`, `gameq`, `os`, `ftpAccess`, `ramLimited`, `downloadPath`, `protectedSaveCFGs`, `iptables`, `mapGroup`) VALUES ('N', NULL, NULL, 1, 'sauerbratenremod', 'Sauerbraten', '', 'remod', NULL, NULL, NULL, 12, NULL, './%binary% -i%ip% -j%port% -c%slots% -fserver-init.cfg', NULL, NULL, NULL, 'N', NULL, 'server-init.cfg full', '[server-init.cfg] cfg\r\nserverip \"%ip%\"\r\nserverport \"%port%\"\r\nmaxclients \"%slots%\"', NULL, 10, 2, 28785, 28786, NULL, NULL, NULL, 'N', ?, 'cube2', 'L', 'Y', 'N', NULL, NULL, NULL, NULL)");
            $query->execute(array($row['resellerid']));
        }
        $query = $sql->prepare("SELECT 1 FROM `servertypes` WHERE `shorten`='shootmania' AND `resellerid`=? LIMIT 1");
        $query->execute(array($row['resellerid']));
        if ($query->rowCount() == 0) {
            $query = $sql->prepare("INSERT INTO `servertypes` (`steamgame`, `appID`, `steamVersion`, `updates`, `shorten`, `description`, `type`, `gamebinary`, `binarydir`, `modfolder`, `fps`, `slots`, `map`, `cmd`, `modcmds`, `tic`, `qstat`, `gamemod`, `gamemod2`, `configs`, `configedit`, `qstatpassparam`, `portStep`, `portMax`, `portOne`, `portTwo`, `portThree`, `portFour`, `portFive`, `protected`, `resellerid`, `gameq`, `os`, `ftpAccess`, `ramLimited`, `downloadPath`, `protectedSaveCFGs`, `iptables`, `mapGroup`) VALUES ('N', NULL, NULL, 1, 'shootmania', 'Shootmania', '', 'ShootmaniaServer.sh', NULL, NULL, NULL, 12, 'ShootmaniaServer', './%binary% /dedicated_cfg=dedicated_cfg.txt /nodaemon /bindip=%ip% /forceip=%ip%', '[Shootmania Combo]\r\n/game_settings=MatchSettings/Combo.txt /title=SMStormCombo@nadeolabs\r\n\r\n[Shootmania Battle]\r\n/game_settings=MatchSettings/Battle.txt /title=SMStorm\r\n\r\n[Shootmania Elite = default]\r\n/game_settings=MatchSettings/Elite.txt /title=SMStormElite@nadeolabs\r\n\r\n[Shootmania Heroes]\r\n/game_settings=MatchSettings/Hereos.txt /title=SMStormHeroes@nadeolabs\r\n\r\n[Shootmania Joust]\r\n/game_settings=MatchSettings/Joust.txt /title=SMStormJoust@nadeolabs\r\n\r\n[Shootmania Melee]\r\n/game_settings=MatchSettings/Melee.txt /title=SMStorm\r\n\r\n[Shootmania Realms / broken]\r\n/game_settings=MatchSettings/Realms.txt /title=SMStorm\r\n\r\n[Shootmania Royal]\r\n/game_settings=MatchSettings/Royal.txt /title=SMStormRoyal@nadeolabs\r\n\r\n[Shootmania Siege]\r\n/game_settings=MatchSettings/Siege.txt /title=SMStorm\r\n\r\n[Shootmania TimeAttack / broken]\r\n/game_settings=MatchSettings/TimeAttack.txt /title=SMStorm\r\n\r\n[Shootmania YOUR Mod, ask the Support]\r\n/game_settings=MatchSettings/CustomMod.txt', NULL, NULL, 'N', NULL, 'UserData/Config/dedicated_cfg.txt\r\nUserData/Maps/MatchSettings/Battle.txt\r\nUserData/Maps/MatchSettings/Elite.txt\r\nUserData/Maps/MatchSettings/Hereos.txt\r\nUserData/Maps/MatchSettings/Joust.txt\r\nUserData/Maps/MatchSettings/Melee.txt\r\nUserData/Maps/MatchSettings/Realms.txt\r\nUserData/Maps/MatchSettings/Royal.txt\r\nUserData/Maps/MatchSettings/Siege.txt\r\nUserData/Maps/MatchSettings/TimeAttack.txt\r\nUserData/Maps/MatchSettings/CustomMod.txt', '[UserData/Config/dedicated_cfg.txt] xml\r\n<max_players>%slots%</max_players>\r\n<max_spectators>10</max_spectators>\r\n<enable_p2p_upload>false</enable_p2p_upload>\r\n<connection_uploadrate>2048</connection_uploadrate>\r\n<connection_downloadrate>4096</connection_downloadrate>\r\n<force_ip_address>%ip%</force_ip_address>\r\n<server_port>%port%</server_port>\r\n<server_p2p_port>%port2%</server_p2p_port>\r\n<bind_ip_address>%ip%</bind_ip_address>\r\n<xmlrpc_port>%port3%</xmlrpc_port>', NULL, 10, 3, 2350, 2351, 2352, NULL, NULL, 'N', ?, '', 'L', 'Y', 'N', NULL, NULL, NULL, NULL)");
            $query->execute(array($row['resellerid']));
        }
        $query = $sql->prepare("SELECT 1 FROM `servertypes` WHERE `shorten`='trackmania' AND `resellerid`=? LIMIT 1");
        $query->execute(array($row['resellerid']));
        if ($query->rowCount() == 0) {
            $query = $sql->prepare("INSERT INTO `servertypes` (`steamgame`, `appID`, `steamVersion`, `updates`, `shorten`, `description`, `type`, `gamebinary`, `binarydir`, `modfolder`, `fps`, `slots`, `map`, `cmd`, `modcmds`, `tic`, `qstat`, `gamemod`, `gamemod2`, `configs`, `configedit`, `qstatpassparam`, `portStep`, `portMax`, `portOne`, `portTwo`, `portThree`, `portFour`, `portFive`, `protected`, `resellerid`, `gameq`, `os`, `ftpAccess`, `ramLimited`, `downloadPath`, `protectedSaveCFGs`, `iptables`, `mapGroup`) VALUES ('N', NULL, NULL, 1, 'trackmania', 'Trackmania2', '', 'TrackmaniaServer.sh', NULL, NULL, NULL, 12, 'TrackmaniaServer', './%binary% /dedicated_cfg=dedicated_cfg.txt /nodaemon /bindip=%ip% /forceip=%ip%', '[Trackmania2 Canyon A = default]\r\n/game_settings=MatchSettings/TMCanyonA.txt /title=TMCanyon\r\n\r\n[Trackmania2 Canyon B]\r\n/game_settings=MatchSettings/TMCanyonB.txt /title=TMCanyon\r\n\r\n[Trackmania2 Canyon C]\r\n/game_settings=MatchSettings/TMCanyonC.txt /title=TMCanyon\r\n\r\n[Trackmania2 Canyon Custom]\r\n/game_settings=MatchSettings/TMCanyonCustom.txt /title=TMCanyon\r\n\r\n[Trackmania2 Stadium A]\r\n/game_settings=MatchSettings/TMStadiumA.txt /title=TMStadium\r\n\r\n[Trackmania2 Stadium B]\r\n/game_settings=MatchSettings/TMStadiumB.txt /title=TMStadium\r\n\r\n[Trackmania2 Stadium C]\r\n/game_settings=MatchSettings/TMStadiumC.txt /title=TMStadium\r\n\r\n[Trackmania2 Stadium Custom]\r\n/game_settings=MatchSettings/TMStadiumCustom.txt /title=TMStadium\r\n\r\n[Trackmania2 Valley A]\r\n/game_settings=MatchSettings/TMValleyA.txt /title=TMValley\r\n\r\n[Trackmania2 Valley B]\r\n/game_settings=MatchSettings/TMValleyB.txt /title=TMValley\r\n\r\n[Trackmania2 Valley C]\r\n/game_settings=MatchSettings/TMValleyC.txt /title=TMValley\r\n\r\n[Trackmania2 Valley Custom]\r\n/game_settings=MatchSettings/TMValleyCustom.txt /title=TMValley\r\n\r\n[Trackmania2 Platform A]\r\n/game_settings=MatchSettings/CanyonPlatformA.txt /title=Platform@nadeolive\r\n\r\n[Trackmania2 Platform B]\r\n/game_settings=MatchSettings/CanyonPlatformB.txt /title=Platform@nadeolive\r\n\r\n[Trackmania2 Platform C]\r\n/game_settings=MatchSettings/CanyonPlatformC.txt /title=Platform@nadeolive\r\n\r\n[Trackmania2 Platform Custom]\r\n/game_settings=MatchSettings/CanyonPlatformCustom.txt /title=Platform@nadeolive', NULL, NULL, 'N', NULL, 'UserData/Config/dedicated_cfg.txt\r\nUserData/Maps/MatchSettings/TMCanyonCustom.txt\r\nUserData/Maps/MatchSettings/TMStadiumCustom.txt\r\nUserData/Maps/MatchSettings/TMValleyCustom.txt\r\nUserData/Maps/MatchSettings/CanyonPlatformCustom.txt', '[UserData/Config/dedicated_cfg.txt] xml\r\n<max_players>%slots%</max_players>\r\n<max_spectators>10</max_spectators>\r\n<enable_p2p_upload>false</enable_p2p_upload>\r\n<connection_uploadrate>2048</connection_uploadrate>\r\n<connection_downloadrate>4096</connection_downloadrate>\r\n<force_ip_address>%ip%</force_ip_address>\r\n<server_port>%port%</server_port>\r\n<server_p2p_port>%port2%</server_p2p_port>\r\n<bind_ip_address>%ip%</bind_ip_address>\r\n<xmlrpc_port>%port3%</xmlrpc_port>', NULL, 10, 3, 2350, 2351, 2352, NULL, NULL, 'N', ?, '', 'L', 'Y', 'N', NULL, NULL, NULL, NULL)");
            $query->execute(array($row['resellerid']));
        }
        $query = $sql->prepare("SELECT 1 FROM `servertypes` WHERE `shorten`='ut99' AND `resellerid`=? LIMIT 1");
        $query->execute(array($row['resellerid']));
        if ($query->rowCount() == 0) {
            $query = $sql->prepare("INSERT INTO `servertypes` (`steamgame`, `appID`, `steamVersion`, `updates`, `shorten`, `description`, `type`, `gamebinary`, `binarydir`, `modfolder`, `fps`, `slots`, `map`, `cmd`, `modcmds`, `tic`, `qstat`, `gamemod`, `gamemod2`, `configs`, `configedit`, `qstatpassparam`, `portStep`, `portMax`, `portOne`, `portTwo`, `portThree`, `portFour`, `portFive`, `protected`, `resellerid`, `gameq`, `os`, `ftpAccess`, `ramLimited`, `downloadPath`, `protectedSaveCFGs`, `iptables`, `mapGroup`) VALUES ('N', NULL, NULL, 1, 'ut99', 'Unreal Tournament', '', 'ucc-bin', 'System', NULL, NULL, 12, 'DM-Codex.unr', './%binary% server', '[DM = default]\r\n%map%?Game=Botpack.DeathMatchPlus?MaxPlayers=%slots% -ini=UnrealTournament.ini -log=server.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[TDM]\r\n%map%?Game=Botpack.TeamGamePlus?MaxPlayers=%slots% -ini=UnrealTournament.ini -log=server.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[CTF]\r\n%map%?Game=Botpack.CTFGame?MaxPlayers=%slots% -ini=UnrealTournament.ini -log=server.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[LMS]\r\n%map%?Game=Botpack.LastManStanding?MaxPlayers=%slots% -ini=UnrealTournament.ini -log=server.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[DOM]\r\n%map%?Game=Botpack.Domination?MaxPlayers=%slots% -ini=UnrealTournament.ini -log=server.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[BT]\r\n%map%?Game=BunnyTrack.BunnyTrackGame?MaxPlayers=%slots% -ini=UnrealTournament.ini -log=server.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[JB]\r\n%map%?Game=JailBreak.JailBreak?MaxPlayers=%slots% -ini=UnrealTournament.ini -log=server.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[MH]\r\n%map%?Game=Monsterhunt.Monsterhunt?MaxPlayers=%slots% -ini=UnrealTournament.ini -log=server.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[TMH]\r\n%map%?Game=TeamMH.TeamMH?MaxPlayers=%slots% -ini=UnrealTournament.ini -log=server.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[RA]\r\n%map%?Game=RocketArena.ArenaGame?MaxPlayers=%slots% -ini=UnrealTournament.ini -log=server.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir', NULL, NULL, 'N', NULL, 'ReadMe_WebAdmin_AdminPassword_ReDirect.txt', NULL, NULL, 10, 3, 7777, 7778, 7779, NULL, NULL, 'N', ?, 'ut', 'L', 'Y', 'N', NULL, NULL, NULL, NULL)");
            $query->execute(array($row['resellerid']));
        }
        $query = $sql->prepare("SELECT 1 FROM `servertypes` WHERE `shorten`='ut2004' AND `resellerid`=? LIMIT 1");
        $query->execute(array($row['resellerid']));
        if ($query->rowCount() == 0) {
            $query = $sql->prepare("INSERT INTO `servertypes` (`steamgame`, `appID`, `steamVersion`, `updates`, `shorten`, `description`, `type`, `gamebinary`, `binarydir`, `modfolder`, `fps`, `slots`, `map`, `cmd`, `modcmds`, `tic`, `qstat`, `gamemod`, `gamemod2`, `configs`, `configedit`, `qstatpassparam`, `portStep`, `portMax`, `portOne`, `portTwo`, `portThree`, `portFour`, `portFive`, `protected`, `resellerid`, `gameq`, `os`, `ftpAccess`, `ramLimited`, `downloadPath`, `protectedSaveCFGs`, `iptables`, `mapGroup`) VALUES ('N', NULL, NULL, 1, 'ut2004', 'UT2004', '', 'ucc-bin', 'System', NULL, NULL, 12, 'DM-Rankin.ut2', './%binary% server', '[DM = default]\r\n%map%?Game=XGame.xDeathMatch?MaxPlayers=%slots% -ini=UT2004.ini -log=UT2004.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[TDM]\r\n%map%?Game=XGame.xTeamGame?MaxPlayers=%slots% -ini=UT2004.ini -log=UT2004.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[CTF]\r\n%map%?Game=XGame.xCTFGame?MaxPlayers=%slots% -ini=UT2004.ini -log=UT2004.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[iCTF-Clan]\r\n%map%?Game=XGame.InstagibCTF?MaxPlayers=%slots% -ini=UT2004.ini -log=UT2004.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[iCTF-Public]\r\n%map%?Game=XGame.xCTFGame?Mutator=XGame.MutInstaGib?MaxPlayers=%slots% -ini=UT2004.ini -log=UT2004.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[TAM]\r\n%map%?Game=3spnv3141.TeamArenaMaster?MaxPlayers=%slots% -ini=UT2004.ini -log=UT2004.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[AM]\r\n%map%?Game=3spnv3141.ArenaMaster=%slots% -ini=UT2004.ini -log=UT2004.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[Freon]\r\n%map%?Game=3SPNv3141.Freon?MaxPlayers=%slots% -ini=UT2004.ini -log=UT2004.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[ONS]\r\n%map%?Game=Onslaught.ONSOnslaughtGame?MaxPlayers=%slots% -ini=UT2004.ini -log=UT2004.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[LMS]\r\n%map%?Game=BonusPack.xLastManStandingGame?MaxPlayers=%slots% -ini=UT2004.ini -log=UT2004.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[DDOM]\r\n%map%?Game=XGame.xDoubleDom?MaxPlayers=%slots% -ini=UT2004.ini -log=UT2004.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[BR]\r\n%map%?Game=XGame.xBombingRun?MaxPlayers=%slots% -ini=UT2004.ini -log=UT2004.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[vCTF]\r\n%map%?Game=XGame.xVehicleCTFGame?MaxPlayers=%slots% -ini=UT2004.ini -log=UT2004.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[Mutant]\r\n%map%?Game=BonusPack.xMutantGame?MaxPlayers=%slots% -ini=UT2004.ini -log=UT2004.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[SkaarjPack.Invasion]\r\n%map%?Game=SkaarjPack.Invasion?MaxPlayers=%slots% -ini=UT2004.ini -log=UT2004.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[AS]\r\n%map%?Game=UT2K4Assault.ASGameInfo?MaxPlayers=%slots% -ini=UT2004.ini -log=UT2004.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir\r\n\r\n[Jailbreak]\r\n%map%?Game=Jailbreak.Jailbreak?MaxPlayers=%slots% -ini=UT2004_JB.ini -log=UT2004_JB.log -multihome=%ip% -port=%port% -queryport=%port2% -WebServerListenPort=%port3% -nohomedir', NULL, NULL, 'N', NULL, 'System/UT2004.ini\r\nSystem/XAdmin.ini\r\nReadMe_WebAdmin_AdminPassword_ReDirect.txt', '[System/UT2004.ini] ini\r\nListenPort=%port3%', NULL, 10, 3, 7777, 7778, 7779, NULL, NULL, 'N', ?, 'ut2004', 'L', 'Y', 'N', NULL, NULL, NULL, NULL)");
            $query->execute(array($row['resellerid']));
        }
        $query = $sql->prepare("SELECT 1 FROM `servertypes` WHERE `shorten`='warsow' AND `resellerid`=? LIMIT 1");
        $query->execute(array($row['resellerid']));
        if ($query->rowCount() == 0) {
            $query = $sql->prepare("INSERT INTO `servertypes` (`steamgame`, `appID`, `steamVersion`, `updates`, `shorten`, `description`, `type`, `gamebinary`, `binarydir`, `modfolder`, `fps`, `slots`, `map`, `cmd`, `modcmds`, `tic`, `qstat`, `gamemod`, `gamemod2`, `configs`, `configedit`, `qstatpassparam`, `portStep`, `portMax`, `portOne`, `portTwo`, `portThree`, `portFour`, `portFive`, `protected`, `resellerid`, `gameq`, `os`, `ftpAccess`, `ramLimited`, `downloadPath`, `protectedSaveCFGs`, `iptables`, `mapGroup`) VALUES ('N', NULL, NULL, 1, 'warsow', 'WarSow', '', 'wsw_server.x86_64', '', 'basewsw', NULL, 12, '', './%binary% +exec dedicated_server.cfg +set sv_maxclients %slots% +set sv_ip %ip% +set sv_port %port% +set sv_port6 %port%', '[Deathmatch]\r\n+set g_gametype \"dm\"\r\n\r\n[Team Deathmatch]\r\n+set g_gametype \"tdm\"\r\n\r\n[Team DMCTF]\r\n+set g_gametype \"tdm_ctf\"\r\n\r\n[Capture the Flag]\r\n+set g_gametype \"ctf\"\r\n\r\n[Clan Arena]\r\n+set g_gametype \"ca\"\r\n\r\n[Duel]\r\n+set g_gametype \"duel\"\r\n\r\n[Duel Arena]\r\n+set g_gametype \"da\"\r\n\r\n[DuelQuad]\r\n+set g_gametype \"duel_quad\"\r\n\r\n[FFA = default]\r\n+set g_gametype \"ffa\"\r\n\r\n[Mid Air]\r\n+set g_gametype \"midair\"\r\n\r\n[Race]\r\n+set g_gametype \"race\"\r\n\r\n[All Round]\r\n+set g_gametype \"allaround\"', NULL, NULL, 'N', NULL, 'basewsw/dedicated_server.cfg\r\nbasewsw/motd.cfg', '[basewsw/dedicated_autoexec.cfg] cfg\r\nsv_ip \"%ip%\"\r\nsv_port \"%port%\"\r\nsv_port6 \"%port%\"\r\nsv_maxclients \"%slots%\"', NULL, 10, 1, 44400, NULL, NULL, NULL, NULL, 'N', ?, 'warsow', 'L', 'Y', 'N', NULL, NULL, NULL, NULL);");
            $query->execute(array($row['resellerid']));
        }
        $query = $sql->prepare("SELECT 1 FROM `servertypes` WHERE `shorten`='jcmp' AND `resellerid`=? LIMIT 1");
        $query->execute(array($row['resellerid']));
        if ($query->rowCount() == 0) {
            $query = $sql->prepare("INSERT INTO `servertypes` (`steamgame`, `appID`, `steamVersion`, `updates`, `shorten`, `description`, `type`, `gamebinary`, `binarydir`, `modfolder`, `fps`, `slots`, `map`, `cmd`, `modcmds`, `tic`, `qstat`, `gamemod`, `gamemod2`, `configs`, `configedit`, `qstatpassparam`, `portStep`, `portMax`, `portOne`, `portTwo`, `portThree`, `portFour`, `portFive`, `protected`, `resellerid`, `gameq`, `os`, `ftpAccess`, `ramLimited`, `downloadPath`, `protectedSaveCFGs`, `iptables`, `mapGroup`) VALUES ('S', 261140, NULL, 1, 'jcmp', 'Just Cause 2 Multi Player', '', 'Jcmp-Server', NULL, NULL, NULL, 0, NULL, './%binary%', NULL, NULL,'jcmp', 'N', NULL, 'config.lua', '[config.lua] lua\r\nMaxPlayers = %slots%,\r\nBindIP = \"%ip%\",\r\nBindPort = %port%,', 100, 2, 7777, 7778, NULL, NULL, NULL, 'N', ?, 'N', 'jcmp', 'L', 'Y', 'N', NULL, NULL, NULL, NULL);");
            $query->execute(array($row['resellerid']));
        }

        // Loop to addons and add in case addons does not exist yet
        foreach ($gameAddons as $addon) {

            if (count($addon) == 10) {

                $query2->execute(array($addon[':addon'],$row['resellerid']));
                $addonID = $query2->fetchColumn();

                if ($addonID < 1) {

                    $dependsID = 0;

                    if (strlen($addon[':depends'])) {
                        $query2->execute(array($addon[':depends'],$row['resellerid']));
                        $dependsID = $query2->fetchColumn();
                    }

                    $query3->execute(array($dependsID, $addon[':paddon'], $addon[':addon'], $addon[':type'], $addon[':folder'], $addon[':menudescription'], $addon[':configs'], $addon[':cmd'], $addon[':rmcmd'],$row['resellerid']));

                    $addonID = $sql->lastInsertId();

                    foreach ($addon[':supported'] as $supported) {

                        $query4->execute(array($supported,$row['resellerid']));

                        $query5->execute(array($addonID,$query4->fetchColumn(),$row['resellerid']));

                    }
                }
            }
        }

    }


    // Migrate existing Images from qstat to GameQ

    // Most accurate based on appID
    $array = array('css' => 232330, 'dods' => 232290, 'l4d' => 550, 'l4d2' => 222860, 'aoc' => 17515, 'hl2dm' => 232370, 'insurgency' =>  7705, 'tf2' => 232250, 'csgo' => 740, 'killingfloor' => 215360, 'zps' => 17505, 'source' => 17575);

    $query = $sql->prepare("UPDATE `servertypes` SET `gameq`=? WHERE `appID`=?");

    foreach ($array as $k => $v) {
        $query->execute(array($k, $v));
    }

    // Accurate, based on easy-wi/qstat query
    $array = array('minecraft' => 'minecraft', 'samp' => 'gtasamp', 'Mta' => 'mtasa', 'teeworlds' => 'teeworlds', 'warsow' => 'warsows', 'et' => 'woets', 'ut' => 'uns', 'ut2004' => 'ut2004s', 'ut3' => 'ut2s');

    $query = $sql->prepare("UPDATE `servertypes` SET `gameq`=? WHERE `qstat`=?");

    foreach ($array as $k => $v) {
        $query->execute(array($k, $v));
    }

    // Less accurate, based on shorten
    $array = array('dod' => 'dod', 'cs16' => 'cstrike', 'cscz' => 'czero', 'tfc' => 'tfc', 'cod' => 'cod', 'cod2' => 'cod2', 'cod4' => 'cod4', 'codmw3' => 'codmw3', 'coduo' => 'coduo', 'codwaw' => 'codwaw');

    $query = $sql->prepare("UPDATE `servertypes` SET `gameq`=? WHERE `shorten`=?");

    foreach ($array as $k => $v) {
        $query->execute(array($k, $v));
    }

    // rework workshop support and allow with csgo

    $query = $sql->prepare("ALTER TABLE `servertypes` ADD COLUMN `workShop` enum('Y','N') DEFAULT 'N' AFTER `mapGroup`");
    $query->execute();

    $query = $sql->prepare("UPDATE `servertypes` SET `workShop`='Y' WHERE `appID`=730 OR `appID`=740");
    $query->execute();

    // DROP as not needed anymore
    $query = $sql->prepare("DROP TABLE `qstatshorten`");
    $query->execute();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}