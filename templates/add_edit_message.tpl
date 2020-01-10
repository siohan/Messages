{if $edit == 0}
  <h3>Ajout d'un nouveau message</h3>
{else}
  <h3>Modification d'un message</h3>
{/if}
{form_start}
<div class="c_full cf">
 
  {if $edit > 0}
  <input type="submit" name="apply" value="Modifier"/>
  <input type="submit" name="submitasnew" value="Enregistrer comme nouveau">
  <input type="hidden" name="edit" value="1">
  <input type="hidden" name="record_id" value="{$record_id}">
  {else}
  <input type="submit" name="submit" value="Envoyer"/>
  <input type="hidden" name="edit" value="0">
  {/if}
  <input type="submit" name="cancel" value="Annuler" formnovalidate/>
</div>
{tab_header name="details" label="Expéditeur"}
{tab_header name="content" label="Contenu"}
{tab_header name="envoi" label="Envoi programmé"}
{tab_header name="divers" label="Accusé, Relance..."}
{tab_start name="details"}
<div class="c_full cf">
	<label class="grid_3">Expéditeur</label>
	<div class="pageinput"><input type="text" name="from" value="{$from}" /></div>
</div>
<div class="c_full cf">
	<label class="grid_3">Destinataires</label>
	<div class="pageinput"><select name="group">{html_options options=$liste_groupes selected=$group_id}</select></div>

</div>
{tab_start name="content"}


	<div class="c_full cf">
		<label class="grid_3">Priorité du message</label>
		<div class="grid_8"><select name="priority">{html_options options=$liste_priorities selected=$priority}</select></div>
	</div>

	<div class="c_full cf">
		<label class="grid_3">Sujet du message</label>
		<div class="grid_8"><input type="text" name="subject" value="{$subject}" /></div>
	</div>

	<div class="c_full cf">
		<label class="grid_3">Message:</label>
		<div class="grid_8">{cms_textarea name=message rows="5" cols="20" enablewysiwyg=1 text=$message value=$message placeholder="Votre message ici"}
			</div>
	</div>

{tab_start name="envoi"}
	<div class="c_full cf">
		<label class="grid_3">Date et heure d'envoi</label>
		<div class="grid_8">{html_select_date start_year='2019' end_year='+10' time=$timbre}@ {html_select_time time=$timbre display_seconds=false}
	</div>
	</div>
{tab_start name="divers"}
<div class="c_full cf">
	<label class="grid_3">Accusé de réception du message</label>
	<div class="grid_8"><select name="ar">{html_options options=$OuiNon selected=$ar}</select></div>
</div>
<div class="c_full cf">
	<label class="grid_3">Relance si pas de réponse</label>
	<div class="grid_8"><select name="relance">{html_options options=$OuiNon selected=$relance}</select></div>
</div>
<div class="c_full cf">
	<label class="grid_3">Temps entre deux relances</label>		
	<div class="grid_8"><input type="text" name="result" value="{$result}"><select name="unite">{html_options options=$liste_unite selected=$unite}</select></div>
	</div>
</div>
{*
<div class="c_full cf">
	<label class="grid_3">Ajouter une pièce jointe</label>		
	<div class="pageinput"><input type="file" name="file1" value="{$file1}"></div>
	</div>
</div>
*}
</div>
{tab_end}

{form_end}
