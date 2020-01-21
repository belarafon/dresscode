<?php

/**
 * Providing invoice functions for plugin
 *
 *
 * @link       http://morkva.co.ua
 * @since      1.0.0
 *
 * @package    morkvaup-plugin
 * @subpackage morkvaup-plugin/public/partials
 */

class MUP_Plugin_Invoice extends MUP_Plugin_Invoice_Controller {

	public $api_key;

	public $order_id;

	#--------------Here Is Sender Data Block -------------

	public $sender_ref;

	public $sender_names;

	public $sender_first_name;

	public $sender_middle_name;

	public $sender_last_name;

	public $sender_city;

	public $sender_phone;

	public $sender_contact;

	public $sender_contact_phone;

	public $sender_address;

	public $sender_street;

	public $sender_warehouse_number;

	public $sender_area;

	public $sender_building;

	public $sender_flat;

	#-------------       Recipient(Set Data) Is Here      -----------

	public $recipient_city;

	public $recipient_city_ref;

	public $recipient_area;

	public $recipient_area_regions;

	public $recipient_area_ref;

	public $recipient_address_name;

	public $recipient_house;

	public $recipient_flat;

	public $recipient_name;

	public $recipient_phone;

	public $datetime;

	public $invoice_description;

	#-------------       Cargo(Set Data) Is Here      -----------

	public $cargo_type;

	public $cargo_weight;

	public $cost;

	public $payer;

	public $price;

	public $redelivery;

	public $order_price;

	public $invoice_x;

	public $invoice_y;

	public $invoice_z;

	public $volume_general;

	public $invoice_places;

	public $invoice_volume;

	#-------------       Register(Set Data) Is Here      -----------

	public function register()
	{
		$this->api_key = get_option('text_example');

		$invoiceController = new MUP_Plugin_Invoice_Controller();

		$invoiceController->setPosts();

		#---------- Sender POST Data ----------

		$this->sender_names = $invoiceController->sender_names;
		$this->sender_city = $invoiceController->sender_city;
		$this->sender_phone = $invoiceController->sender_phone;
		$this->sender_contact = $invoiceController->sender_contact;
		$this->sender_contact_phone = $invoiceController->sender_contact_phone;
		# $this->sender_street = $invoiceController->sender_street;
		# $this->sender_building = $invoiceController->sender_building;

		#---------- Recipient POST Data ----------

		$this->recipient_city = $invoiceController->recipient_city;
		$this->recipient_area_regions = $invoiceController->recipient_area_regions;
		# $this->recipient_address_name = $invoiceController->recipient_address_name;
		$this->recipient_house = $invoiceController->recipient_house;
		$this->recipient_name = $invoiceController->recipient_name;
		$this->recipient_phone = $invoiceController->recipient_phone;

		$this->invoice_description = $invoiceController->invoice_description;

		#---------- Cargo POST Data ----------

		$this->cargo_type = $invoiceController->cargo_type;
		$this->cargo_weight = $invoiceController->cargo_weight;
		$this->datetime = $invoiceController->datetime;
		$this->payer = $invoiceController->payer;
		$this->price = $invoiceController->price;
		$this->redelivery = $invoiceController->redelivery;

		$this->invoice_x = $invoiceController->invoice_x;
		$this->invoice_y = $invoiceController->invoice_y;
		$this->invoice_z = $invoiceController->invoice_z;

		$this->places = $invoiceController->invoice_places;
		$this->invoice_volume = $invoiceController->invoice_volume;

		return $this;

	}

	#------------- Functions For Creating Sender Is Here -----------

