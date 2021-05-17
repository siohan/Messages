<?php
#-------------------------------------------------------------------------
# Module: Messages
# Version: 0.1, Claude SIOHAN
# Method: Install
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2008 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://dev.cmsmadesimple.org/projects/skeleton/
#
#-------------------------------------------------------------------------

/**
 * For separated methods, you'll always want to start with the following
 * line which check to make sure that method was called from the module
 * API, and that everything's safe to continue:
 */ 
if (!isset($gCms)) exit;


/** 
 * After this, the code is identical to the code that would otherwise be
 * wrapped in the Install() method in the module body.
 */

$db = $gCms->GetDb();

// mysql-specific, but ignored by other database
$taboptarray = array( 'mysql' => 'ENGINE=MyISAM' );

$dict = NewDataDictionary( $db );

// table schema description
$flds = "
	id I(11) AUTO KEY,
	sender C(255),
	senddate D,
	sendtime T,
	replyto C(255),
	group_id I(2),
	recipients_number I(3),
	subject C(255),
	message X,
	sent I(1) DEFAULT 0,
	priority I(1) DEFAULT 3,
	timbre I(11), 
	ar I(1) DEFAULT 0,
	relance I(1) DEFAULT 0,
	occurence I(11) DEFAULT 0";
	$sqlarray = $dict->CreateTableSQL( cms_db_prefix()."module_messages_messages", $flds, $taboptarray);
	$dict->ExecuteSQLArray($sqlarray);			
//
// mysql-specific, but ignored by other database
$taboptarray = array( 'mysql' => 'ENGINE=MyISAM' );

$dict = NewDataDictionary( $db );

// table schema description
$flds = "
	id I(11) AUTO KEY,
	message_id I(11),
	genid I(11),
	recipients C(255),
	message X,
	sent I(1) DEFAULT 0,
	status X, 
	actif I(1) DEFAULT 1,
	ar I(1) DEFAULT 0,
	relance I(1) DEFAULT 0,
	timbre I(11)";
	$sqlarray = $dict->CreateTableSQL( cms_db_prefix()."module_messages_recipients", $flds, $taboptarray);
	$dict->ExecuteSQLArray($sqlarray);			
//

$this->SetPreference('LastSendMessage', time());
$this->SetPreference('LastRelanceMessages', time());
$this->SetPreference('pageid_messages', '');
//Permissions
$this->CreatePermission('Messages use', 'Utiliser le module Message');
$this->CreatePermission('Messages Delete', 'Supprimer des messages');

// put mention into the admin log
$this->Audit( 0, 
	      $this->Lang('friendlyname'), 
	      $this->Lang('installed', $this->GetVersion()) );

	
	      
?>