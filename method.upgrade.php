<?php
#-------------------------------------------------------------------------
# Module: Messages
# Version: 0.3.1
# Method: Upgrade
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

$db = $this->GetDb();			/* @var $db ADOConnection */
$dict = NewDataDictionary($db); 	/* @var $dict ADODB_DataDict */
/**
 * After this, the code is identical to the code that would otherwise be
 * wrapped in the Upgrade() method in the module body.
 */
$now = trim($db->DBTimeStamp(time()), "'");
$current_version = $oldversion;
switch($current_version)
{
  // we are now 1.0 and want to upgrade to latest
 
	
	case "0.1" : 	
	
	{
		$this->SetPreference('LastSendMessage', time());
	}
	case "0.2" :
	{
		//on créé un nouveau champ genid I(11)
		$dict = NewDataDictionary( $db );
		$flds = "genid I(11)";
		$sqlarray = $dict->AddColumnSQL( cms_db_prefix()."module_messages_recipients", $flds);
		$dict->ExecuteSQLArray($sqlarray);
		
		//on remplace les licences par le genid
		$query = "SELECT adh.genid, cont.licence FROM ".cms_db_prefix()."module_adherents_adherents AS adh, ".cms_db_prefix()."module_messages_recipients AS cont WHERE adh.licence = cont.licence";
		$dbresult = $db->Execute($query);
		if($dbresult)
		{
			while($row = $dbresult->FetchRow())
			{
				$genid = $row['genid'];
				$query2 = "UPDATE ".cms_db_prefix()."module_messages_recipients SET genid = ? WHERE licence = ?";
				$dbresult2 = $db->Execute($query2, array($genid, $row['licence']));
			
			}
		}
	}
	case "0.3" :
	{
		$dict = NewDataDictionary( $db );
		$flds = "actif I(1) DEFAULT 1";
		$sqlarray = $dict->AddColumnSQL( cms_db_prefix()."module_messages_recipients", $flds);
		$dict->ExecuteSQLArray($sqlarray);
	}
	case "0.3.1":
	{
		$dict = NewDataDictionary($db);
		$flds = "priority I(1) DEFAULT 3, timbre I(11), ar I(1) DEFAULT 0, relance I(1) DEFAULT 0, occurence I(11) DEFAULT 0";
		$sqlarray = $dict->AddColumnSQL( cms_db_prefix()."module_messages_messages", $flds);
		$dict->ExecuteSQLArray($sqlarray);
		
		$dict = NewDataDictionary($db);
		$flds = "relance I(1) DEFAULT 0, timbre I(11)";
		$sqlarray = $dict->AddColumnSQL( cms_db_prefix()."module_messages_recipients", $flds);
		$dict->ExecuteSQLArray($sqlarray);
		
		
		$this->SetPreference('pageid_messages', '');
		$this->SetPreference('LastRelanceMessages', time());
		
		# Mails templates
		$fn = cms_join_path(dirname(__FILE__),'templates','orig_tpl_messages.tpl');
		if( file_exists( $fn ) )
		{
			$template = file_get_contents( $fn );
			$this->SetTemplate('messages_template',$template);
		}
	}
		
	

}
// put mention into the admin log
$this->Audit( 0, 
	      $this->Lang('friendlyname'), 
	      $this->Lang('upgraded', $this->GetVersion()));

//note: module api handles sending generic event of module upgraded here
?>