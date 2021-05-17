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
//debug_display($_POST, 'Parameters');
$edit = 0;
if(!empty($_POST))
{
	if(isset($_POST['cancel']))
	{
		$this->RedirectToAdminTab();
	}
	elseif(isset($_POST['submitasnew']) )
	{
		$edit = 0;
	}
	elseif(isset($_POST['apply']))
	{
		$edit = 0;
		
		
		if(isset($_POST['edit']) && $_POST['edit'] !='')
		{
			$edit = $_POST['edit'];
		}
		
	}
	$error = 0;
	$mess_ops = new T2t_messages;
	$cont_ops = new contact;
	$cg_ops = new CGExtensions;
	
		if(isset($_POST['record_id']) && $_POST['record_id'] !='')
		{
			$record_id = $_POST['record_id'];
		}
		if(isset($_POST['from']) && $_POST['from'] !='')
		{
			$sender = $_POST['from'];
		}
		if(isset($_POST['group']) && $_POST['group'] !='')
		{
			$group_id = $_POST['group'];
		}
		else
		{
			$error++;
		}
		if(isset($_POST['priority']) && $_POST['priority'] !='')
		{
			$priority = $_POST['priority'];
		}
		if(isset($_POST['Date_Month']) && $_POST['Date_Month'] !='')
		{
			$Date_Month = $_POST['Date_Month'];
		}
		if(isset($_POST['Date_Day']) && $_POST['Date_Day'] !='')
		{
			$Date_Day = $_POST['Date_Day'];
		}
		if(isset($_POST['Date_Year']) && $_POST['Date_Year'] !='')
		{
			$Date_Year = $_POST['Date_Year'];
		}
		if(isset($_POST['Time_Hour']) && $_POST['Time_Hour'] !='')
		{
			$Time_Hour = $_POST['Time_Hour'];
		}
		if(isset($_POST['Time_Minute']) && $_POST['Time_Minute'] !='')
		{
			$Time_Minute = $_POST['Time_Minute'];
		}
		if(isset($_POST['Time_Second']) && $_POST['Time_Second'] !='')
		{
			$Time_Second = $_POST['Time_Second'];
		}
		else
		{
			$Time_Second = "00";
		}
		if(isset($_POST['ar']) && $_POST['ar'] !='')
		{
			$ar_message = $_POST['ar'];
		}
		if(isset($_POST['relance']) && $_POST['relance'] !='')
		{
			$relance = $_POST['relance'];
		}
		if(isset($_POST['result']) && $_POST['result'] !='')
		{
			$result = $_POST['result'];
		}
		if(isset($_POST['unite']) && $_POST['unite'] !='')
		{
			$unite = $_POST['unite'];
		}
		if($unite == 'Heures')
		{
			$coeff = 3600;
		}
		else
		{
			$coeff = 3600*24;
		}
		$occurence = $coeff*$result;
		if(isset($_POST['subject']) && $_POST['subject'] !='')
		{
			$subject = $_POST['subject'];
		}
		else
		{
			$error++;
		}
		if(isset($_POST['message']) && $_POST['message'] !='')
		{
			$message = $_POST['message'];
		}
		/*
		else
		{
			$error++;
		}
		*/

		//echo 'le nb error est :'.$error;
		if($error == 0)
		{
			$gp_ops = new groups;
			$recipients_number = $gp_ops->count_users_in_group($group_id);
			$timbre = mktime($Time_Hour,$Time_Minute, $Time_Second,$Date_Month, $Date_Day,$Date_Year);
			$replyto = $sender;
			$senddate = $Date_Year.'-'.$Date_Month.'-'.$Date_Day;
			$sendtime = $Time_Hour.':'.$Time_Minute.':'.$Time_Second;
			if($timbre > time())
			{
				//on envoie en différé
				$sent = 0;
			}
			else
			{
				$sent = 1;	
			}

			if($edit == 0)
			{
				$mess = $mess_ops->add_message($sender, $senddate, $sendtime, $replyto, $group_id,$recipients_number, $subject, $message, $sent, $priority, $timbre, $ar_message, $relance, $occurence);
				$message_id =$db->Insert_ID();
				if(true == $mess)
				{
					$this->SetMessage('Message ajouté !');
				}
				//on envoie ou pas ?
				//on regarde si le message est programmé (scheduled)
				$retourid = $this->GetPreference('pageid_messages');
				$page = $cg_ops->resolve_alias_or_id($retourid);
				$montpl = $this->GetTemplateResource('tpl_messages.tpl');						
				
				
				$query = "SELECT genid FROM ".cms_db_prefix()."module_adherents_groupes_belongs WHERE id_group = ?";
				$dbresult = $db->Execute($query, array($group_id));
				if($dbresult && $dbresult->recordCount() >0)
				{
					$smarty = cmsms()->GetSmarty();
					$cmsmailer = new \cms_mailer();
					$tpl = $smarty->createTemplate($montpl);
					$tpl->assign('ar', $ar_message);
					$tpl->assign('relance', $relance);
					$tpl->assign('occurence', $occurence);
					$tpl->assign('message',$message);
					
					while($row = $dbresult->FetchRow())
					{
						//on ajoute le message à chaque membre du groupe d'abord
						$email_contact = $cont_ops->email_address($row['genid']);
						if (false == $email_contact)
						{
							$email_contact = 'No email';
						}
						$senttouser = 0;
						$status = 0;
						$ar = 0;
						
						$add_to_recipients = $mess_ops->add_messages_to_recipients($message_id, $row['genid'], $email_contact,$message,$senttouser,$status, $ar);
						if(false != $email_contact)
						{
							//On peut envoyer
							$lien = $this->create_url($id,'default',$page, array("message_id"=>$message_id, "genid"=>$row['genid']));
							// do not assign data to the global smarty
							
							$tpl->assign('lien',$lien);
							$output = $tpl->fetch();
							try
							{
								$cmsmailer->SetFromName($sender);//$this->GetPreference('admin_email'));
								$cmsmailer->AddAddress($email_contact);
								$cmsmailer->IsHTML(true);
								$cmsmailer->SetPriority($priority);
								$cmsmailer->SetBody($output);
								$cmsmailer->SetSubject($subject);
								$cmsmailer->Send();
								$mess_ops->sent_email($message_id, $row['genid']);
							}
							catch (phpmailerException $e) 
							{
  								
  								$status = $e->errorMessage();
								$mess_ops->not_sent_emails($message_id, $row['genid'], $status);
							} 
							catch (Exception $e) 
							{
  								echo $e->getMessage(); //Boring error messages from anything else!
							}
							unset($email_contact);
						}
						else
						{
							//pas d'email, on ne peut pas envoyer
						}
						
						
						
					}
				}	
			
			}
			else
			{
				$mess = $mess_ops->update_message($record_id,$sender, $senddate, $sendtime, $replyto, $group_id,$recipients_number, $subject, $message, $sent, $priority, $timbre, $ar_message, $relance, $occurence);
			}
			$this->RedirectToAdminTab('messages');
		}

	
}
else
{
	//debug_display($params,'Parameters');
	//On affiche un formulaire qui est soit vierge ($edit == 0) soit non ($edit == 1)
	//on met les valeurs par défaut
	$edit = 0;
	$record_id = 0;
	$priority = 3;
	$group_id = 1;
	$sender = "";
	$aujourdhui = date('Y-m-d');
	$adh_ops = new groups;
	$group = $adh_ops->liste_groupes_dropdown();
	$destinataires = array();
	$senddate = date('Y-m-d');
	$sendtime = date('H:i');
	$subject = "";
	$message = "";
	$replyto = "";
	$timbre = time();
	$ar = 0;
	$relance = 0;
	$liste_unite = array('Heures'=>'Heures', 'Jours'=>'Jours');
	
	$occurence = 0;
	$result = 0;
	$unite = 'Jours';
	$OuiNon = array('1'=>'Oui','0'=>'Non');
	$liste_priorities = array("1"=>"Haute", "3"=>"Normale", "5"=>"Basse");
	if(isset($params['record_id']) && $params['record_id'] != '')
	{
		//on va chercher les détails du message
		$record_id = $params['record_id'];
		$edit = 1;
		$mess_ops = new T2t_messages;
		$details = $mess_ops->details_message($record_id);
		$message_id = $details['message_id'];
		$sender = $details['sender'];
		$replyto = $details['replyto'];
		$group_id = $details['group_id'];
		$details['recipients_number'];
		$subject = $details['subject'];
		$message = $details['message'];
		$sent = $details['sent'];
		$priority = $details['priority'];
		$timbre = $details['timbre'];
		$ar = $details['ar'];
		$relance = $details['relance'];
		$occurence = $details['occurence'];
	}
	if($occurence >0)
	{
		if(true == is_float($occurence/86400))
		{
			//on met le résultat en heures
			$result = $occurence/3600;
			$unite = 'Heures';
		
		}
		else
		{
			//on met le résultat en jours
			$result = $occurence/86400;
			$unite = 'Jours';
		
		}
	}

	$tpl = $smarty->CreateTemplate($this->GetTemplateResource('add_edit_message.tpl'), null, null, $smarty);
	$tpl->assign('edit', $edit);
	$tpl->assign('record_id', $record_id);
	$tpl->assign('from', $sender);
	$tpl->assign('senddate', $senddate);
	$tpl->assign('sendtime', $sendtime);
	$tpl->assign('replyto', $replyto);
	$tpl->assign('group_id', $group_id);
	$tpl->assign('subject', $subject);
	$tpl->assign('message', $message);
	$tpl->assign('timbre', $timbre);
	$tpl->assign('ar', $ar);
	$tpl->assign('relance', $relance);
	$tpl->assign('occurence', $occurence);
	
	$tpl->assign('result', $result);
	$tpl->assign('unite', $unite);
	$tpl->assign('OuiNon', $OuiNon);
	$tpl->assign('liste_groupes', $group);
	$tpl->assign('liste_priorities', $liste_priorities);
	$tpl->assign('liste_unite', $liste_unite);
	$tpl->assign('priority', $priority);
	$tpl->display();
		
}

?>