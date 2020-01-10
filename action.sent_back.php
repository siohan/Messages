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
if(isset($params['record_id']) && $params['record_id'] != '')
{
	$message_id = $params['record_id'];
	$mess_ops = new T2t_messages;
	$details = $mess_ops->details_message($message_id);
	//var_dump($details);
	$message = $details['message'];
	$ar_message = $details['ar'];
	$relance = $details['relance'];
	$occurence = $details['occurence'];
	$sender = $details['sender'];
	$subject = $details['subject'];
	$priority = $details['priority'];
	
	//on indique que le message est parti
	$mess_ops->sent_message($message_id);
	
	//on envoie le message
	$gp_ops = new groups;
	$recipients_number = $gp_ops->count_users_in_group($details['group_id']);
	
	$contacts_ops = new contact;
	$cg_ops = new CGExtensions;
	
	$adherents = $contacts_ops->UsersFromGroup($details['group_id']);

//	var_dump($adherents);
	foreach($adherents as $sels)
	{
		//avant on envoie dans le module emails pour tous les utilisateurs et sans traitement

			//avant on envoie dans le module emails pour tous les utilisateurs et sans traitement

			$query = "SELECT contact FROM ".cms_db_prefix()."module_adherents_contacts WHERE genid = ? AND type_contact = 1 LIMIT 1";
			$dbresult = $db->Execute($query, array($sels));
			if($dbresult)
			{
				$retourid = $this->GetPreference('pageid_messages');
				$page = $cg_ops->resolve_alias_or_id($retourid);

				if($dbresult->RecordCount()>0)
				{
					$montpl = $this->GetTemplateResource('tpl_messages.tpl');						
					$smarty = cmsms()->GetSmarty();
					$cmsmailer = new \cms_mailer();
					
					$ar = 0;
					while($row = $dbresult->FetchRow())
					{
						$email_contact = $row['contact'];
						if(!is_null($email_contact))
						{
							$status = "Email Ok";
							$lien = $this->create_url($id,'default',$page, array("message_id"=>$message_id, "genid"=>$sels));
							//on vérifie si le message a déjà été envoyé (update) ou non (insert)
							
							$senttouser = 0;
							
								// do not assign data to the global smarty
								$tpl = $smarty->createTemplate($montpl);
								$tpl->assign('lien',$lien);
								$tpl->assign('ar', $ar_message);
								$tpl->assign('relance', $relance);
								$tpl->assign('occurence', $occurence);
								$tpl->assign('message',$message);
							 	$output = $tpl->fetch();

								$cmsmailer->SetFromName($sender);//$this->GetPreference('admin_email'));
								$cmsmailer->AddAddress($email_contact);
								$cmsmailer->IsHTML(true);
								$cmsmailer->SetPriority($priority);
								$cmsmailer->SetBody($output);
								$cmsmailer->SetSubject($subject);
							//	$cmsmailer->AddAttachment();
								
								
						                if( !$cmsmailer->Send() ) 
								{			
						                    	$senttouser = 0;
									$this->Audit('',$this->GetName(),'Problem sending email to '.$v);
						                }
								else
								{
									$senttouser =1;
								}
								$cmsmailer->reset();
								$has_been_sent  = $mess_ops->is_already_sent($message_id, $sels);
								if(true == $has_been_sent)
								{
									$mess_ops->update_recipients_message($message_id, $sels);
								}
								else
								{
									$add_to_recipients = $mess_ops->add_messages_to_recipients($message_id, $sels, $email_contact,$senttouser,$status, $ar);
								}
								//$add_to_recipients = $mess_ops->add_messages_to_recipients($message_id, $sels, $email_contact,$senttouser,$status, $ar);
								unset($email_contact);
						}
						else
						{
								$status = "Email absent";
								$email_contact = "rien";
						}
					}
				}
			}
			









		
	}
	$this->RedirectToAdminTab('mess');
}
else
{
	//message + redirection
}