<?php
/**
 * @abstract This Component Class is created to access TCPDF plugin for generating reports.
 * @example You can refer http://www.tcpdf.org/examples/example_011.phps for more details for this example.
 * @todo you can extend tcpdf class method according to your need here. You can refer http://www.tcpdf.org/examples.php section for 
 *       More working examples.
 * @version 1.0.0
 */
require_once(realpath(dirname(__FILE__) . '/../extensions/tcpdf/tcpdf.php'));
Yii::import('ext.tcpdf.*');


class PdfCreator extends TCPDF {

    //Page header
    public function Header() {
        $this->Image(K_PATH_IMAGES.$this->header_logo, '', '', $this->header_logo_width);
        $this->MultiCell('100%', 50, "<div><h3>$this->header_title</h3></div>", 0, 'J', false, '', 15, 18, false, false, true, true, 0, 'L', true);
        $this->MultiCell('100%', 50, "<div><i>$this->header_string</i></div>", 0, 'J', false, '', 15, 30, false, false, true, true, 0, 'L', true);
    }

    // Colored table
    public function ClientUploadReport($header, $data) {

        // Colors, line width and bold font
        $this->SetFillColor(215, 210, 253);
        $this->SetTextColor(0);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(0.3);
        $this->setCellPaddings(2,2,1,1);
        $this->SetFont('', 'B');
        // Header
        $w = array(10, 35, 45, 20, 70);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            $this->Cell(
                    $w[$i],
                    $h = 6,
                    $header[$i],
                    $border = array('B' => array('width' => .25, 'color' => array(134, 6, 173))),
                    $ln = 0,
                    $align = 'L',
                    $fill = true,
                    $link = '',
                    $stretch = 0,
                    $ignore_min_height = false,
                    $calign = 'T',
                    $valign = 'B'
            );
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(201, 232, 207);
        $this->SetTextColor(0);
        $this->SetFont('');
        $this->setCellPaddings(2,2,1,1);
        // Data
        $fill = 0;
        $rowBreak = 24;

        foreach($data as $key => $row) {
            if ($key != 0 && $key % $rowBreak === 0) {
                $this->AddPage();
            }
            $this->MultiCell(
                    $w[0],
                    $h = 10,
                    $row[0],
                    $border = array('B' => array('width' => .1, 'color' => array(13, 109, 13))),
                    $align = 'R',
                    $fill,
                    $ln = 0,
                    $x = '',
                    $y = '',
                    $reseth = false,
                    $stretch = 0,
                    $ishtml = false,
                    $autopadding = false,
                    $maxh = 10,
                    $valign = 'M',
                    $fitcell = true 
	); 		
            $this->MultiCell(
                    $w[1],
                    $h = 10,
                    $row[1],
                    $border = array('B' => array('width' => .1, 'color' => array(13, 109, 13))),
                    $align = 'L',
                    $fill,
                    $ln = 0,
                    $x = '',
                    $y = '',
                    $reseth = false,
                    $stretch = 0,
                    $ishtml = false,
                    $autopadding = false,
                    $maxh = 10,
                    $valign = 'M',
                    $fitcell = true
	); 		
            $this->MultiCell(
                    $w[2],
                    $h = 10,
                    $row[2],
                    $border = array('B' => array('width' => .1, 'color' => array(13, 109, 13))),
                    $align = 'L',
                    $fill,
                    $ln = 0,
                    $x = '',
                    $y = '',
                    $reseth = false,
                    $stretch = 0,
                    $ishtml = false,
                    $autopadding = false,
                    $maxh = 10,
                    $valign = 'M',
                    $fitcell = true
	); 		
            $this->MultiCell(
                    $w[3],
                    $h = 10,
                    $row[3],
                    $border = array('B' => array('width' => .1, 'color' => array(13, 109, 13))),
                    $align = 'L',
                    $fill,
                    $ln = 0,
                    $x = '',
                    $y = '',
                    $reseth = false,
                    $stretch = 0,
                    $ishtml = false,
                    $autopadding = false,
                    $maxh = 10,
                    $valign = 'M',
                    $fitcell = true 
	); 		
            $this->MultiCell(
                    $w[4],
                    $h = 10,
                    $row[4],
                    $border = array('B' => array('width' => .1, 'color' => array(13, 109, 13))),
                    $align = 'L',
                    $fill,
                    $ln = 0,
                    $x = '',
                    $y = '',
                    $reseth = false,
                    $stretch = 0,
                    $ishtml = false,
                    $autopadding = false,
                    $maxh = 10,
                    $valign = 'M',
                    $fitcell = true 
	); 		
                    
            $this->Ln();
            $fill=!$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    
    public function AdvisorInvoiceReport($header, $data) {

        // Colors, line width and bold font
        $this->SetFillColor(215, 210, 253);
        $this->SetTextColor(0);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(0.3);
        $this->setCellPaddings(2,2,1,1);
        $this->SetFont('', 'B');
        // Header
        $w = array(50, 70, 40, 20);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            $this->Cell(
                    $w[$i],
                    $h = 6,
                    $header[$i],
                    $border = array('B' => array('width' => .25, 'color' => array(134, 6, 173))),
                    $ln = 0,
                    $align = 'L',
                    $fill = true,
                    $link = '',
                    $stretch = 0,
                    $ignore_min_height = false,
                    $calign = 'T',
                    $valign = 'B'
            );
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(201, 232, 207);
        $this->SetTextColor(0);
        $this->SetFont('');
        $this->setCellPaddings(2,2,1,1);
        // Data
        $fill = 0;
        $rowBreak = 24;

        foreach($data as $key => $row) {
            if ($key != 0 && $key % $rowBreak === 0) {
                $this->AddPage();
            }
            $this->MultiCell(
                    $w[0],
                    $h = 10,
                    $row[0],
                    $border = array('B' => array('width' => .1, 'color' => array(13, 109, 13))),
                    $align = 'L',
                    $fill,
                    $ln = 0,
                    $x = '',
                    $y = '',
                    $reseth = false,
                    $stretch = 0,
                    $ishtml = false,
                    $autopadding = false,
                    $maxh = 10,
                    $valign = 'M',
                    $fitcell = true 
	); 		
            $this->MultiCell(
                    $w[1],
                    $h = 10,
                    $row[1],
                    $border = array('B' => array('width' => .1, 'color' => array(13, 109, 13))),
                    $align = 'L',
                    $fill,
                    $ln = 0,
                    $x = '',
                    $y = '',
                    $reseth = false,
                    $stretch = 0,
                    $ishtml = false,
                    $autopadding = false,
                    $maxh = 10,
                    $valign = 'M',
                    $fitcell = true
	); 		
            $this->MultiCell(
                    $w[2],
                    $h = 10,
                    $row[2],
                    $border = array('B' => array('width' => .1, 'color' => array(13, 109, 13))),
                    $align = 'L',
                    $fill,
                    $ln = 0,
                    $x = '',
                    $y = '',
                    $reseth = false,
                    $stretch = 0,
                    $ishtml = false,
                    $autopadding = false,
                    $maxh = 10,
                    $valign = 'M',
                    $fitcell = true
	); 		
            $this->MultiCell(
                    $w[3],
                    $h = 10,
                    $row[3],
                    $border = array('B' => array('width' => .1, 'color' => array(13, 109, 13))),
                    $align = 'L',
                    $fill,
                    $ln = 0,
                    $x = '',
                    $y = '',
                    $reseth = false,
                    $stretch = 0,
                    $ishtml = false,
                    $autopadding = false,
                    $maxh = 10,
                    $valign = 'M',
                    $fitcell = true 
	); 		
            
            $this->Ln();
            $fill=!$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}
?>