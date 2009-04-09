{include file='header.tpl'}

<h2>Admin panel</h2>
<hr>

{if isset($sent)}
<b>Email was sent successfully!</b><br/ >
{/if}
<b>Requests for invites</b>
<ul id="invites">
{section name=i loop=$emails}
{if ($emails[i].status == '0')}
  <li>
    <dl>
      <dt><a href="admin.php?action=invite&email={$emails[i].email|stripslashes|urlencode}">
        {$emails[i].email|stripslashes}</a></dt>
    </dl>
  </li>
{/if}
{/section}
</ul><br />
<b>Invited people</b>
{section name=i loop=$emails}
{if ($emails[i].status == '1')}
  <li>
    <dl>
      <dt><a href="admin.php?action=invite&email={$emails[i].email|stripslashes|urlencode}">
        {$emails[i].email|stripslashes}</a></dt>
    </dl>
  </li>
{/if}
{/section}
</ul>

{include file='footer.tpl'}
