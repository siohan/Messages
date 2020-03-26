<?php
class RelanceMessagesTask implements CmsRegularTask
{

	
	
	public function get_name()
   	{
      		return get_class();
   	}

   public function get_description() {}
   

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

      $last_execute = (int) $mess->GetPreference('LastRelanceMessages');
      
      // Définition de la périodicité de la tâche (24h ici)
      if( $time - $last_execute > 84600 ) return true; // hardcoded to 15 minutes
      return false;
      
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
	
	$query = "SELECT id AS rec_id, occurence, timbre, subject, message, priority FROM ".cms_db_prefix()."module_messages_messages WHERE relance = 1";
	
      	$dbresult = $db->Execute($query);
	//on a donc les n licences pour faire la deuxième requete
	//on commence à boucler
	if($dbresult && $dbresult->RecordCount()>0)  //la requete est ok et il y a des résultats
	{
		//on instancie la classe
		
		
		$mess_ops = new T2t_messages;
		$retourid = $mess->GetPreference('pageid_messages');
		$cg_ops = new CGExtensions;
		$page = $cg_ops->resolve_alias_or_id($retourid);
		

		while($row = $dbresult->FetchRow())
		{	
			$message_id = $row['rec_id'];
			$occurence = $row['occurence'];
			$timbre = $row['timbre'];
			$subject = $row['subject'];
			$message = $row['message'];
			$priority = $row['priority'];

			//on va chercher le dernier envoi
			//on refait une requete pour sélectionner les personnes n'ayant pas confirmé réception
			$query2 = "SELECT recipients, genid FROM ".cms_db_prefix()."module_messages_recipients WHERE message_id = ? AND ar = 0 AND timbre + ? < UNIX_TIMESTAMP()";
			$dbresult2 = $db->Execute($query2, array($message_id, $occurence));
		
			if($dbresult2 && $dbresult2->RecordCount() >0)
			{
				$mess_ops = new T2t_messages;
				while($row2 = $dbresult2->FetchRow())
				{
					 	
						$recipients = $row2['recipients'];
						$genid = $row2['genid'];
						$lien = $mess->create_url($id,'default',$page, array("message_id"=>$message_id, "genid"=>$genid));
						//$message.= '<p><strong>Merci de bien vouloir confirmer réception de ce message en cliquant sur le lien ci-après : <a href="'.$lien.'" >Je confirme réception</a></p>';
						//$message.= '<p><strong>Sans confirmation de réception, ce message se répétera.</strong></p>';
						//$priority = 3;
						$cmsmailer = new \cms_mailer();
						$cmsmailer->reset();
					//	$cmsmailer->SetFrom($sender);//$this->GetPreference('admin_email'));
						$cmsmailer->AddAddress($recipients,$name='');
						$cmsmailer->IsHTML(true);
						$cmsmailer->SetPriority($priority);
						$cmsmailer->SetBody($message);
						$cmsmailer->SetSubject($subject);
						
				                if( !$cmsmailer->Send() ) 
						{			
				                    	$mess_ops->not_sent_emails($message_id, $recipients);
							$this->Audit('',$this->GetName(),'Problem sending email to '.$recipients);

				                }
						else
						{
							$mess_ops->sent_email($message_id,$recipients);
						}
				}
				return true;
			}
			else
			{
				return false;
			}
		}
		
	
		
			
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
      $mess->Audit('','Messages','Relance Messages Ok');
      
   }

   public function on_failure($time = '')
   {
      $mess = cms_utils::get_module('Messages');
	$mess->Audit('','Messages','Pas de nouvelles relances de Messages');
   }

}
?>