<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/pdf/config.php');
include_once('include/pdf/3gk_table.php');
include_once('include/HTML_TopBottom.php');
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

if(isset($_REQUEST['show'])) {
	$diensten = getAllKerkdiensten(false);
	$roosters = $_REQUEST['r'];
	
	$text[] = "<table border=1>";
	$text[] = "<tr>";
	$text[] = "<td>&nbsp;</td>";
	$header[] = 'Datum';
	
	foreach($roosters as $rooster) {
		$RoosterData = getRoosterDetails($rooster);
		$text[] = "<td><b>". $RoosterData['naam'] ."</b></td>";
		$header[] = $RoosterData['naam'];
	}
	$text[] = "</tr>";
	
	foreach($diensten as $dienst) {
		$dienstData = getKerkdienstDetails($dienst);		
		$gevuldeCel = false;
		$cel = $rij = array();
				
		foreach($roosters as $rooster) {
			$vulling = getRoosterVulling($rooster, $dienst);
		
			if(count($vulling) > 0) {				
				$team = array();	
				foreach($vulling as $lid) {
					$team[] = makeName($lid, 5);
				}
				$cel[] = "<td valign='top'>". implode("<br>", $team) ."</td>";
				$rij[] = implode("\n", $team);
				$gevuldeCel = true;
			} else {
				$cel[] = "<td>&nbsp;</td>";
				$rij[] = '';
			}		
		}
		
		if($gevuldeCel) {
			$text[] = "<tr>";
			$text[] = "<td valign='top'>".strftime("%a %d %b %H:%M", $dienstData['start'])."</td>";
			$text[] = implode("\n", $cel);
			$text[] = "</tr>";
			$rij = array_merge(array(strftime("%a %d %b %H:%M", $dienstData['start'])), $rij);
			$data[] = $rij;
		}		
	}
	
	$text[] = "</table>";
	$text[] = "<p>";
	$text[] = "<a href='?r[]=". implode("&r[]=", $_REQUEST['r'])."&show&pdf'>sla op als PDF</a>";
} else {
	$roosters = getRoosters(0);
	$text[] = "<form>";
	$text[] = "<table>";
	foreach($roosters as $rooster) {
		$data = getRoosterDetails($rooster);
		$text[] = "<tr><td><input type='checkbox' name='r[]' value='$rooster'></td><td>". $data['naam']."</td></tr>";
	}
	$text[] = "<tr><td colspan='2'>&nbsp;</td></tr>";
	$text[] = "<tr><td colspan='2' align='center'><input type='submit' name='show' value='Toon gezamenlijk'></td></tr>";
	$text[] = "</table>";
	$text[] = "</form>";	
}

if(isset($_REQUEST['pdf'])) {
	if(count($header) == 2) {
		$RoosterData = getRoosterDetails(end($roosters));
		$title = $RoosterData['naam'];
	} else {
		$title = 'Gecombineerd rooster';
	}
	
	if(count($header) < 4) {
		$pdf = new PDF_3GK_Table();
	} else {
		$pdf = new PDF_3GK_Table('L');
	}
	$breedte = $pdf->GetPageWidth();
	
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetFont($cfgLttrType,'',8);
	
	$widths = array_fill(1, (count($header)-1), ($breedte-25-(2*$cfgMarge))/(count($header)-1));
	$widths[0] = 25;
	$pdf->SetWidths($widths);
	
	$pdf->makeTable($header, $data);
  $pdf->Output('I', $title .'.pdf');	
} else {
	echo $HTMLHeader;
	echo implode("\n", $text);
	echo $HTMLFooter;
}

?>
