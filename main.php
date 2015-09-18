<?php
/**
 * Telegram Bot example for mapping
 * @author Matteo Tempestini 
	Funzionamento
	- invio location
	- invio segnalazione come risposta
	- memorizzazione dati nel DB SQLITE
	- export in file CSV e MAPPING
 */
//include('settings_t.php');
include("Telegram.php");

class mainloop{
 
 function start($telegram,$update)
	{

		date_default_timezone_set('Europe/Rome');
		$today = date("Y-m-d H:i:s");
		
		// Instances the class
		$db = new PDO(DB_NAME);

		/* If you need to manually take some parameters
		*  $result = $telegram->getData();
		*  $text = $result["message"] ["text"];
		*  $chat_id = $result["message"] ["chat"]["id"];
		*/
		
		$text = $update["message"] ["text"];
		$chat_id = $update["message"] ["chat"]["id"];
		$user_id=$update["message"]["from"]["id"];
		$location=$update["message"]["location"];
		$reply_to_msg=$update["message"]["reply_to_message"];
		
		$this->shell($telegram, $db,$text,$chat_id,$user_id,$location,$reply_to_msg);
		$db = NULL;

	}

	//gestisce l'interfaccia utente
	 function shell($telegram,$db,$text,$chat_id,$user_id,$location,$reply_to_msg)
	{
		date_default_timezone_set('Europe/Rome');
		$today = date("Y-m-d H:i:s");

		if ($text == "/start") {
				$log=$today. ";new chat started;" .$chat_id. "\n";
			}
			
			//gestione segnalazioni georiferite
			elseif($location!=null)
			{

				$this->location_manager($db,$telegram,$user_id,$chat_id,$location);
				exit;	

			}
			elseif($reply_to_msg!=null)
			{
				//inserisce la segnalazione nel DB delle segnalazioni georiferite
				$statement = "UPDATE ".DB_TABLE_GEO ." SET text='".$text."' WHERE bot_request_message ='".$reply_to_msg['message_id']."'";
				print_r($reply_to_msg['message_id']);
            	$db->exec($statement);
				$reply = "Segnalazione Registrata. Grazie!";
				$content = array('chat_id' => $chat_id, 'text' => $reply);
				$telegram->sendMessage($content);
				$log=$today. ";information for maps recorded;" .$chat_id. "\n";	
				
				//aggiorno dati mappa
				exec('sqlite3 -header -csv db.sqlite "select * from segnalazioni;" > map_data.csv');
			}			
			//comando errato
			else{
				 $reply = "Hai selezionato un comando non previsto";
				 $content = array('chat_id' => $chat_id, 'text' => $reply);
				 $telegram->sendMessage($content);
				 $log=$today. ";wrong command sent;" .$chat_id. "\n";
			 }
						
			//aggiorna tastiera
			//$this->create_keyboard($telegram,$chat_id);
			//log			
			file_put_contents(LOG_FILE, $log, FILE_APPEND | LOCK_EX);
			
	}


	// Crea la tastiera
	 function create_keyboard($telegram, $chat_id)
		{
				//
		}

	//crea la tastiera per scegliere la zona temperatura
	 function create_keyboard_temp($telegram, $chat_id)
		{
				//
		}
		
		
	
	function location_manager($db,$telegram,$user_id,$chat_id,$location)
		{
				$lng=$location["longitude"];
				$lat=$location["latitude"];
				
				//rispondo
				$response=$telegram->getData();
				$bot_request_message_id=$response["message"]["message_id"];
				
				//nascondo la tastiera e forzo l'utente a darmi una risposta
				$forcehide=$telegram->buildForceReply(true);

				//chiedo cosa sta accadendo nel luogo
				$content = array('chat_id' => $chat_id, 'text' => "[Cosa sta accadendo qui?]", 'reply_markup' =>$forcehide, 'reply_to_message_id' =>$bot_request_message_id);
				$bot_request_message=$telegram->sendMessage($content);
				
				//memorizzare nel DB
				$obj=json_decode($bot_request_message);
				$id=$obj->result;
				$id=$id->message_id;
				//print_r($id);
				$statement = "INSERT INTO ". DB_TABLE_GEO. " (lat,lng,user,text,bot_request_message) VALUES ('" . $lat . "','" . $lng . "','" . $user_id . "',' ','". $id ."')";
            	$db->exec($statement);
		}
		
		
}

?>