	public function getCitySender()
	{
		$invoiceController = new MUP_Plugin_Invoice_Controller();

		$url = "https://www.ukrposhta.ua/ecom/0.0.1/";

		/**
		 * Getting settings of WooShipping plugin
		 */

		$shipping_settings = get_option('woocommerce_nova_poshta_shipping_method_settings');
		$this->sender_city = $shipping_settings["city_name"];

		$methodProperties = array(
			"FindByString" => $this->sender_city
		);

		$senderCity = array(
			"modelName" => "Address",
			"calledMethod" => "getCities",
			"methodProperties" => $methodProperties,
			"apiKey" => get_option('text_example')
		);

		$curl = curl_init();

		MUP_Plugin_Invoice_Controller::createRequest( $url, $senderCity, $curl );

		$response = curl_exec( $curl );
		$err = curl_error( $curl );

		if ( $err ) {
			exit('Вибачаємось, але сталась помилка');
		} else {
			$obj = json_decode($response, true);
			$this->sender_city = $obj["data"][0]["Ref"];

			// echo "<pre><b>POST data: </b>";
			// var_dump($_POST);
			// echo "</pre>";

			// echo "<pre><b>Sender City: </b>";
			// var_dump($response);
			// echo "</pre>";
		}

	}

	public function getSender()
	{

		$invoiceController = new MUP_Plugin_Invoice_Controller();

		$url = "";

		$names = $this->sender_names;
		$names = explode(" ", $names);

		$this->sender_middle_name = $names[0];
		$this->sender_last_name = $names[2];
		$this->sender_first_name = $names[1];

		/**
		 * Getting settings of WooShipping plugin
		 */

		$shipping_settings = get_option('woocommerce_nova_poshta_shipping_method_settings');
		$sender_city = $shipping_settings["city_name"];

		$methodProperties = array(
			"CounterpartyProperty" => "Sender",
			"FirstName" => $this->sender_first_name,
			"MiddleName" => $this->sender_middle_name,
			"LastName" => $this->sender_last_name,
			"City" => $this->sender_city,
			"Phone" => $this->sender_phone,
			"Page" => "1"
		);

		$senderCounterparty = array(
			"apiKey" => $this->api_key,
			"modelName" => "Counterparty",
			"calledMethod" => "getCounterparties",
			"methodProperties" => $methodProperties
		);

		$curl = curl_init();

		MUP_Plugin_Invoice_Controller::createRequest( $url, $senderCounterparty, $curl );

		$response = curl_exec( $curl );
		$err = curl_error( $curl );

		if( $err ) {
			exit('Вибачаємось, але сталась помилка.');
		} else {
			$obj = json_decode( $response, true );
			$this->sender_ref = $obj["data"][0]["Ref"];

			// echo "<pre><b>Sender Ref: </b>";
			// var_dump($response);
			// echo "</pre>";
		}

		return $this;

	}

	public function createSenderContact()
	{

		$invoiceController = new MUP_Plugin_Invoice_Controller();

		$methodProperties = array(
			"Ref" => $this->sender_ref
		);

		$senderAddress = array(
			"apiKey" => $this->api_key,
			"modelName" => "Counterparty",
			"calledMethod" => "getCounterpartyContactPersons",
			"methodProperties" => $methodProperties
		);

		$url = "";

		$curl = curl_init();

		MUP_Plugin_Invoice_Controller::createRequest( $url, $senderAddress, $curl );

		$response = curl_exec( $curl );
		$err = curl_error( $curl );
		curl_close( $curl );

		if ( $err ) {
			exit('Вибачте, але сталась помилка');
		} else {
			$obj = json_decode( $response, true );
			$this->sender_contact = $obj["data"][0]["Ref"];

			// echo "<pre><b>Sender contact: </b>";
			// var_dump($response);
			// echo "</pre>";
		}

		return $this;

	}

	public function senderFindArea()
	{
		$invoiceController = new MUP_Plugin_Invoice_Controller();

		$methodProperties = array(
			"Ref" => $this->sender_city
		);

		$senderArea = array(
			"modelName" => "Address",
			"calledMethod" => "getCities",
			"methodProperties" => $methodProperties,
			"apiKey" => $this->api_key
		);

		$url = "";

		$curl = curl_init();

		MUP_Plugin_Invoice_Controller::createRequest( $url , $senderArea, $curl);

		$response = curl_exec( $curl );
		$err = curl_error( $curl );

		if ( $err ) {
			exit('Вибачаємось, але сталсь помилка');
		} else {
			$obj = json_decode( $response, true );
			$this->sender_area = $obj["data"][0]["Area"];

			// echo "<pre><b>Sender area:</b>";
			// var_dump($response);
			// echo "</pre>";
		}

		return $this;
	}

