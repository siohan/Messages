{form_start action="admin_options_tab"}
<fieldset>
	<legend>Configuration principale</legend>
	<div class="pageoverflow">
		<p class="pagetext">Alias de la page du module{cms_help key='help_pageid_messages' title='Alias de la page des messages'}</p>
		<p class="pageinput"><input type="text" name="pageid_messages" value="{$pageid_messages}"></p>
	</div>

</fieldset>
<input type="submit" name="submit" value="Envoyer">
 <input type="submit" name="cancel" value="Annuler" formnovalidate/>
{form_end}
