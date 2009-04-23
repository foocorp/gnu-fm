<html>
<head>
<title>Find your WebID</title>
<style type="text/css">
body
{
	background: #900;
	color: white;
	font-family: "Bitstream Vera Sans", "Helvetica", "Arial", sans-serif;
}
.form-end
{
	text-align: center;
}
ul
{
	padding: 0;
	margin: 0;
	list-style: none;
}
ul li, div.success
{
	margin: 0 0 8px 0;
	padding: 8px;
	background: white;
	color: black;
	border-radius: 6px;
	-o-border-radius: 6px;
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
}
ul li .radio
{
	position: relative;
	top: -20px;
}
ul li label b
{
	position: relative;
	left: 18px;
	top: -16px;
	font-size: 110%;
}
input
{
	font-size: 95%;
}
input.wide
{
	width: 100%;
}
.options_panel
{
	margin: 8px;
	padding: 8px;
	background: #ffd;
	border-radius: 6px;
	-o-border-radius: 6px;
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
	font-size: 80%;
}
</style>
</head>
<body>

<h1>WebID Finder</h1>

<?php
	if ($_REQUEST['submit'])
	{
		require_once 'guts.php';
		
		switch ($_REQUEST['source'])
		{
			case 'laconica' :
				$info = getFromLaconica( $_REQUEST[ $_REQUEST['source'] ] );
				break;
			case 'foaf' :
				$info = getFromFOAF( $_REQUEST[ $_REQUEST['source'] ] );
				break;
			case 'opera' :
				$info = getFromMyOpera( $_REQUEST[ $_REQUEST['source'] ] );
				break;
			case 'web' :
				$info = getFromWebsite( $_REQUEST[ $_REQUEST['source'] ] );
				break;
			case 'email' :
				$info = getFromEmail( $_REQUEST[ $_REQUEST['source'] ] );
				break;
			default :
				$info = getBestGuess( $_REQUEST[ $_REQUEST['source'] ] );
		}
		
		if ($info['WebID'])
		{
			print "<div class=\"success\">\n";
			printf("<p>Found a WebID%s:<br /><tt id=\"webid\" title=\"%s\">%s</tt></p>\n", 
				(empty($info['Name']) ? '' : (' for ' . htmlentities($info['Name']))),
				htmlentities($info['WebID']),
				htmlentities($info['WebID']));
			if (!empty($_REQUEST['javascript']))
			{
				print "<script type=\"text/javascript\">\n";
				print "function UseIt() { window.opener.document.getElementById('"
					. htmlentities($_REQUEST['javascript'])
					. "').value = document.getElementById('webid').title; window.close(); }\n";
				print "</script>\n";
				print "<p><a href=\"javascript:UseIt();\">Use this WebID</a></p>\n";
			}
			print "</div>\n";
			print "</body>\n";
			print "</html>\n";
			exit;
		}

		else
		{
			print "<p>Sorry, couldn't find your WebID that way. Try another method.</p>\n";
		}
	}
?>

<form action="" method="post">

<ul>

	<li>
		<input class="radio" type="radio" name="source" value="foaf" id="src_foaf" />
		<label for="src_foaf">
			<img src="foaf.png" alt="">
			<b>Your FOAF File</b>
		</label>
		<div id="options_foaf" class="options_panel">
			<label for="foaf">Enter your FOAF file URL.</label>
			<br /><input class="wide text" name="foaf" id="foaf" />
		</div>
	</li>

	<li>
		<input class="radio" type="radio" name="source" value="laconica" id="src_laconica" />
		<label for="src_laconica">
			<img src="laconica.png" alt="">
			<b>Your Laconica / identi.ca Account</b>
		</label>
		<div id="options_laconica" class="options_panel">
			<label for="laconica">Enter your identi.ca user name or the URL of your profile page on another laconica site.</label>
			<br /><input class="wide text" name="laconica" id="laconica" />
		</div>
	</li>

<!--	<li>
		<input class="radio" type="radio" name="source" value="opera" id="src_opera" />
		<label for="src_opera">
			<img src="opera.png" alt="">
			<b>Your &#8220;My Opera&#8221; Account</b>
		</label>
		<div id="options_opera" class="options_panel">
			<label for="opera">Enter your my.opera.com user name.</label>
			<br /><input class="wide text" name="opera" id="opera" />
		</div>
	</li>
-->
	<li>
		<input class="radio" type="radio" name="source" value="web" id="src_web" />
		<label for="src_web">
			<img src="web.png" alt="">
			<b>Your Website</b>
		</label>
		<div id="options_web" class="options_panel">
			<label for="web">Enter your website address.</label>
			<br /><input class="wide text" name="web" id="web" />
		</div>
	</li>

	<li>
		<input class="radio" type="radio" name="source" value="email" id="src_email" />
		<label for="src_email">
			<img src="email.png" alt="">
			<b>Your E-mail Address</b>
		</label>
		<div id="options_email" class="options_panel">
			<label for="email">Enter your e-mail address (don't worry, we won't keep it).</label>
			<br /><input class="wide text" name="email" id="email" />
		</div>
	</li>

</ul>

<div class="form-end">
	<input type="hidden" name="javascript" value="<?php echo htmlentities($_REQUEST['javascript']) ?>" />
	<input type="hidden" name="submit" value="1" />
	<input name="search" value="search" alt="Search" type="image" src="Search.png" />
</div>

</form>

<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
function Initio ()
{
	var OptionsPanels = $(".options_panel");
	OptionsPanels.hide();
	$(".radio").each( function (i) {
		this.onclick = Expando;
	});
}

function Expando ()
{
	$(".radio").each( function (i) {
		if (this.checked)
			$("#options_" + this.value).show();
		else
			$("#options_" + this.value).hide();
	});
}

Initio();
</script>

</body>
</html>