	public function senderFindStreet()
	{

		$shipping_settings = get_option('woocommerce_nova_poshta_shipping_method_settings');
		$warehouse = $shipping_settings["warehouse_name"];
		$warehouse_full = explode(" ", $warehouse);

		$warehouse_number = $warehouse_full[1];

		$warehouse_number = str_replace("№", "", $warehouse_number);

		$new_arr = implode(" ", $warehouse_full);
		// var_dump($new_arr);

		$sup_arr = explode(":", $new_arr);
		// var_dump($sup_arr);

		$street_name = $sup_arr[1];
		$street_name = trim($street_name);
		// var_dump($street_name);

		$street_name = explode("вул.", $street_name);
		$street_name = implode(" ", $street_name);
		$street_name = trim($street_name);
		// var_dump($street_name);

		$street_name = explode(",", $street_name);
		// var_dump($street_name);

		$street_name_full = $street_name[0];
		$street_number = $street_name[1];
		$street_number = trim($street_number);

		$this->sender_street = $street_name_full;
		$this->sender_building = $street_number;
		$this->sender_warehouse_number = $warehouse_number;

		$invoiceController = new MUP_Plugin_Invoice_Controller();

		$methodProperties = array(
			"CityRef" => $this->sender_city,
			"FindByString" => $this->sender_street
		);

		$senderStreet = array(
			"modelName" => "Address",
			"calledMethod" => "getStreet",
			"methodProperties" => $methodProperties,
			"apiKey" => $this->api_key
		);

		$curl = curl_init();

		$url = "";

		MUP_Plugin_Invoice_Controller::createRequest( $url, $senderStreet, $curl);

		$response = curl_exec( $curl );
		$err = curl_error( $curl );
		curl_close( $curl );

		if ( $err ) {
			exit('Вибачаємось, але сталась помилка');
		} else {
			$obj = json_decode( $response, true );
			$data = get_option('woocommerce_nova_poshta_shipping_method_settings');
			$this->sender_street = $data["warehouse"];
			$street_name = $obj["data"][0]["Description"];

			// echo "<pre><b>Sender street: </b>";
			// var_dump($response);
			// echo "</pre>";
		}

		return $this;

	}

	public function createSenderAddress()
	{

		$invoiceController = new MUP_Plugin_Invoice_Controller();

		$methodProperties = array(
			"CounterpartyRef" => $this->sender_ref,
			"StreetRef" => $this->sender_street,
			"BuildingNumber" => $this->sender_building,
			"Flat" => "1"
		);

		$senderAddress = array(
			"modelName" => "Address",
			"calledMethod" => "save",
			"methodProperties" => $methodProperties,
			"apiKey" => $this->api_key
		);

		$curl = curl_init();

		$url = "";

		MUP_Plugin_Invoice_Controller::createRequest( $url, $senderAddress, $curl );

		$response = curl_exec( $curl );
		$err = curl_error( $curl );
		curl_close( $curl );

		if ( $err ) {
			exit('Вибачаємось, але сталась помилка');
		} else {
			$obj = json_decode( $response, true );
			$this->sender_address = $obj["data"][0]["Ref"];

			// echo "<pre><b>Sender address: </b>";
			// var_dump($response);
			// echo "</pre>";
		}

		return $this;

	}

