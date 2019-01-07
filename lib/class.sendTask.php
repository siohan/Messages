<?php
class sendTask implements CmsRegularTask
{

   public function get_name()
   {
      return get_class();
   }

   public function get_description()
   {
      return 'Envoi des messages programmés';
   }

   public function test($time = '')
   {

      // Instantiation du module
      $mess = cms_utils::get_module('Messages');
	//$interval =  $mess->GetPreference('LastSendMessage');

      // Récupération de la dernière date d'exécution de la tâche
      if (!$time)
      {
         $time = time();
      }

      $last_execute = $mess->GetPreference('LastSendMessage');
      
      // Définition de la périodicité de la tâche (24h ici)
      if( $last_execute >= ($time - 900) ) return FALSE; // hardcoded to 15 minutes
      {
         return TRUE;
      }
      
      return FALSE;
      
   }

   public function execute($time = '')
   {

      	if (!$time)
      	{
         	$time = time();
      	}

      	$mess = cms_utils::get_module('Messages');
      
      	// Ce qu'il y a à exécuter ici
	
	$db = cmsms()->GetDb();
	
	$query = "SELECT id ,sender, replyto, senddate, sendtime, subject, message,group_id, sent FROM ".cms_db_prefix()."module_messages_messages WHERE sent = 0 AND UNIX_TIMESTAMP(CONCAT_WS(' ',senddate, sendtime)) < UNIX_TIMESTAMP()";
	
      	$dbresult = $db->Execute($query);
	//on a donc les n licences pour faire la deuxième requete
	//on commence à boucler
	if($dbresult && $dbresult->RecordCount()>0)  //la requete est ok et il y a des résultats
	{
		//on instancie la classe
		
		
		$mess_ops = new T2t_messages;

		while($row = $dbresult->FetchRow())
		{	
		//	on sort les variables
			$sender = $row['sender'];
			$replyto = $row['replyto'];
			$group_id = $row['group_id'];
			$mess_id = $row['id'];
			$envoi = $mess_ops->sent_message($mess_id);
			
			$gp_ops = new groups;
			$recipients_number = $gp_ops->count_users_in_group($group_id);
			
			$message = $row['message'];
			$subject = $row['subject'];
		//	$sender = $row['sender'];
		
			$contacts_ops = new contact;
			$adherents = $contacts_ops->UsersFromGroup($group_id);
			var_dump($adherents);
		
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
				}
				
			}	
			
			
			foreach($destinataires as $item=>$v)
			{

			//var_dump($item);
				$priority = 3;
				$cmsmailer = new \cms_mailer();
				$cmsmailer->reset();
				$cmsmailer->SetFrom($sender);//$this->GetPreference('admin_email'));
				$cmsmailer->AddAddress($v,$name='');
				$cmsmailer->IsHTML(false);
				$cmsmailer->SetPriority($priority);
				$cmsmailer->SetBody($message);
				$cmsmailer->SetSubject($subject);
				$cmsmailer->Send();
		                if( !$cmsmailer->Send() ) 
				{			
		                    	$mess_ops->not_sent_emails($mess_id, $v);
					$this->Audit('',$this->GetName(),'Problem sending email to '.$v);

		                }
				else
				{
					$mess_ops->sent_email($mess_id,$v);
				}
			}
		}
		return true; // Ou false si ça plante	
	}
	else
	{
		return false;
	}
	//return true; // Ou false si ça plante
	
	
	

      

   }

   public function on_success($time = '')
   {

      if (!$time)
      {
         $time = time();
      }
      
      $mess = cms_utils::get_module('Messages');
      $mess->SetPreference('LastSendMessage', $time);
      $mess->Audit('','Messages','Messages Ok');
      
   }

   public function on_failure($time = '')
   {
      $mess = cms_utils::get_module('Messages');
	$mess->Audit('','Messages','Messages KO');
   }

}
?>