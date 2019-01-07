<?php
#CMS - CMS Made Simple



class T2t_messages
{
  function __construct() {}

function details_message($message_id)
{
	$db = cmsms()->GetDb();
	$query = "SELECT id, sender, senddate, sendtime, replyto, group_id,recipients_number, subject, message, sent FROM ".cms_db_prefix()."module_messages_messages WHERE id = ?";
	$dbresult = $db->Execute($query, array($message_id));
	$details = array();
	if($dbresult)
	{
		while($row = $dbresult->FetchRow())
		{
			$details['message_id'] = $row['id'];
			$details['sender'] = $row['sender'];
			$details['senddate'] = $row['senddate'];
			$details['sendtime'] = $row['sendtime'];
			$details['replyto'] = $row['replyto'];
			$details['group_id'] = $row['group_id'];
			$details['recipients_number'] = $row['recipients_number'];
			$details['subject'] = $row['subject'];
			$details['message'] = $row['message'];
			$details['sent'] = $row['sent'];
		}
	}
		return $details;
	

}	
function add_message($sender, $senddate, $sendtime, $replyto, $group_id,$recipients_number, $subject, $message, $sent)
{
	$db = cmsms()->GetDb();
	$query = "INSERT INTO ".cms_db_prefix()."module_messages_messages (sender, senddate, sendtime, replyto, group_id,recipients_number, subject, message, sent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
	$dbresult = $db->Execute($query, array($sender, $senddate, $sendtime, $replyto, $group_id,$recipients_number, $subject, $message, $sent));
	if($dbresult)
	{
		return true;
	}
	else
	{
		return false;
	}
}

//remplit la table avec les messages
function add_messages_to_recipients($message_id, $genid, $recipients,$sent,$status, $ar)
{
	$db = cmsms()->GetDb();
	$query = "INSERT INTO ".cms_db_prefix()."module_messages_recipients (message_id, genid, recipients,sent,status, ar) VALUES (?, ?, ?, ?, ?, ?)";
	$dbresult = $db->Execute($query, array($message_id, $genid, $recipients,$sent,$status, $ar));
	if($dbresult)
	{
		return true;
	}
	else
	{
		$mess = $db->ErrorMsg();
		return $mess;
	}
}
//indique que le message n'a pas été envoyé erreur email
function not_sent_emails($message_id, $recipients)
{
	$db = cmsms()->GetDb();
	$query = "UPDATE ".cms_db_prefix()."module_messages_recipients SET sent = 0, status = 'Echec envoi' WHERE message_id = ? AND recipients LIKE ?";
	$dbresult = $db->Execute($query, array($message_id, $recipients));
	if($dbresult)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function sent_message($message_id)
{
	$db = cmsms()->GetDb();
	$query = "UPDATE ".cms_db_prefix()."module_messages_messages SET sent = 1 WHERE id = ?";
	$dbresult = $db->Execute($query, array($message_id));
	if($dbresult)
	{
		return true;
	}
	else
	{
		return false;
	}
}
//indique que le message est visible dans l'espace privé (pas forcément envoyé par email : email absent, incorrect...)
function sent_email($message_id, $recipients)
{
	$db = cmsms()->GetDb();
	$query = "UPDATE ".cms_db_prefix()."module_messages_recipients SET sent = 1, status = 'Messagerie interne' WHERE message_id = ? AND recipients LIKE ?";
	$dbresult = $db->Execute($query, array($message_id, $recipients));
	if($dbresult)
	{
		return true;
	}
	else
	{
		return false;
	}
}
//calcule le nombre d'erreurs sur un message (non envoyé, pb à l'envoi, etc...)
function count_errors_per_message($message_id)
{
	$db = cmsms()->GetDb();
	$query = "SELECT count(*) as errors FROM ".cms_db_prefix()."module_messages_recipients WHERE sent = 0 AND message_id= ?";
	$dbresult = $db->Execute($query, array($message_id));
	if($dbresult)
	{
		$row = $dbresult->FetchRow();
		$nb_errors = $row['errors'];//$dbresult->RecordCount();
		return $nb_errors;
	}
}
//calcule le nombre d'accusé de réception (ar) par messages
function count_ar_per_message($message_id)
{
	$db = cmsms()->GetDb();
	$query = "SELECT count(*) as errors FROM ".cms_db_prefix()."module_messages_recipients WHERE ar = 1 AND message_id= ?";
	$dbresult = $db->Execute($query, array($message_id));
	if($dbresult)
	{
		$row = $dbresult->FetchRow();
		$nb_ar = $row['errors'];//$dbresult->RecordCount();
		return $nb_ar;
	}
}
function nb_messages_per_user($genid)
{
	$db = cmsms()->GetDb();
	$query = "SELECT count(*) AS nb FROM ".cms_db_prefix()."module_messages_recipients WHERE genid = ? AND ar < 1 AND actif = 1";
	$dbresult = $db->Execute($query, array($genid));
	if($dbresult)
	{
		$row = $dbresult->FetchRow();
		$nb_messages = $row['nb'];//$dbresult->RecordCount();
		return $nb_messages;
	}
	else
	{
		return false;
	}
}
//met "marqué comme lu" ar=1 en bdd
function ar($message_id, $genid)
{
	$db = cmsms()->GetDb();
	$query = "UPDATE ".cms_db_prefix()."module_messages_recipients SET ar = 1 WHERE id = ? AND genid = ?";
	$dbresult = $db->Execute($query, array($message_id, $genid));
	
	
}
//envoie un mail

//convertit une date de la forme Y-m-d H:i vers un int temps unix
function datetotimeunix($senddate, $sendtime)
{
	$day = (int) substr($senddate, 8,2);
	$month = (int) substr($senddate, 5,2);
	$year = (int) substr($senddate, 0,4);
	$hour = (int) substr($sendtime, 0,2);
	$min = (int) substr($sendtime, 3,2);
	$seconds = 0;
	$intdate = mktime($hour,$min, $seconds, $month, $day, $year);
	return $intdate;
}
//supprime un message
function delete_message($message_id)
{
	$db = cmsms()->GetDb();
	$query = "DELETE FROM ".cms_db_prefix()."module_messages_messages WHERE id = ?";
	$dbresult = $db->Execute($query, array($message_id));
	if($dbresult)
	{
		return true;
	}
	else
	{
		return false;
	}
}
//on supprime les recipients du message_id
function delete_recipients($message_id)
{
	$db = cmsms()->GetDb();
	$query = "DELETE FROM ".cms_db_prefix()."module_messages_recipients WHERE message_id = ?";
	$dbresult = $db->Execute($query, array($message_id));
	if($dbresult)
	{
		return true;
	}
	else
	{
		return false;
	}
}
##

##
#END OF CLASS
}