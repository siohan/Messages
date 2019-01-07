<?php
//ce fichier fait des actions de masse, il est appelé depuis l'onglet de récupération des infos sur les joueurs
if( !isset($gCms) ) exit;
//debug_display($params, 'Parameters');
//var_dump($params['sel']);
$db =& $this->GetDb();
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
			$this->SetMessage('Joueurs désactivés');
			$this->RedirectToAdminTab('joueurs');
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