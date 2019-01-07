<?php

if(!isset($gCms)) exit;
//on vérifie les permissions
if(!$this->CheckPermission('Message use'))
{
	echo $this->ShowErrors($this->Lang('needpermission'));
	return;
}
$db = cmsms()->GetDb();
global $themeObject;
//debug_display($params, 'Parameters');
if(isset($params['record_id']) && $params['record_id'] != '')
{
	$message_id = $params['record_id'];
	$mess_ops = new T2t_messages;
	$details = $mess_ops->details_message($message_id);
	//var_dump($details);
	//on envoie le message
	$gp_ops = new groups;
	$recipients_number = $gp_ops->count_users_in_group($details['group_id']);
	
	$contacts_ops = new contact;
	$adherents = $contacts_ops->UsersFromGroup($details['group_id']);

//	var_dump($adherents);
	foreach($adherents as $sels)
	{
		//avant on envoie dans le module emails pour tous les utilisateurs et sans traitement

		$query = "SELECT contact FROM ".cms_db_prefix()."module_adherents_contacts WHERE licence = ? AND type_contact = 1 LIMIT 1";
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

		echo $status.'<br />';	

	
		$add_to_recipients = $mess_ops->add_messages_to_recipients($message_id, $sels, $email_contact,$senttouser,$status, $ar);
	//	var_dump($add_to_recipients);
	}
	foreach($destinataires as $item=>$v)
	{

	//var_dump($item);

		$cmsmailer = new \cms_mailer();
		$cmsmailer->reset();
		$cmsmailer->SetFrom($details['sender']);//$this->GetPreference('admin_email'));
		$cmsmailer->AddAddress($v,$name='');
		$cmsmailer->IsHTML(false);
		$cmsmailer->SetPriority($priority);
		$cmsmailer->SetBody($details['message']);
		$cmsmailer->SetSubject($details['subject']);
		$cmsmailer->Send();
                if( !$cmsmailer->Send() ) 
		{			
                    	$mess_ops->not_sent_emails($message_id, $recipients);
			$this->Audit('',$this->GetName(),'Problem sending email to '.$item);

                }
	}
}
else
{
	//message + redirection
}