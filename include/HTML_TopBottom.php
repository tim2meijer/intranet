<?php

# Header
$HTMLHead	= "<!--     Deze pagina is onderdeel van $ScriptTitle $Version gemaakt door Matthijs Draijer     -->\n\n";
$HTMLHead	.= '<html>'.NL;
$HTMLHead	.= '<head>'.NL;
$HTMLHead	.= "	<title>$ScriptTitle $Version</title>\n";
$HTMLHead	.= "	<link rel='stylesheet' type='text/css' href='". $ScriptURL ."/include/style.css'>\n";

$HTMLBody	= '</head>'.NL;
$HTMLBody	.= '<body>'.NL;
$HTMLBody	.= '<table width="95%" cellpadding="0" cellspacing="0" align="center" bgcolor="ffffff" border=0>'.NL;
$HTMLBody	.= '<tr>'.NL;
$HTMLBody	.= '	<td height="20" bgcolor="#8C1974">&nbsp;</td>'.NL;
$HTMLBody	.= '</tr>'.NL;
$HTMLBody	.= '<tr>'.NL;
$HTMLBody	.= '	<td height="10">&nbsp;</td>'.NL;
$HTMLBody	.= '</tr>'.NL;
$HTMLBody	.= '<tr>'.NL;
$HTMLBody	.= '	<td>'.NL;
$HTMLBody	.= '	<table width="95%" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" border=0>'.NL;
$HTMLBody	.= '	<tr>'.NL;
$HTMLBody	.= '		<td width="50">&nbsp;</td>'.NL;
$HTMLBody	.= '		<td width="150"><a href="'. $ScriptURL .'"><img src="'. $ScriptURL .'/images/logoKoningsKerk.png" height=150 alt=""></a></td>'.NL;
$HTMLBody	.= '    <td width="75">&nbsp;</td>'.NL;
$HTMLBody	.= '		<td class="onderwerp" align="middle" height="80" valign="middle">&nbsp;</td>'.NL;
$HTMLBody	.= '		<td width="50">&nbsp;</td>'.NL;
$HTMLBody	.= '	</tr>'.NL;
$HTMLBody	.= '  </table>'.NL;
$HTMLBody	.= '  <table width="95%" align="center" border=0>'.NL;
$HTMLBody	.= '	<tr>'.NL;
$HTMLBody	.= '		<td class="seperator">&nbsp;</td>'.NL;
$HTMLBody	.= '	</tr>'.NL;
$HTMLBody	.= '	<tr>'.NL;
$HTMLBody	.= '		<td>&nbsp;</td>'.NL;
$HTMLBody	.= '	</tr>'.NL;
$HTMLBody	.= '	<tr>'.NL;
$HTMLBody	.= '		<td>'.NL;

$HTMLHeader = $HTMLHead.$HTMLBody;


# Footer
$HTMLFooter  = '		</td>'.NL;
$HTMLFooter .= '	</tr>'.NL;
$HTMLFooter .= '	<tr>'.NL;
$HTMLFooter .= '		<td class="seperator">&nbsp;</td>'.NL;
$HTMLFooter .= '	</tr>'.NL;
$HTMLFooter .= '	<tr>'.NL;
$HTMLFooter .= '		<td>&nbsp;</td>'.NL;
$HTMLFooter .= '	</tr>'.NL;
$HTMLFooter .= '  </table>'.NL;
$HTMLFooter .= '	</td>'.NL;
$HTMLFooter .= '</tr>'.NL;
$HTMLFooter .= '<tr>'.NL;
# $HTMLFooter .= '	<td height="20" bgcolor="#34383D">&nbsp;</td>'.NL;
$HTMLFooter .= '	<td height="20" bgcolor="#8C1974">&nbsp;</td>'.NL;
$HTMLFooter .= '</tr>'.NL;
$HTMLFooter .= '</table>'.NL;
$HTMLFooter .= '<br /><br /><br /><br /><br /><br />'.NL;
$HTMLFooter .= '</body>'.NL;
$HTMLFooter .= '</html>'.NL;
$HTMLFooter .= "\n\n<!--     Deze pagina is onderdeel van $ScriptTitle $Version gemaakt door Matthijs Draijer     -->";
		

?>