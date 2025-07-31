<?php
// require 'vendor\autoload.php';

// use Mike42\Escpos\Printer;
// use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
// use Mike42\Escpos\PrintConnectors\FilePrintConnector;
// use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
// use Mike42\Escpos\EscposImage;

// $connector = null;
// $connector = new WindowsPrintConnector('TM-T81'); // Change to your printer name

// $printer = new Printer($connector);
// $logo = "/public/assets/logo.png";
// if (is_file($logo) && file_exists($logo)) {
//     // $printer->setJustification(Printer::JUSTIFY_CENTER);
//     $img = EscposImage::load($logo);
//     $printer->bitImage($img);
//     $printer->feed(1);
// }
// // $printer->setJustification(Printer::JUSTIFY_CENTER);
// $content= file_get_contents("public/ticket_111.txt");

// $printer->text($content);
// $printer->feed(2);
// $printer->cut(Printer::CUT_PARTIAL);
// $printer->close();


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url); // $url = ton endpoint
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Ajoute cette ligne explicitement :
curl_setopt($ch, CURLOPT_CAINFO, 'C:/xampp/php/extras/ssl/cacert.pem');

$result = curl_exec($ch);

if (curl_errno($ch)) {
    throw new Exception('Curl Error: ' . curl_error($ch));
}
curl_close($ch);


