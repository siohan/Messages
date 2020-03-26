<?php
#CMS - CMS Made Simple



class T2t_messages
{
  function __construct() {}

function details_message($message_id)
{
	$db = cmsms()->GetDb();
	$query = "SELECT id, sender, senddate, sendtime, replyto, group_id,recipients_number, subject, message, sent, priority, timbre, ar, relance, occurence FROM ".cms_db_prefix()."module_messages_messages WHERE id = ?";
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
			$details['priority'] = $row['priority'];
			$details['timbre'] = $row['timbre'];
			$details['ar'] = $row['ar'];
			$details['relance'] = $row['relance'];
			$details['occurence'] = $row['occurence'];
		}
	}
		return $details;
	

}
//ajoute un message	
function add_message($sender, $senddate, $sendtime, $replyto, $group_id,$recipients_number, $subject, $message, $sent, $priority, $timbre, $ar, $relance, $occurence)
{
	$db = cmsms()->GetDb();
	$query = "INSERT INTO ".cms_db_prefix()."module_messages_messages (sender, senddate, sendtime, replyto, group_id,recipients_number, subject, message, sent, priority, timbre, ar, relance, occurence) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
	$dbresult = $db->Execute($query, array($sender, $senddate, $sendtime, $replyto, $group_id,$recipients_number, $subject, $message, $sent, $priority, $timbre, $ar, $relance, $occurence));
	if($dbresult)
	{
		return true;
	}
	else
	{
		return false;
	}
}
//modifie un message existant

function update_message($record_id, $sender, $senddate, $sendtime, $replyto, $group_id,$recipients_number, $subject, $message, $sent, $priority, $timbre, $ar, $relance, $occurence)
{
	$db = cmsms()->GetDb();
	$query = "UPDATE ".cms_db_prefix()."module_messages_messages SET sender = ?, senddate = ?, sendtime = ?, replyto = ?, group_id = ?,recipients_number = ?, subject = ?, message = ?, sent = ?, priority = ?, timbre = ?, ar = ?, relance = ?, occurence = ? WHERE id = ?";
	$dbresult = $db->Execute($query, array($sender, $senddate, $sendtime, $replyto, $group_id,$recipients_number, $subject, $message, $sent, $priority, $timbre, $ar, $relance, $occurence, $record_id));
	if($dbresult)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function details_message_recipients($message_id, $genid)
{
	$db = cmsms()->GetDb();
	$query = "SELECT id, message_id, genid, recipients, sent, status, actif, ar, relance, timbre, message FROM ".cms_db_prefix()."module_messages_recipients WHERE message_id = ? AND genid = ?";
	$dbresult = $db->Execute($query, array($message_id, $genid));
	$details = array();
	if($dbresult)
	{
		while($row = $dbresult->FetchRow())
		{
			$details['id'] = $row['id'];
			$details['message_id'] = $row['message_id'];
			$details['genid'] = $row['genid'];
			$details['recipients'] = $row['recipients'];
			$details['sent'] = $row['sent'];
			$details['status'] = $row['status'];
			$details['actif'] = $row['actif'];
			$details['ar'] = $row['ar'];
			$details['relance'] = $row['relance'];
			$details['timbre'] = $row['timbre'];
			$details['message'] = $row['message'];
		}
	}
		return $details;
	

}
//remplit la table avec les messages
function add_messages_to_recipients($message_id, $genid, $recipients,$message,$sent,$status, $ar)
{
	$db = cmsms()->GetDb();
	$relance = 0;
	$timbre = time();
	$query = "INSERT INTO ".cms_db_prefix()."module_messages_recipients (message_id, genid, recipients,message,sent,status, ar, relance,timbre) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
	$dbresult = $db->Execute($query, array($message_id, $genid, $recipients,$message,$sent,$status, $ar, $relance, $timbre));
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
//modifie un message déjà envoyé à un destinataire//incrémente la relance
function update_recipients_message($message_id, $genid)
{
	$db = cmsms()->GetDb();
	$timbre = time();
	$query = "UPDATE ".cms_db_prefix()."module_messages_recipients SET relance = relance+1 , timbre = ?  WHERE message_id = ? AND genid = ?";
	$dbresult = $db->Execute($query, array($timbre, $message_id, $genid));
	if($dbresult)
	{
		return true;
	}
	else
	{
		return false;
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
//pour indiquer qu'un message a été envoyé
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
	$timbre = time();
	$query = "UPDATE ".cms_db_prefix()."module_messages_recipients SET sent = 1, status = 'Messagerie interne', relance = relance+1, timbre = ? WHERE message_id = ? AND recipients LIKE ?";
	$dbresult = $db->Execute($query, array($timbre,$message_id, $recipients));
	if($dbresult)
	{
		return true;
	}
	else
	{
		return false;
	}
}
//vérifie si ce message à déjà été envoyé à ce destinataire
function is_already_sent($message_id, $genid)
{
	$db = cmsms()->GetDb();
	$query = "SELECT * FROM ".cms_db_prefix()."module_messages_recipients WHERE message_id = ? AND genid = ?";
	$dbresult = $db->Execute($query, array($message_id, $genid));
	if($dbresult && $dbresult->RecordCount()>0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
//variante pour le fichier mass_action
//indique que l'email a été envoyé
function sent_to_recipients($message_id)
{
	$db = cmsms()->GetDb();
	$query = "UPDATE ".cms_db_prefix()."module_messages_recipients SET sent = 1 WHERE id = ?";
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
//variante pour le fichier mass_action
//indique que l'email n'a  pas été envoyé
function not_sent_to_recipients($message_id)
{
	$db = cmsms()->GetDb();
	$query = "UPDATE ".cms_db_prefix()."module_messages_recipients SET sent = 0, ar = 0 WHERE id = ?";
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
	if($dbresult && $dbresult->recordCount() >0)
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
function ar($message_id)
{
	$db = cmsms()->GetDb();
	$query = "UPDATE ".cms_db_prefix()."module_messages_recipients SET ar = 1, sent = 1 WHERE id = ?";
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
//marque un message pour un seul destinataire comme non lu
function not_ar($message_id)
{
	$db = cmsms()->GetDb();
	$query = "UPDATE ".cms_db_prefix()."module_messages_recipients SET ar = 0 WHERE id = ?";
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
//met "marqué comme lu" ar=1 en bdd pour tous les destinataires d'un message
function ar_all($message_id)
{
	$db = cmsms()->GetDb();
	$query = "UPDATE ".cms_db_prefix()."module_messages_recipients SET ar = 1 WHERE message_id = ?";
	$dbresult = $db->Execute($query, array($message_id));
	
	
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