	public function findRecipientArea()
	{

		$invoiceController = new MUP_Plugin_Invoice_Controller();

		global $wpdb;

		$my_row = $wpdb->get_row("SELECT ref FROM {$wpdb->prefix}nova_poshta_city WHERE description = '$this->recipient_city' OR description_ru = '$this->recipient_city'");

		/* Getting city data from curl */

		$arrayMyRow = (array) $my_row;

		$this->recipient_city_ref = $arrayMyRow["ref"];

		$curl_city = curl_init();

		$url = "";

		$cityMethodProperties = array(
			"Ref" => $arrayMyRow["ref"]
		);

		$recipientCity = array(
			"modelName" => "Address",
			"calledMethod" => "getCities",
			"methodProperties" => $cityMethodProperties,
			"apiKey" => get_option('text_example')
		);

		MUP_Plugin_Invoice_Controller::createRequest($url, $recipientCity, $curl_city);

		$city_response = curl_exec($curl_city);
		$city_err = curl_error($curl_city);
		curl_close($curl_city);

		$obj_city = json_decode($city_response, true);
		$this->recipient_city = $obj_city["data"][0]["Description"];
		$this->recipient_area_ref = $obj_city["data"][0]["Area"];

		/* Getting Recipient Area */

		$methodProperties = array(
			"Ref" => $this->recipient_city
		);

		$recipientArea = array(
			"modelName" => "AddressGeneral",
			"calledMethod" => "getSettlements",
			"methodProperties" => $methodProperties,
			"apiKey" => $this->api_key
		);

		$curl = curl_init();

		$url = "";

		MUP_Plugin_Invoice_Controller::createRequest( $url, $recipientArea, $curl );

		$response = curl_exec( $curl );
		$err = curl_error( $curl );
		curl_close( $curl );

		if ( $err ) {
			exit('Вибачаємось, але сталась помилка');
		} else {
			$obj = json_decode( $response, true );
			$this->recipient_area = $obj["data"][0]["AreaDescription"];
			$this->recipient_city_ref = $obj["data"][0]["Ref"];

			// echo "<pre><b>Recipient area: </b>";
			// var_dump($response);
			// echo "</pre>";
		}

		return $this;

	}

	public function newFindRecipientArea()
	{
		$invoiceController = new MUP_Plugin_Invoice_Controller();

		global $wpdb;

		$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}nova_poshta_city WHERE description = '$this->recipient_city' OR description_ru = '$this->recipient_city'", ARRAY_A);

		$this->recipient_city_ref = $results[0]["ref"];

		$curl_city = curl_init();

		$url = "";

		$methodProperties = array(
			"Ref" => $this->recipient_city_ref
		);

		$recipientCityRef = array(
			"modelName" => "Address",
			"calledMethod" => "getCities",
			"methodProperties" => $methodProperties,
			"apiKey" => $this->api_key
		);

		$invoiceController->createRequest($url, $recipientCityRef, $curl_city);

		$response = curl_exec($curl_city);
		$err = curl_error($curl_city);
		curl_close($curl_city);

