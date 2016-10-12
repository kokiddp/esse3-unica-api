<?php 

/**
 *
 * PHP version 5
 *
 *
 * @author     Andrea Corriga <me@andreacorriga.com>
 * @copyright  2016 Andrea Corriga
 * @license    
 * @version    1.0
 * @link       http://andreacorriga.com
 * 
 *
 */
	class Message {

		public static function get_200(){
			
			return array(
						"status" 	=> '200',
						"message"	=> 'La richiesta e\' stata elaborata correttamente.'
				);
		}

		public static function get_401(){
			return array(
							"status" 	=> '401',
							"message"	=> 'Errore autenticazione, i dati inseriri risultano non essere corretti.'
				);
		}

		public static function get_400(){
			return array(
							"status" 	=> '400',
							"message"	=> 'Errore 400, la richiesta risulta essere non valida.'
				);
		}		

		public static function get_500(){
			
			return array(
						"status" 	=> '500',
						"message"	=> 'E\' stato riscontrato un errore generico all\'interno del server. Se l\'errore persiste contattare l\'amministratore.' 
				);
		}

		// Restituisce il valore del campo status di uno dei metodi di questa classe
		public static function getStatus($response){
			return $response["status"];
		}

		// Restituisce il content del campo status di uno dei metodi di questa classe
		public static function getContent($response){
			return $response["content"];
		}

		
		// Stampa il Json Formattato ottenuto da uno dei metodi di questa classe
		public static function handleMessage($response, $values = array()){
			
			$message = json_encode(
						array(
							"response" 	=> $response,
							"data" 	=> $values
							), JSON_PRETTY_PRINT
					);

			header('Content-type: text/javascript; charset=utf-8'); 
			print_r($message);
		}
	}
