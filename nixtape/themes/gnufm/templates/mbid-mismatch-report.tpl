{include file='header.tpl'}

<h2 property="dc:title">MBID mismatch report</h2>

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Artist</th>
      <th>Album</th>
      <th>Track</th>
      <th>MBID on file</th>
      <th>Other MBID</th>
    </tr>
  </thead>
  <tbody>
{section name=i loop=$entries}
<tr>
<td>{$entries[i].id}</td>
<td>{$entries[i].artist}</td>
<td>{$entries[i].album}</td>
<td>{$entries[i].name}</td>
<td>{$entries[i].tmbid}</td>
<td>{$entries[i].stmbid}</td>
</tr>
{/section}
  </tbody>
</thead>

{include file='footer.tpl'}
