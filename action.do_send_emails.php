<?php

if(!isset($gCms)) exit;
//on vérifie les permissions
if(!$this->CheckPermission('Messages use'))
{
	echo $this->ShowErrors($this->Lang('needpermission'));
	return;
}
$db = cmsms()->GetDb();
global $themeObject;
//debug_display($params, 'Parameters');
$error = 0;
if(isset($params['group']) && $params['group'])
{
	$group_id = $params['group'];
}
else
{
	$error++;
}
if(isset($params['from']) && $params['from'])
{
	$sender = $params['from'];
}
else
{
	$error++;
}
if(isset($params['priority']) && $params['priority'])
{
	$priority = $params['priority'];
}
else
{
	$error++;
}
if(isset($params['sujet']) && $params['sujet'])
{
	$subject = $params['sujet'];
}
else
{
	$error++;
}

if(isset($params['message']) && $params['message'])
{
	$message = $params['message'];
}	
else
{
	$error++;
}
$aujourdhui = time();
//var_dump($aujourdhui);
if(isset($params['senddate']) && $params['senddate'])
{
	$senddate = $params['senddate'];
}
else
{
	$senddate = date('Y-m-d');
}
if(isset($params['sendtime']) && $params['sendtime'])
{
	$sendtime = $params['sendtime'];
}
else
{
	$sendtime = date("H:i:s");
}
$mess_ops = new T2t_messages;
//transforme la date en time unix
$time_envoi = $mess_ops->datetotimeunix($senddate, $sendtime);
//var_dump($time_envoi);
$sent = 1;
if($time_envoi > $aujourdhui)
{
	$sent = 0;
}

if($error >0)
{
	//pas glop, des erreurs !
	echo "trop d\'erreurs !";
}
else
{
	// on commence le traitement
	//if($aujourdhui <= $senddate = date('Y-m-d');
	
	$replyto = $sender;
	
	$gp_ops = new groups;
	$recipients_number = $gp_ops->count_users_in_group($group_id);
	$mess_ops = new T2t_messages;
	$mess = $mess_ops->add_message($sender, $senddate, $sendtime, $replyto, $group_id,$recipients_number, $subject, $message, $sent);
	$message_id =$db->Insert_ID();
//	var_dump($message_id);
	
	if ($sent == 1)
	{
		//on extrait les utilisateurs (licence) du groupe sélectionné
		$contacts_ops = new contact;
		$adherents = $contacts_ops->UsersFromGroup($group_id);

	//	var_dump($adherents);
		foreach($adherents as $sels)
		{
			//avant on envoie dans le module emails pour tous les utilisateurs et sans traitement

			$query = "SELECT contact FROM ".cms_db_prefix()."module_adherents_contacts WHERE genid = ? AND type_contact = 1 LIMIT 1";
			$dbresult = $db->Execute($query, array($sels));
			$row = $dbresult->FetchRow();

			$email_contact = $row['contact'];
			//var_dump($email_contact);

			if(!is_null($email_contact))
			{
				$destinataires[] = $email_contact;
					$senttouser = 1;
					$status = "Email Ok";
					$ar = 0;
			}
			else
			{
				//on indique l'erreur : pas d'email disponible !
					$senttouser = 0;
					$status = "Email absent";
					$ar = 0;
					$email_contact = "rien";
			}

	
			$add_to_recipients = $mess_ops->add_messages_to_recipients($message_id, $sels, $email_contact,$senttouser,$status, $ar);
			//on crée une url pour l'accusé de réception
			$retourid = $this->GetPreference('pageid_message');
			$page = $cg_ops->resolve_alias_or_id($retourid);
			$lien = $this->create_url($id,'default',$page, array("message_id"=>$message_id, "genid"=>$sels));
		}

			foreach($destinataires as $item=>$v)
			{

			//var_dump($v);

				$cmsmailer = new \cms_mailer();
				$cmsmailer->reset();
			//	$cmsmailer->SetFrom($sender);//$this->GetPreference('admin_email'));
				$cmsmailer->SetSMTPDebug($flag = TRUE);
				$cmsmailer->AddAddress($v,$name='');
				$cmsmailer->IsHTML(false);
				$cmsmailer->SetPriority($priority);
				$cmsmailer->SetBody($message);
				$cmsmailer->SetSubject($subject);
				$cmsmailer->Send();
		                if( !$cmsmailer->Send() ) 
				{			
		                    	$mess_ops->not_sent_emails($message_id, $recipients);
					$this->Audit('',$this->GetName(),'Problem sending email to '.$item);

		                }
			
			}
	}
	
	

	

}
$this->RedirectToAdminTab('mess');
