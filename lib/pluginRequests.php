<?php

	require_once('doppler-client.php');
	
	$action = null;
	$error = false;
	$error_msg = array();
	$response = array();
	try{
		if(isset($_POST['action']))
			$action = $_POST['action'];
		else throw new Exception("Action wasn't specified", 1);

		switch ($action) {
			case 'load_api_key':
				$apiKey = $_POST['args']['api_key'];
				$client = new DopplerClient($apiKey);
				$result = $client->user->testApiConnection(); 
				$response[] = $result;
				break;
			case 'add_subscribers':
				$apiKey = $_POST['args']['api_key'];
				$client = new DopplerClient($apiKey);
				$listIdsArray = explode(',', $_POST['args']['list_ids']);
				foreach($listIdsArray as $listID){
					$result = $client->subscriber->addSubscriber(array( 'SubscribersListID' => trim($listID) , 'EMail' => $_POST['args']['email'] )); 
					$response[] = $result;
				}
				
				break;
			default:
				# code...
				break;
		}
	} catch(Exception  $e){
		$response[] = $e; 
	}
	
	 echo json_encode(array('responses' => $response));	
?>