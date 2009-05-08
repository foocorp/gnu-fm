{include file='header.tpl'}

<h2>{t}Explore popular artists{/t}</h2>

<ul id="topartists" class="listcloud">
  {section name=popular loop=$topartists}
  <li>
    <dl>
      <dt><a href="{$topartists[popular].artisturl}">
        {$topartists[popular].artist|stripslashes}</a></dt>
      <dd>{$topartists[popular].c} gobbles</dd>
    </dl>
  </li>
  {/section}
</ul>
<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
