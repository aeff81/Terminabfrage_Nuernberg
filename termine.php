<?php
/* Folgende Dienstleistungen sind mir bisher bekannt
   diese lassen sich z.B. mit dem Developer-Modus eines Browsers ermitteln
   73 => Gebrauchtfahrzeug aus dem Inland umschreiben oder zulassen
   75 => Gebrauchtfahrzeug aus dem Ausland zulassen
   (beide jeweils für "Privatkunden und Unternehmen außerhalb der Automobilbranche")

   Diese Info kommt dann auch nochmal im JSON mit zurück. So kann man kontrollieren ob man die richtige
   ID erwischt hat.
 */
$dienstleistung=75;
# Zeitstempel in Unix-Zeit (Google: "Epoch converter")
$nichtvor=1763717577; // 21.11.
$nichtnach=176467770; // 2.12. 13:15
$emailempfaenger='somebody@example.com';
$url = "https://microservices.nuernberg.de/behoerdenwegweiser/tevis/dates";

$data = '{
  "concernIds":['.$dienstleistung.'],
  "locations":[
    {
      "shortName":"Ordnungsamt",
      "departmentName":"Bürgerdienste Mobilität",
      "locationIds":[5,5]
    },
    {
      "shortName":"Bürgeramt Ost",
      "departmentName":null,
      "locationIds":[32,32]
    },
    {
      "shortName":"Bürgeramt Süd",
      "departmentName":null,
      "locationIds":[12,12]
    }
  ]
}';

$headers = [
    "User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:140.0) Gecko/20100101 Firefox/140.0",
    "Accept: */*",
    "Accept-Language: de,en-US;q=0.7,en;q=0.3",
    "Accept-Encoding: gzip, deflate, br, zstd",
    "Referer: https://www.nuernberg.de/",
    "Content-Type: text/plain;charset=UTF-8",
    "Origin: https://www.nuernberg.de",
    "Connection: keep-alive",
    "Sec-Fetch-Dest: empty",
    "Sec-Fetch-Mode: cors",
    "Sec-Fetch-Site: same-site",
    "Priority: u=4"
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Optional: falls die Antwort komprimiert ist
curl_setopt($ch, CURLOPT_ENCODING, "");

$response = curl_exec($ch);

if(curl_errno($ch)){
    echo "cURL Fehler: " . curl_error($ch);
} else {
	$daten=json_decode($response);
}

//var_dump($daten);
$nimmdas=$daten->{'data'}[0]->{'locations'};
//var_dump($nimmdas);
foreach ($nimmdas as $termin)
{
	$stelle=$termin->{'place'};
	$zeit=$termin->{'date'};
	$link=$termin->{'reservation_link'};
	$nachricht=$stelle."\r\n".$zeit."\r\n".$link."\r\n________________________________\r\n";
	echo $nachricht;

	# Mail nur wenn der Termin den Kriterien entspricht.	
	if (($termin->{'timestamp'} < $nichtnach) and ($termin->{'timestamp'} > $nichtvor))
	{
		mail($emailempfaenger, 'Neuer Termin!', $nachricht);
	}
}
curl_close($ch);


//  Zur Referenz ein Curl-Aufruf aus der Shell
//  curl 'https://microservices.nuernberg.de/behoerdenwegweiser/tevis/dates'   -X POST   -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:140.0) Gecko/20100101 Firefox/140.0'   -H 'Accept: */*'   -H 'Accept-Language: de,en-US;q=0.7,en;q=0.3'   -H 'Accept-Encoding: gzip, deflate, br, zstd'   -H 'Referer: https://www.nuernberg.de/'   -H 'Content-Type: text/plain;charset=UTF-8'   -H 'Origin: https://www.nuernberg.de'   -H 'Connection: keep-alive'   -H 'Sec-Fetch-Dest: empty'   -H 'Sec-Fetch-Mode: cors'   -H 'Sec-Fetch-Site: same-site'   -H 'Priority: u=4'   --data-raw $'{"concernIds":[73],"locations":[{"shortName":"Ordnungsamt","departmentName":"Bürgerdienste Mobilität","locationIds":[5,5]},{"shortName":"Bürgeramt Ost","departmentName":null,"locationIds":[32,32]},{"shortName":"Bürgeramt Süd","departmentName":null,"locationIds":[12,12]}]}'

?>
