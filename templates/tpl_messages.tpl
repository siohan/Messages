<html>
  <head>
    <style>
      a {
         background-color: #4CAF50;
         border: none;
         color: white;
         padding: 5px 12px;
         text-align: center;
         text-decoration: none; 
         display: inline-block;
         font-size: 12px;
a:hover {
        background-color: #000000;
        border: 2px #FFFFFF solid;
        color: #FFFFFF;
      }
    </style>
  </head>
  <body>
      <table width="100%" cellspacing="0" cellpadding="10" border="0"> 
<tr colspan="2">
<td>{$message}</td>
</tr>
{if $ar == '1'}
<tr><td><strong>En cliquant ci-après, <a href="{$lien}">je confirme réception</a></td></tr>
{/if}
{if $relance == '1'}
<tr><td>Sans réponse de ta part, ce message se répètera.</td></tr>
{/if}
</table>
<p>Réponds en cliquant ci dessus ou connecte-toi sur ton espace privé à l'adresse suivante : http://ping.agi-webconseil.fr/index.php?page=mon-compte</p>
</body>
</html>