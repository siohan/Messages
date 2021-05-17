<?php
//ce fichier fait des actions de masse, il est appelé depuis l'onglet de récupération des infos sur les joueurs
if( !isset($gCms) ) exit;
debug_display($params, 'Parameters');
//var_dump($params['sel']);
$db = cmsms()->GetDb();
if (isset($params['submit_massaction']) && isset($params['actiondemasse']) )
  {
     if( isset($params['sel']) && is_array($params['sel']) &&
	count($params['sel']) > 0 )
      	{
        	$mess_ops = new T2t_messages;
		switch($params['actiondemasse'])
		{
			case "delete" :
			foreach( $params['sel'] as $message_id )
	  		{
	    			$mess = $mess_ops->delete_message($message_id );
				if(true === $mess)
				{
					$mess_ops->delete_recipients($message_id);
				}
	  		}
			$this->SetMessage('Messages effacés');
			$this->RedirectToAdminTab('mess');
			break;
			
		
			
			//marque les messages comme lus
			case "read" :
			foreach( $params['sel'] as $message_id )
	  		{
	    			$mess = $mess_ops->ar($message_id);
				
	  		}
			$this->SetMessage('Marqués comme lus');
			$this->Redirect($id, 'show_recipients', $returnid, array("record_id"=>$record_id));
			break;
			
			//marque les messages comme non lus
			case "unread" :
			foreach( $params['sel'] as $message_id )
	  		{
	    			$mess = $mess_ops->not_ar($message_id);
				
	  		}
			$this->SetMessage('Marqués comme non lus');
			$this->Redirect($id, 'show_recipients', $returnid, array("record_id"=>$record_id));
			break;
			
			
			
	
      		}//fin du switch
  	}
	else
	{
		$this->SetMessage('PB de sélection de masse !!');
		$this->RedirectToAdminTab('recuperation');
	}
}
/**/
#
# EOF
#
?>