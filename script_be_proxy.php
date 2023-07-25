<?php

require_once 'vendor/autoload.php';
libxml_use_internal_errors(true); 

$functie = $_GET['functie'];

if($functie == "scrape"){
	scrape();
} else if($functie == "inserare_afisare_s1"){
	inserare_afisare_s1();
} else if($functie == "inserare_afisare_s2"){
	inserare_afisare_s2();
} else if($functie == "stergere_afisare_s1"){
	stergere_afisare_s1();
}


function scrape(){
	
	$url = "https://clictadigital.com/how-to-use-h1-h2-and-h3-header-tags-for-seo-effectively/"; 
	$html = file_get_contents($url);
	
	$dom = new DOMDocument();
	$dom->loadHTML($html);

	$xpath = new DOMXPath($dom);

	$h1Elements = $xpath->query('(//h1)[position() <= 3]');
	$h2Elements = $xpath->query('(//h2)[position() <= 3]');
	
	$elementeDeScrape = [];
	$i = 0;
	while ($i < 3) {
		$elemente = [];
		$elemente[] = $h1Elements[$i]->textContent;
		$elemente[] = $h2Elements[$i]->textContent;
		$elementeDeScrape[] = $elemente;
		$i++;
	}
	
	$response = [ 'elementeDeScrape' => $elementeDeScrape,	];

	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
	header("Access-Control-Allow-Headers: Content-Type, Authorization");
	header('Content-Type: application/json; charset=utf-8');
	
	echo json_encode($response, JSON_UNESCAPED_UNICODE);
}


function inserare_afisare_s1(){
	
	$date = file_get_contents('php://input');
	$date = json_decode($date, true);
	$url = 'http://localhost:4000/date';
	
	foreach ($date as $inregistrare) {
		
		$curl = curl_init($url);
		$updatedData = json_encode($inregistrare, JSON_PRETTY_PRINT);
		
		curl_setopt($curl, CURLOPT_POSTFIELDS, $updatedData);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Accept: application/json'
		));
		
		$response = curl_exec($curl);
		if ($response === false) {
			echo 'Eroare: ' . curl_error($curl);
		}
		curl_close($curl);
		usleep(1000);
	}

	$jsonData = file_get_contents($url);
	echo $jsonData;	
}


function stergere_afisare_s1(){
	
	$url = 'http://localhost:4000/date';
		
	$campDeSters = $_GET['camp'];	
	$jsonData = file_get_contents($url);
	$date = json_decode($jsonData, true);

	$elementGasit = null;
	foreach ($date as $i => $inregistrare) {
		if ($inregistrare['h1'] === $campDeSters) { 
			$elementGasit = $inregistrare['id'];
			break;
		}
	}

	if ($elementGasit !== null) {
		
		$deleteUrl = $url . '/' . $elementGasit;
		
		$deleteCh = curl_init($deleteUrl);
		curl_setopt($deleteCh, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($deleteCh, CURLOPT_RETURNTRANSFER, true);
		$deleteResponse = curl_exec($deleteCh);
		$jsonData = file_get_contents($url);
		echo $jsonData;	
		curl_close($deleteCh);
		
	} else {
		echo 'Valoarea nu a fost gasita';
	}
}


function inserare_afisare_s2(){

	$endpointUrl = 'http://localhost:8080/rdf4j-server/repositories/1';
	
	$date = file_get_contents('php://input');
	$date = json_decode($date, true);
	$graph = new \EasyRdf\Graph();

	$graph->addLiteral('http://localhost:8080/resource/1', 'http://localhost:8080/property/title', $date[0]['h1']);

	$jsonLd = $graph->serialise('jsonld');
	
	$client = new Client();

	$headers = [
		'Content-Type' => 'application/ld+json',
		'Accept' => 'application/ld+json'
	];

	foreach ($headers as $name => $value) {
		$client->setHeaders($name, $value);
	}

	$client->setUri($endpointUrl);
	$client->setMethod('POST');
	$client->setRawData($jsonLd);

	$response = $client->request();
	echo $response;	
}
		
	
?>