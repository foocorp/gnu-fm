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

	<div id="adbard">

	    <!--Ad Bard advertisement snippet, begin -->

	    <script type='text/javascript'>
	     var ab_h = '4bcaab930d3bdfded68fd7be730d7db4';
     	     var ab_s = '55fd9cde6d855a75f9ca43d854272f6b';
     	    </script>
   	    
            <script type='text/javascript' src='http://cdn1.adbard.net/js/ab1.js'></script>

	    <!--Ad Bard, end -->

	</div>

{include file='footer.tpl'}
