<?php


require_once('resource.php');

/**
 * DopplerClient
 * @author Lucas Marquez <lmarquez@makingsense.com>, <lucasasecas@gmail.com>
 * @version 1.0.0
 */



class DopplerClient{

	
	/**
	 * Endpoint to doppler api
	 * @var string
	 */
	var $_url  = 'http://api2.fromdoppler.com/Default.asmx?WSDL';

	/**
	 * Resource Campaign
	 * @var CampaignResource extends Resource
	 */
	public $campaign;

	/**
	 * User resource
	 * @var ClientResource  extends Resource
	 */
	public $user;

	/**
	 * Field Resource
	 * @var FieldResource extends Resource
	 */
	public $field;

	/**
	 * Subscriber Resource
	 * @var FieldSource $subscriber
	 */
	public $subscriber;

	/**
	 * 
	 * 
	 * 
	
	var $_soapClient;

	/**
	 * Class constructor 
	 * @param String
	 */
	function DopplerClient($apiKey){
		
		$this->_soapClient = new SoapClient('http://api2.fromdoppler.com/Default.asmx?WSDL');
		$this->_apiKey = $apiKey;

/*		$this->campaign = new CampaignResource(
			$this,
			array(
				'functions' => array(
					'getBouncesList' => array(
						'methodApiName' => 'Campaign.GetBouncesList',
						),
					'getHardBouncesList' => array(
						'methodApiName' => 'Campaign.GetHardBouncesList'
						),
					'getSoftBouncesList' => array(
						'methodApiName' => 'Campaign.GetSoftBouncesList' 
						)
					'getSubscribersListsByCampaign' => array(
						'methodApiName' => 'Campaign.GetSubscribersLists' 
						),
					'getUnsubscribesList' => array(
						'methodApiName' => 'Campaign.GetUnsubscribesList' 
						)
					)
				)
			);*/

		$this->user = new ClientResource(
			$this,
			array(
				'functions' => array(
					'addSubscribers' => array(
						'methodApiName' => 'AddSubscribers',
						),
					'getCampaignsList' => array(
						'methodApiName' => 'GetCampaignsList'
						),
					'getLastSentCampaign ' => array(
						'methodApiName' => 'GetLastSentCampaign' 
						),
					'getSubscribersLists' => array(
						'methodApiName' => 'GetSubscribersLists' 
						),
					'testApiConnection' => array(
						'methodApiName' => 'TestApiConnection')
					)
				)
			);

		$this->subscriber = new SubscriberResource(
			$this,
			array(
				'functions' => array(
					'addSubscriber' => array(
						'methodApiName' => 'AddSubscriber',
						)
					)
				)
			);

		// $this->field = new FieldResource(
		// 	$this,
		// 	array(
		// 		'functions' => array(
		// 			'addField' => array(
		// 				'methodApiName' => '"Field.Add',
		// 				),
		// 			'getFields' => array(
		// 				'methodApiName' => 'Field.GetFields'
		// 				)					
		// 			)
		// 		)
		// 	);
	}
	public function soapCall($method, $args = null){

		$result = false;
		$retry_count = 0;
		$max_retry = 5;

		$apiKeyArgs = array('APIKey' => $this->_apiKey);

		$args = $args == null ? $apiKeyArgs :  array_merge($apiKeyArgs, $args);
		


		while(!$result && $retry_count < $max_retry) {

			try{
				$result = $this->_soapClient->$method($args);
				return $result;

			}catch (SoapFault $fault){
				if($fault->faultstring != 'Could not connect to host')
		        {
		          throw $fault;
		        }
			}
			sleep(1);
		    $retry_count ++;
		}

		if($retry_count == $max_retry) 
	    {
	      throw new SoapFault('Could not connect to host after 5 attempts');
	    }
	    return $result;
	}

}

class ClientResource extends Resource{

		public function addSubscribers($args = null){

			return $this->call('addSubscribers' , $args);
		}

		public function getCampaignsList($args = null){
			return $this->call('getCampaignsList' , $args);
		}

		public function getLastSentCampaign($args = null){
			return $this->call('getLastSentCampaign' , $args);
		}

		public function getSubscribersLists($args = null){
			return $this->call('getSubscribersLists' , $args);
		}

		public function testApiConnection ($args = null){
			return $this->call('testApiConnection', $args);
		}
	}



class SubscriberResource extends Resource{

		public function addSubscriber($args = null){
			return $this->call('addSubscriber' , $args);
		}

		
	}
?>