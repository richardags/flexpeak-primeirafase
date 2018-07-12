<?php
	
	class UtilsClass {
		static function validateCEP($cep) {
			//O CEP pode conter três tipos formatos: 00.000-000, 00000-000 ou 00000000
		    return preg_match("/^([0-9]{2}.[0-9]{3}-[0-9]{3}|[0-9]{5}-[0-9]{3}|[0-9]{8})$/", $cep);
		}

		static function unmaskCEP($cep){
			return str_replace(array('.', '-'), '', $cep);
		}
		static function validateDate($date, $format = 'd/m/Y')
		{
			date_default_timezone_set('UTC');
		    $d = DateTime::createFromFormat($format, $date);
		    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
		    return $d && $d->format($format) === $date;
		}
		static function validateNumber($number){
			return preg_match('/^\d+$/', $number);
		}
		static function message($echo){
			echo "<br>$echo";
		}
		static function jump(){
			echo '<br>';
		}
		static function getHTTP($url){
			$options = array(
			        CURLOPT_RETURNTRANSFER => true,   // return web page
			        CURLOPT_HEADER         => false,  // don't return headers
			        CURLOPT_FOLLOWLOCATION => true,   // follow redirects
			        //CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
			        //CURLOPT_ENCODING       => "",     // handle compressed
			       // CURLOPT_USERAGENT      => "test", // name of client
			        CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
			        //CURLOPT_SSL_VERIFYHOST    => 2,   //check the existence of a common name and also verify that it matches the hostname provided.
			        CURLOPT_SSL_VERIFYPEER    => false,   //stop cURL from verifying the peer's certificate
			        //CURLOPT_CAINFO         => 'certificate.ca', //set certificate
			        CURLOPT_FAILONERROR => true,    // required for HTTP error codes to be reported via our call to curl_error($ch)
			        CURLOPT_CONNECTTIMEOUT => 5,    // time-out on connect
			        CURLOPT_TIMEOUT        => 5,    // time-out on response
			    );

			$ch = curl_init($url);
			curl_setopt_array($ch, $options);

			$result = curl_exec($ch);

			if(curl_errno($ch))
				throw new Exception(curl_error($ch));

			curl_close($ch);

			return $result;
		}
	}
	
?>