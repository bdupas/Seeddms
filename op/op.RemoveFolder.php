<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

include("../inc/inc.Settings.php");
include("../inc/inc.LogInit.php");
include("../inc/inc.ClassEmail.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.ClassController.php");
include("../inc/inc.Authentication.php");
include("../inc/inc.Extension.php");

$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
$controller = Controller::factory($tmp[1]);

/* Check if the form data comes for a trusted request */
if(!checkFormKey('removefolder')) {
	UI::exitError(getMLText("folder_title", array("foldername" => getMLText("invalid_request_token"))),getMLText("invalid_request_token"));
}

if (!isset($_POST["folderid"]) || !is_numeric($_POST["folderid"]) || intval($_POST["folderid"])<1) {
	UI::exitError(getMLText("folder_title", array("foldername" => getMLText("invalid_folder_id"))),getMLText("invalid_folder_id"));
}
$folderid = $_POST["folderid"];
$folder = $dms->getFolder($folderid);

if (!is_object($folder)) {
	UI::exitError(getMLText("folder_title", array("foldername" => getMLText("invalid_folder_id"))),getMLText("invalid_folder_id"));
}

if ($folderid == $settings->_rootFolderID || !$folder->getParent()) {
	UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("cannot_rm_root"));
}

if ($folder->getAccessMode($user) < M_ALL) {
	UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("access_denied"));
}

/* save this for notification later on */
$nl =	$folder->getNotifyList();
$parent=$folder->getParent();
$foldername = $folder->getName();

$controller->setParam('folder', $folder);
$controller->setParam('index', $index);
if(!$controller->run()) {
	UI::exitError(getMLText("folder_title", array("foldername" => getMLText("invalid_folder_id"))),getMLText("invalid_folder_id"));
}

<<<<<<< HEAD
if ($notifier) {
	$subject = "folder_deleted_email_subject";
	$message = "folder_deleted_email_body";
	$params = array();
	$params['name'] = $foldername;
	$params['folder_path'] = $folder->getFolderPathPlain();
	$params['username'] = $user->getFullName();
	$params['sitename'] = $settings->_siteName;
	$params['http_root'] = $settings->_httpRoot;
	$notifier->toList($user, $nl["users"], $subject, $message, $params);
	foreach ($nl["groups"] as $grp) {
		$notifier->toGroup($user, $grp, $subject, $message, $params);
=======
$nl =	$folder->getNotifyList();
$foldername = $folder->getName();
if ($folder->remove()) {
	// Send notification to subscribers.
	if ($notifier) {
/*
		$subject = "###SITENAME###: ".$folder->getName()." - ".getMLText("folder_deleted_email");
		$message = getMLText("folder_deleted_email")."\r\n";
		$message .= 
			getMLText("name").": ".$folder->getName()."\r\n".
			getMLText("folder").": ".$folder->getFolderPathPlain()."\r\n".
			getMLText("comment").": ".$folder->getComment()."\r\n".
			"URL: ###URL_PREFIX###out/out.ViewFolder.php?folderid=".$folder->getID()."\r\n";

		$notifier->toList($user, $folder->_notifyList["users"], $subject, $message);
		foreach ($folder->_notifyList["groups"] as $grp) {
			$notifier->toGroup($user, $grp, $subject, $message);
		}
*/
		$subject = "folder_deleted_email_subject";
		$message = "folder_deleted_email_body";
		$params = array();
		$params['name'] = $foldername;
		$params['folder_path'] = $parent->getFolderPathPlain();
		$params['username'] = $user->getFullName();
		$params['sitename'] = $settings->_siteName;
		$params['http_root'] = $settings->_httpRoot;
		$params['url'] = "http".((isset($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'],'off')!=0)) ? "s" : "")."://".$_SERVER['HTTP_HOST'].$settings->_httpRoot."out/out.ViewFolder.php?folderid=".$parent->getID();
		$notifier->toList($user, $nl["users"], $subject, $message, $params);
		foreach ($nl["groups"] as $grp) {
			$notifier->toGroup($user, $grp, $subject, $message, $params);
		}
>>>>>>> seeddms-4.3.4
	}
}

add_log_line();

header("Location:../out/out.ViewFolder.php?folderid=".$parent->getID()."&showtree=".$_POST["showtree"]);

?>