		if ( $err ) {
			exit('Error');
		} else {
			$obj_city = json_decode( $response, true );
		}
	}

	public function createRecipient()
	{
		$recipient_names = $this->recipient_name;
		$recipient_names = explode(" ", $recipient_names);

		$first_name = $recipient_names[1];
		if ( isset( $recipient_names[2] ) ) { $middle_name = $recipient_names[2]; }
		$last_name = $recipient_names[0];

		$invoiceController = new MUP_Plugin_Invoice_Controller();

		$methodProperties = array(
			"CityRef" => $this->recipient_city,
			"FirstName" => $first_name,
			"MiddleName" => $middle_name,
			"LastName" => $last_name,
			"Phone" => $this->recipient_phone,
			"Email" => "",
			"CounterpartyType" => "PrivatePerson",
			"CounterpartyProperty" => "Recipient"
		);

		$counterpartyRecipient = array(
			"apiKey" => $this->api_key,
			"modelName" => "Counterparty",
			"calledMethod" => "save",
			"methodProperties" => $methodProperties
		);

		$curl = curl_init();

		$url = "";

		MUP_Plugin_Invoice_Controller::createRequest( $url, $counterpartyRecipient, $curl );

		$response = curl_exec( $curl );
		$err = curl_error( $curl );

		if ( $err ) {
			exit('Вибачаємось, але сталась помилка');
		} else {
			$obj = json_decode( $response, true );

		}

	}

	public function howCosts()
	{

		$invoiceController = new MUP_Plugin_Invoice_Controller();

		$methodProperties = array(
			"CitySender" => $this->sender_city,
			"CityRecipient" => $this->recipient_city_ref,
			"Weight" => $this->cargo_weight,
			"ServiceType" => "WarehouseWarehouse",
			"Cost" => "100",
			"SeatsAmount" => "1"
		);

		$costs = array(
			"modelName" => "InternetDocument",
			"calledMethod" => "getDocumentPrice",
			"methodProperties" => $methodProperties,
			"apiKey" => $this->api_key
		);

		$curl = curl_init();

		$url = "";

		MUP_Plugin_Invoice_Controller::createRequest( $url, $costs, $curl );

		$response = curl_exec( $curl );
		$err = curl_error( $curl );
		curl_close( $curl );

		if ( $err ) {
			exit('Вибачаємось, але сталась помилка');
		} else {
			$obj = json_decode( $response, true );
			$this->cost = $obj["data"][0]["Cost"];
		}

		return $this;

	}

	public function createVolumeGeneral()
	{

		$this->invoice_x = intval( $this->invoice_x );
		$this->invoice_y = intval( $this->invoice_y );
		$this->invoice_z = intval( $this->invoice_z );

		echo '<pre>';
		var_dump( $this->invoice_x );
		var_dump( $this->invoice_y );
		var_dump( $this->invoice_z );
		echo '</pre>';

		$result_code = ( $this->invoice_x * $this->invoice_z * $this->invoice_y ) / 4000;

		$this->volume_general = $result_code;

		echo '<pre>';
		var_dump( $result_code );
		echo '</pre>';


		return $this->volume_general;
	}

	public function createInvoice()
	{

		if ( empty($this->price )) {
			$this->price = $this->order_price;
		} else if ( ! empty( $this->price ) ) {
			$this->price = $this->price;
		}

		$invoiceController = new MUP_Plugin_Invoice_Controller();

		$wooshipping_settings = get_option('woocommerce_nova_poshta_shipping_method_settings');
		$this->sender_address = $wooshipping_settings["warehouse"];

		// $invoice_weight = get_option( 'invoice_weight' );

		/*if ( isset( $this->invoice_x ) or isset( $this->invoice_y ) ) {
			$this->volume_general = ( $this->invoice_x * $this->invoice_y * $this->invoice_z );
		}*/

		/*echo '<pre>';
		var_dump(  $this->invoice_x * $this->invoice_y * $this->invoice_z );
		var_dump( $this->volume_general / 4000 );
		echo '</pre>';*/

		if ( empty( $this->invoice_volume ) ) {
			$this->invoice_volume = 0.005;
		}

		if ( empty( $this->cargo_weight ) ) {
				$this->cargo_weight = 0.5;
		}

		if ( $this->redelivery == "ON" ) {
			$backwardDeliveryData = array(
				"PayerType" => $this->payer,
				"CargoType" => "Money",
				"RedeliveryString" => $this->price
			);

			$methodProperties = array(
				"NewAddress" => "1",
				"PayerType" => $this->payer, // By default - Recipient
				"PaymentMethod" => "Cash",
				"CargoType" => $this->cargo_type,
				"Weight" => $this->cargo_weight,
				"ServiceType" => "WarehouseWarehouse",
				"SeatsAmount" => $this->places,
				"Description" => $this->invoice_description,
				"Cost" => $this->price,
				"CitySender" => $this->sender_city,
				"Sender" => $this->sender_ref,
				"SenderAddress" => $this->sender_address,
				"ContactSender" => $this->sender_contact,
				"SendersPhone" => $this->sender_phone,
				"RecipientCityName" => $this->recipient_city,
				"RecipientArea" => $this->recipient_area_regions,
				"RecipientAreaRegions" => $this->recipient_area_regions,
				"RecipientAddressName" => $this->recipient_address_name,
				"RecipientHouse" => $this->recipient_address_name,
				"RecipientFlat" => "1",
				"RecipientName" => $this->recipient_name,
				"RecipientType" => "PrivatePerson",
				"RecipientsPhone" => $this->recipient_phone,
				"DateTime" => $this->datetime,
				"InfoRegClientBarcodes" => $this->order_id,
				"BackwardDeliveryData" => array(
					$backwardDeliveryData,
				)
			);
		} else if ( empty( $this->invoice_volume ) &&  empty($this->redelivery) ) {
			$methodProperties = array(
				"NewAddress" => "1",
				"PayerType" => $this->payer, // By default - Recipient
				"PaymentMethod" => "Cash",
				"CargoType" => $this->cargo_type,
				"Weight" => $this->cargo_weight,
				"ServiceType" => "WarehouseWarehouse",
				"SeatsAmount" => $this->places,
				"Description" => $this->invoice_description,
				"Cost" => $this->price,
				"CitySender" => $this->sender_city,
				"Sender" => $this->sender_ref,
				"SenderAddress" => $this->sender_address,
				"ContactSender" => $this->sender_contact,
				"SendersPhone" => $this->sender_phone,
				"RecipientCityName" => $this->recipient_city,
				"RecipientArea" => $this->recipient_area_regions,
				"RecipientAreaRegions" => $this->recipient_area_regions,
				"RecipientAddressName" => $this->recipient_address_name,
				"RecipientHouse" => $this->recipient_address_name,
				"RecipientFlat" => "1",
				"RecipientName" => $this->recipient_name,
				"RecipientType" => "PrivatePerson",
				"RecipientsPhone" => $this->recipient_phone,
				"DateTime" => $this->datetime,
				"InfoRegClientBarcodes" => $this->order_id
			);
		} else if ( isset( $this->invoice_volume ) && $this->redelivery != "ON" ) {
			$methodProperties = array(
				"NewAddress" => "1",
				"PayerType" => $this->payer, // By default - Recipient
				"PaymentMethod" => "Cash",
				"CargoType" => $this->cargo_type,
				"VolumeGeneral" => $this->invoice_volume,
				"Weight" => $this->cargo_weight,
				"ServiceType" => "WarehouseWarehouse",
				"SeatsAmount" => $this->places,
				"Description" => $this->invoice_description,
				"Cost" => $this->price,
				"CitySender" => $this->sender_city,
				"Sender" => $this->sender_ref,
				"SenderAddress" => $this->sender_address,
				"ContactSender" => $this->sender_contact,
				"SendersPhone" => $this->sender_phone,
				"RecipientCityName" => $this->recipient_city,
				"RecipientArea" => $this->recipient_area_regions,
				"RecipientAreaRegions" => $this->recipient_area_regions,
				"RecipientAddressName" => $this->recipient_address_name,
				"RecipientHouse" => $this->recipient_address_name,
				"RecipientFlat" => "1",
				"RecipientName" => $this->recipient_name,
				"RecipientType" => "PrivatePerson",
				"RecipientsPhone" => $this->recipient_phone,
				"DateTime" => $this->datetime,
				"InfoRegClientBarcodes" => $this->order_id
			);
		} else if ( isset( $this->invoice_volume ) && $this->redelivery == "ON" ) {

			$methodProperties = array(
				"NewAddress" => "1",
				"PayerType" => $this->payer, // By default - Recipient
				"PaymentMethod" => "Cash",
				"CargoType" => $this->cargo_type,
				"VolumeGeneral" => $this->invoice_volume,
				"Weight" => $this->cargo_weight,
				"ServiceType" => "WarehouseWarehouse",
				"SeatsAmount" => $this->places,
				"Description" => $this->invoice_description,
				"Cost" => $this->price,
				"CitySender" => $this->sender_city,
				"Sender" => $this->sender_ref,
				"SenderAddress" => $this->sender_address,
				"ContactSender" => $this->sender_contact,
				"SendersPhone" => $this->sender_phone,
				"RecipientCityName" => $this->recipient_city,
				"RecipientArea" => $this->recipient_area_regions,
				"RecipientAreaRegions" => $this->recipient_area_regions,
				"RecipientAddressName" => $this->recipient_address_name,
				"RecipientHouse" => $this->recipient_address_name,
				"RecipientFlat" => "1",
				"RecipientName" => $this->recipient_name,
				"RecipientType" => "PrivatePerson",
				"RecipientsPhone" => $this->recipient_phone,
				"DateTime" => $this->datetime,
				"InfoRegClientBarcodes" => $this->order_id,
				"BackwardDeliveryData" => array(
					$backwardDeliveryData,
				)
			);
		}



		// echo "<pre><b>Invoice Method properties: </b>";
		// print_r(json_encode($methodProperties));
		// echo "</pre>";

		$invoice = array(
			"apiKey" => $this->api_key,
			"modelName" => "InternetDocument",
			"calledMethod" => "save",
			"methodProperties" => $methodProperties
		);

		$curl = curl_init();

		$url = "";

		MUP_Plugin_Invoice_Controller::createRequest( $url, $invoice, $curl );

		$response = curl_exec( $curl );
		$err = curl_error( $curl );
		curl_close( $curl );

		if ( $err ) {
			print_r($err);
			exit('Вибачаємось, але сталась помилка');
		} else {
			$obj = json_decode( $response, true );
			$document_number = $obj["data"][0]["Ref"];
			$document_id = $obj["data"][0]["IntDocNumber"];

			session_start();

			// echo "<pre>";
			// var_dump($response);
			// echo "</pre>";



			$_SESSION['invoice_id'] = $document_id;


			$invoiceforerror = array(
			"apiKey" => $this->api_key,
			"modelName" => "CommonGeneral",
			"calledMethod" => "getMessageCodeText",
			"methodProperties" => $methodProperties
			);

			$curlforerror = curl_init();

			$urlforerror = "";

			MUP_Plugin_Invoice_Controller::createRequest( $urlforerror, $invoiceforerror, $curlforerror );

			$responseforerror = curl_exec( $curlforerror );
			$errforerror = curl_error( $curlforerror );
			curl_close( $curlforerror );
			$objforerror = json_decode( $responseforerror, true );
			//print_r($objforerror['data']);

			$newarray = null;

			for($i = 0 ; $i < sizeof($objforerror['data']); $i++ ){

				$mc = $objforerror['data'][$i]['MessageCode'];
				$ua = $objforerror['data'][$i]['MessageDescriptionUA'];
				$ru = $objforerror['data'][$i]['MessageDescriptionRU'];
				$eng = $objforerror['data'][$i]['MessageText'];

				$newarray[$mc]['ua'] = $ua;
			}
			echo '<hr>';


			$errors = $obj["errorCodes"];


			$errors0 = $obj;


			$error = $obj["errorCodes"][0];

			if ( $error ) {

				/*echo "
				<div class='container'>
					<div class='card text-white bg-danger'>
						<h3>Помилка</h3>
						<p>
							Код помилки: " . $errors . "
						</p>
					</div>
				</div>
				";*/

				echo "<div id='errno' class='container'>";
					echo "<div class='card text-white bg-danger'>";
						echo "<h3>Помилка</h3>";
						echo "<p>  ";
							foreach ( $errors as $code ) {
								echo $newarray[$code]['ua'] . "<br>" . " ";
							}


						echo "</p>";
						echo "<p> Коди помилки: ";
							foreach ( $errors as $code ) {
								echo $code . ";" . " ";
							}
						echo '</p><div class="clr"></div>';
					echo "</div>";
				echo "</div>";

				exit;
			}

			echo "
			<div id='nnnid' class='container'>
				<div class='sucsess-naklandna'>
					<h3>Накладна успішно створена!</h3>
					<p>
						Номер накладної: " . $_SESSION['invoice_id'] . "
					</p>
				</div>
			</div>
			";



			global $wpdb;

			$invoice_number = $obj["data"][0]["IntDocNumber"];
			$invoice_ref = $obj["data"][0]["Ref"];

			$table_name = $wpdb->prefix . MUP_TABLEDB;

			$wpdb->insert(
				$table_name,
				array(
					'order_id' => $this->order_id,
					'order_invoice' => $invoice_number,
					'invoice_ref' => $invoice_ref
				)
			);

			$_SESSION['invoice_id_for_order'] = $_SESSION['invoice_id'];
			unset( $_SESSION['invoice_id'] );

		}

		return $this;

	}

}
