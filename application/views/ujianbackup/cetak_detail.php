<?php
// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF
{

    public function Header()
    {
        $image_file = K_PATH_IMAGES . 'logo_example.jpg';
        $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $this->SetFont('helvetica', 'B', 18);
        $this->SetY(13);
        $this->Cell(0, 15, 'Hasil Ujian', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}

// create new PDF document
$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('Hasil Ujian');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();

$mulai = strftime('%A, %d %B %Y', strtotime($ujian->tgl_mulai));
$selesai = strftime('%A, %d %B %Y', strtotime($ujian->terlambat));

// create some HTML content
$html = <<<EOD
<p>
Lorem ipsum dolor, sit amet consectetur adipisicing elit. Cumque facere sint veniam.
Dolore distinctio, at consequuntur magnam cupiditate voluptate hic ratione ea illo nulla quis cum optio, nisi eius dignissimos!
</p>
<table>
    <tr>
        <th>Nama Ujian</th>
        <td>{$ujian->nama_ujian}</td>
        <th>Mata Pelajaran</th>
        <td>{$ujian->nama_mapel}</td> 
    </tr>
    <tr>
        <th>Jumlah Soal</th>
        <td>{$ujian->jumlah_soal}</td>
        <th>Topik</th>
        <td>{$ujian->nama_topik}</td>
    </tr>
    <tr>
        <th>Waktu</th>
        <td>{$ujian->waktu} Menit</td>
        <th>Nilai Terendah</th>
        <td>{$nilai->min_nilai}</td>
    </tr>
    <tr>
        <th>Tanggal Mulai</th>
        <td>{$mulai}</td>
        <th>Nilai Tertinggi</th>
        <td>{$nilai->max_nilai}</td>
    </tr>
    <tr>
        <th>Tanggal Selasi</th>
        <td>{$selesai}</td>
        <th>Rata-rata Nilai</th>
        <td>{$nilai->avg_nilai}</td>
    </tr>
    <tr>
        <th>Guru</th>
        <td>{$ujian->nama_guru}</td>
    </tr>
</table>
EOD;

$html .= <<<EOD
<br><br><br>
<table border="1" style="border-collapse:collapse">
    <thead>
        <tr align="center">
            <th width="10%" rowspan="2">Mapel</th>
            <th width="4%" colspan="2">KD1</th>
            <th width="4%" colspan="2">KD2</th>
            <th width="4%" colspan="2">KD3</th>
            <th width="4%" colspan="2">KD4</th>
            <th width="4%" colspan="2">KD5</th>
            <th width="4%" colspan="2">KD6</th>
            <th width="4%" colspan="2">KD7</th>
            <th width="4%" colspan="2">KD8</th>
            <th width="4%" colspan="2">KD9</th>
            <th width="4%" colspan="2">KD10</th>
            <th width="4%" rowspan="2">UTS</th>           
            <th width="4%" rowspan="2">UAS</th>           
        </tr>        
        <tr align="center">
            <th width="4%">Tugas</th>
            <th width="4%">Ujian</th>
            <th width="4%">Tugas</th>
            <th width="4%">Ujian</th>
            <th width="4%">Tugas</th>
            <th width="4%">Ujian</th>
            <th width="4%">Tugas</th>
            <th width="4%">Ujian</th>
            <th width="4%">Tugas</th>
            <th width="4%">Ujian</th>
            <th width="4%">Tugas</th>
            <th width="4%">Ujian</th>
            <th width="4%">Tugas</th>
            <th width="4%">Ujian</th>
            <th width="4%">Tugas</th>
            <th width="4%">Ujian</th>
            <th width="4%">Tugas</th>
            <th width="4%">Ujian</th>
            <th width="4%">Tugas</th>
            <th width="4%">Ujian</th>
        </tr>        
    </thead>
    <tbody>

    </tbody>
</table>
EOD;

// output the HTML content
$pdf->writeHTML($html, true, 0, true, 0);
// reset pointer to the last page
$pdf->lastPage();
// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('tes.pdf', 'I');
