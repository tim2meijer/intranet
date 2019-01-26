<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/pdf/config.php');
include_once('include/pdf/3gk_table.php');
include_once('include/HTML_TopBottom.php');
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

$pdf = new PDF_3GK_Table();
$breedte = $pdf->GetPageWidth();
	
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont($cfgLttrType,'',8);
	
$widths = array_fill(1, (count($header)-1), ($breedte-25-(2*$cfgMarge))/(count($header)-1));
$widths[0] = 25;
$pdf->SetWidths($widths);
	
$pdf->makeTable($header, $data);
$pdf->Output('I', $title.'_'.date('Y_m_d').'.pdf');