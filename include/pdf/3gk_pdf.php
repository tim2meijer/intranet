<?php
require('fpdf.php');

class PDF_3GK extends FPDF {
	# Page header
	function Header() {
    global $cfgMarge, $title;
        
		# Stel marges in
    $this->SetMargins($cfgMarge, $cfgMarge);
    
    # Logo
    $size = array(20, 20);
    $offsetX = $cfgMarge;
    $offsetY = $cfgMarge-10;
    $this->Image('images/trinitaslogo.png',$offsetX,$offsetY,$size[0]);
    
    # Arial bold 15
    $this->SetFont('Arial','B',15);
    
    # Move to the right + Title
    $this->SetY($offsetY);
    $this->SetX($offsetX+$size[0]+10);    
    $this->Cell(0,$size[0],$title,0,0,'C');
    
    # Line break
    $this->Ln($size[1]+10);
	}

	// Page footer
	function Footer() {		
		global $cfgMarge, $title;
				
    $breedte = $this->GetPageWidth();
		
    # 2 cm van de onderkant en lettertype
    $this->SetY(-20);
    $this->SetFont('Arial','I',6);
    
    # Printdatum, titel en paginanummers    
    $this->Cell(30,10,strftime("%A %d %B %Y"),0,0,'L');
    $this->Cell(($breedte-(2*(30+$cfgMarge))),10,'SAMENWERKINGSGEMEENTE CGK-GKV-NGK DEVENTER',0,0,'C');
		$this->Cell(30,10,'Pagina '.$this->PageNo().' van {nb}',0,0,'R');   
    
	}
}

?>