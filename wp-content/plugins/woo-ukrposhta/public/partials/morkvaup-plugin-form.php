<?php

session_start();

require("morkvaup-plugin-invoice-controller.php");
include("morkvaup-plugin-invoice.php");
require("api.php");

function logg($string){
  echo '<script>console.log("'.$string.'");</script>';
}
//встановлюємо змінні станів
$showpage = true;
$message = '';
$requested = false;
$failed = false;

//отримуємо id замовлення
if( (isset($_SERVER['HTTP_REFERER'])) && (isset($output['post'])) ){
    $qs = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
    if(!empty($qs)){//якщо ? get не пусті то отримуємо id  $_GET [post]
        parse_str($qs, $output); $order_id =  $output['post'];
      }
}
if(isset($order_id)){//встановлено order_id з попередньої умови
        logg('встановлено order_id ');
        $order_data0 = wc_get_order( $order_id );
        $order_data = $order_data0->get_data();
        $_SESSION['order_id'] = $order_id;
}
else if ( isset($_SESSION['order_id']) ) {
        logg('else if встановлено  з сесії order_id');
        $order_id = $_SESSION['order_id'];
        $order_data0 = wc_get_order( $order_id );
        $order_data = $order_data0->get_data();
}
else{
        logg('else $showpage=false');
        $showpage =false;
}

//print_r($order_data);
$invoice = array();
$bearer = get_option('production_bearer_ecom');
$cptoken = get_option('production_cp_token');
$tbearer = get_option('production_bearer_status_tracking');
logg('setted up $bearer $cptoken $tbearer');

//$bearer = get_option('sandbox_bearer_ecom');
//$cptoken = get_option('sandbox_cp_t');
//$tbearer = get_option('sandbox_bearer_st');
//logg('setted up sandbox $bearer $cptoken $tbearer');

$ukrposhtaApi = new UkrposhtaApi($bearer ,$cptoken, $tbearer);
logg('created  istance of $ukrposhtaApi');

//створення адрес #1  якщо встановлені індекси але відправлення не міжнародне
if(isset($_POST['index1']) && isset($_POST['index2']) && !isset($_POST['international']) ) {
    logg('001 - встановлені індекси але відправлення не міжнародне');
    $address1 = $ukrposhtaApi->modelAdressPost(array("postcode"=>$_POST['index1']  ));
    if(isset($address1['id'])){
      $address1_id = $address1['id'];
    }
    else{
      $failed = true;
  	  $message .= 'Проблема з індексом відправника. ';
      $message .= 'not set($address1[id]. ';
    }
    $address2 = $ukrposhtaApi->modelAdressPost(array("postcode"=>$_POST['index2'] ));
    if(!isset($address2['id'])){
    	$failed = true;
	    $message .= 'Проблема з індексом отримувача. ';
    }
    else{
      $address2_id = $address2['id'];
    }
}
//кінець створення адрес #1

// створення адрес #2 якщо встановлені індекси. і відправлення міжнародне але не встановлений id адреси відправника
if(isset($_POST['index1']) && isset($_POST['index2']) && isset($_POST['international']) && !isset($_POST['address1id'])) {
    logg('002 - встановлені індекси. і відправлення міжнародне але не встановлений id адреси відправника');
    $address1 = $ukrposhtaApi->modelAdressPost(array(
      "country"=>"UA",
      "city"=>$_POST['city0'], "region"=> $_POST['city0'], "postcode"=>$_POST['index1'], "street"=> $_POST['street'], "houseNumber"=>$_POST['housenumber']));
    if(isset($address1['id'])){
      $address1_id = $address1['id'];  //print_r($address1); echo '<hr>';
      }
    else{
      $failed=true;
      $message .= 'Проблема з індексом відправника. ';
      $message .= 'not set($address1[id]. ';
      //print_r($address1); echo '<hr>';
      }
    $address2 = $ukrposhtaApi->modelAdressPost(array(
      "country"=>$_POST['country'],
      "city"=>$_POST['city'],
    "foreignStreetHouseApartment" => $_POST['foreignStreetHouseApartment']));
    if(!isset($address2['id'])){
      $failed = true;
      $message .= 'Проблема з адресою отримувача міжнародного відправлення. ';
    }
    else{
      $address2_id = $address2['id'];
    }
}
//кінець створення адрес #2

//створення адрес #3 якщо встановлені id відправника те індекс2 але доставка не міжнародна
if( isset($_POST['address1id']) && isset($_POST['index2'])  && !isset($_POST['international'])) {
  logg('003 - встановлені id відправника те індекс2 але доставка не міжнародна');
  $address1_id = $_POST['address1id'];
  $address2 = $ukrposhtaApi->modelAdressPost( array("postcode"=>$_POST['index2'] ) );
  $address2_id= $address2['id'];
	$message .= 'Проблеми індексами';
}
//кінець створення адрес #3

// створення адрес #4 якщо встановлений id адреси1 та індекс2 і відправлення міжнародне
if( isset($_POST['address1id']) && isset($_POST['index2'])  && isset($_POST['international'])) {
  logg('004 - встановлений id адреси1 та індекс2 і відправлення міжнародне');
  $address1_id = $_POST['address1id'];
    $address2 = $ukrposhtaApi->modelAdressPost(array(
      "country"=>$_POST['country'],
      "city"=>$_POST['city'],
    "foreignStreetHouseApartment" => $_POST['foreignStreetHouseApartment']));
  $address2_id= $address2['id'];
  $message .= 'Проблеми міжнародним відправленням';
}
//кінець створення адрес #4

//створення клієнтів
if(isset($_POST['phone1']) && isset($_POST['phone2']) && !$failed && !isset($_POST['international'])){
  logg('005 - встановлені телефони 1 і 2 && !failed && !international');
  if( isset( $_POST['sender_first_name'] ) && isset( $_POST['sender_last_name'] ) &&  isset( $_POST['rec_first_name'] )  && isset( $_POST['rec_last_name'] ) ) {
    $name11 = $_POST['sender_first_name'];
    $name12 = $_POST['sender_last_name'];
    $phone1 = $_POST['phone1'];
    $name21 = $_POST['rec_first_name'];
    $name22 = $_POST['rec_last_name'];
    $phone2 = $_POST['phone2'];
    //створення клієнта  відправника
    $client1 = $ukrposhtaApi->modelClientsPost(array(
    "type"=>"INDIVIDUAL",
    "name"=> $name11.' '.$name12,
    "firstName"=> $name11,
    "lastName"=> $name12,
    "addressId"=> $address1_id,
    "phoneNumber"=> $phone1
    ));
    //створення клієнта отримувача
    $client2 = $ukrposhtaApi->modelClientsPost(array(
    "type"=>"INDIVIDUAL",
    "name"=> $name21.' '.$name21,
    "firstName"=> $name21,
    "lastName"=> $name22,
    "addressId"=> $address2_id,
    "phoneNumber"=> $phone2
    ));
     // echo '<pre>';
     //print_r($client1);
     // print_r($client2);
     // echo '</pre>';
  }
}
else{//failed
  $message .= 'Проблеми з правильністю телефонів';
  logg('006 - Проблеми з правильністю телефонів');
}
//створення international клієнта
if(isset($_POST['phone1']) && isset($_POST['phone2']) && !$failed  && isset($_POST['international'])){
  logg('007 - телефони є і international');
  if( isset( $_POST['sender_first_name'] ) && isset( $_POST['sender_last_name'] ) &&  isset( $_POST['rec_first_name'] )  && isset( $_POST['rec_last_name'] ) ) {
    $name11 = $_POST['sender_first_name'];
    $name12 = $_POST['sender_last_name'];
    $phone1 = $_POST['phone1'];
    $name21 = $_POST['rec_first_name'];
    $name22 = $_POST['rec_last_name'];
    $phone2 = $_POST['phone2'];
    $latinname = $_POST['latinname'];
    $latinname0 = $_POST['latinname0'];
    //створення клієнта  відправника
    $client1 = $ukrposhtaApi->modelClientsPost(array(
    "type"=>"INDIVIDUAL",
    "name"=> $name11.' '.$name12,
    "firstName"=> $name11,
    "latinName"=>$latinname0,
    "lastName"=> $name12,
    "addressId"=> $address1_id,
    "phoneNumber"=> $phone1
    ));
    //створення клієнта  отримувача
    $client2 = $ukrposhtaApi->modelClientsPost(array(
    "type"=>"INDIVIDUAL",
    "name"=> $name21.' '.$name21,
    "latinName"=>$latinname,
    "firstName"=> $name21,
    "lastName"=> $name22,
    "addressId"=> $address2_id,
    "phoneNumber"=> $phone2
    ));
    logg('008 - users set');
    //print_r($client1);
    //echo '<hr>';
    //print_r($client2);
  }
}
//кінець створення international клієнта
//створення відправлення
if( (isset($client1['uuid']) && isset($client2['uuid']) ) && !isset($_POST['international']) ){
  $invoice = $ukrposhtaApi->modelShipmentsPost(array(
   "sender" => array( "uuid" => $client1['uuid'] ),
   "recipient" => array( "uuid"=> $client2['uuid'] ),
   "type" => $_POST['sendtype'],
   "checkOnDelivery" => true,
   "deliveryType"=> $_POST['sendwtype'],
   "paidByRecipient"=> $_POST['paidByRecipient'],
   "nonCashPayment"=> false,
   "description" => $_POST['description'],
   "onFailReceiveType" => $_POST['onFailReceiveType'],
   "postPay" => $_POST['invoice_places'],
   "parcels"=> array( array(
     "weight"=> 1000 * $_POST['invoice_cargo_mass'],
     "length"=> $_POST['invoice_volume'],
     "declaredPrice" => intval($_POST['declaredPrice']),
   ) )
  ));
  logg('009 - instance $ukrposhtaApi->modelShipmentsPost');
   // echo '<pre>';
   // print_r($invoice);
   // echo '</pre>';
  if(isset($invoice['uuid'])){
    $ref = $invoice['uuid'];
    $barcode = $invoice['barcode'];
    global $wpdb;
    $query = 'INSERT INTO '.$wpdb->prefix.'uposhta_invoices (order_id, order_invoice, invoice_ref) VALUES ("'.$order_data["id"].'", "'.$barcode.'", "'.$ref.'");';
    //echo $query;
    $requested = true;
    $wpdb->query($query);
    logg('0010 - Відправлення створено і записано до бд');
  }
  else{
    logg('0011 - Помилка');
  }
}
//кінець створення відправлення
// створення international відправлення
if( (isset($client1['uuid']) || isset($client2['uuid']) ) && isset($_POST['international']) ){
    $invoice = $ukrposhtaApi->modelShipmentsPost(array(
   "sender" => array( "uuid" => $client1['uuid'] ),
   "recipient" => array( "uuid"=> $client2['uuid'] ),
   "packageType" => $_POST['senduptype'],
   "international" => true,
   "internationalData"=> array(
      "avia"=>false,
      "transportType"=>"AVIA",
      "directFlight"=>false,
      "tracked"=>true
   ),
   "parcels"=> array( array(
      "weight"=> 1000 * $_POST['invoice_cargo_mass'],
        "length"=> $_POST['invoice_volume'],
        "parcelItems"=>array(array(
            "name"=>"Shirt",
            "description"=>$_POST["description"],
            "quantity"=>4,
            "value"=>50,
            "weight"=>1,
            "countryOfOrigin"=>"UA",
      ))
   )),
  ));
  //echo '<pre>';
  //print_r($invoice);
  //echo '</pre>';

  if(isset($invoice['uuid'])){
    $ref = $invoice['uuid'];
    $barcode = $invoice['barcode'];
    global $wpdb;
    $query = 'INSERT INTO '.$wpdb->prefix.'uposhta_invoices (order_id, order_invoice, invoice_ref) VALUES ("'.$order_data["id"].'", "'.$barcode.'", "'.$ref.'");';
    //echo $query;
    $requested = true;
    $wpdb->query($query);
    logg("Відправлення international створено");

  }
  else{
    logg("Сталась помилка:".$invoice['message']);
  }

}
//кінець створення international відправлення

else{
  logg("Не задоволено жодної умови");
  //echo '<pre>';
	//p($client1);
	//print_r($client2);
	//echo '</pre>';
	$failed = true;
	if(isset($client1['message'])){
		$message .= $client1['message'].'. ';
	}if(isset($client2['message'])){
		$message .= $client2['message'].'. ';
	}
}
echo '<br>';

if ( isset($order_data["billing"]["first_name"]) ) {
    $shipping_first_name = $order_data["billing"]["first_name"];
} else if ( isset( $order_data["shipping"]["first_name"] ) ) {
  $shipping_first_name = $order_data["shipping"]["first_name"];
} else {
  $shipping_first_name = "";
}

if ( isset($order_data["billing"]["last_name"]) ) {
    $shipping_last_name = $order_data["billing"]["last_name"];
} else if ( isset($order_data["shipping"]["last_name"]) ) {
  $shipping_last_name = $order_data["shipping"]["last_name"];
} else {
  $shipping_last_name = "";
}

if ( isset($order_data["billing"]["address_2"]) ) {
   $shipping_address = $order_data["billing"]["address_2"];
  $shipping_address = explode(" ", $shipping_address);
} else if ( isset($order_data["shipping"]["address_2"]) ) {
  $shipping_address = $order_data["shipping"]["address_2"];
  $shipping_address = explode(" ", $shipping_address);
} else {
  $shipping_address[0] = "";
  $shipping_address[1] = "";
}
/* OTHER GETTING DATA FUNCTIONS */

if ( isset($order_data["billing"]["city"]) ) {
    $shipping_city = $order_data["billing"]["city"];
} else if ( isset($order_data["shipping"]["city"]) ) {
  $shipping_city = $order_data["shipping"]["city"];
} else {
  $shipping_city = "";
}

if ( isset($order_data["billing"]["state"]) ) {
  $shipping_state = $order_data["billing"]["state"];
  } else if ( isset($order_data["shipping"]["state"]) ) {
  $shipping_state = $order_data["shipping"]["state"];
} else {
  $shipping_state = "";
}

if ( isset($order_data["billing"]["phone"]) ) {
  $shipping_phone = $order_data["billing"]["phone"];
  } else if ( isset($order_data["shipping"]["phone"]) ) {
  $shipping_phone = $order_data["shipping"]["phone"];

} else {
  $shipping_phone = "";
}

function startsWith ($string, $startString)
{
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}
//
// echo '<b>$_POST</b><pre>';
// print_r($order_data);
// echo '</pre><hr>';


// start calculating alternate weight
$varia = null;
if(isset ($order_data['line_items'])){
$varia = $order_data['line_items'];
}
$alternate_weight = 0;
$dimentions = array();
$d_vol_all = 0;
$weighte = '';

$prod_quantity = 0;
$prod_quantity2 = 0;

$list = '';
$list2 = '';
$descr = '';


if(isset ($varia)){
	foreach ($varia as $item){
  	$data = $item->get_data();

    echo '<pre>';
    $quantity = ($data['quantity']);
    $quanti = $quantity;

    //$prod_quantity += $quantity;

    //$prod_quantity += 1;

    echo '</pre>';

  	$pr_id = $data['product_id'];
  	$product = wc_get_product($pr_id);
  	if ( $product->is_type('variable') ) {


    	$var_id = $data['variation_id'];
    	$variations      = $product->get_available_variations();

    	for ($i=0; $i < sizeof($variations) ; $i++){
      		if($variations[$i]['variation_id'] == $var_id ){
            //print_r($variations[$i]);

            while ($quanti > 0) {

              if (is_numeric(  $variations[$i]['weight'] )){$alternate_weight += $variations[$i]['weight'];}

              if( !($variations[$i]['weight'] > 0)  ){
                $weighte = 'Маса вказана не  для всіх товарів в кошику. Радимо уточнити цифри.';
              }

              array_push($dimentions, $variations[$i]['dimensions']);
		if ( is_numeric( $variations[$i]['dimensions']['length'] ) && is_numeric( $variations[$i]['dimensions']['width'] ) && is_numeric( $variations[$i]['dimensions']['height'] ) ){
              		$d_vol = $variations[$i]['dimensions']['length'] * $variations[$i]['dimensions']['width'] * $variations[$i]['dimensions']['height'];
	             	$d_vol_all += $d_vol;
		}
              $quanti--;
            }
                  //$product = new WC_Product($var_id);

                  $sku = $variations[$i]['sku'];
                  if(!empty($sku)){
                    $sku = '('.$sku.')';
                  }
                  $name = $product->get_title();

                  $list2  .= $name .$sku. ' x '.$quantity.'шт ;';
                  $list  .= $name .' x '.$quantity.'шт ;';
                  $prod_quantity += 1;
                  $prod_quantity2 += $quantity;


      		}
    	}



  	}
    else{


      $sku = $product->get_sku();
      if(!empty($sku)){
        $sku = '('.$sku.')';
      }
      $name = $product->get_title();

           $list2  .= $name .$sku. ' x '.$quantity.'шт ;';

           $list  .= $name . ' x '.$quantity.'шт ;';

           $prod_quantity += 1;
                  $prod_quantity2 += $quantity;


    	$diment =0;
    	if( (is_numeric($product->get_width()) ) && (is_numeric($product->get_length())) && (is_numeric($product->get_height())) ) {
    	$diment = $product->get_length() * $product->get_width() * $product->get_height();
      	$d_array = array('length'=>$product->get_length(),'width'=> $product->get_width(), 'height'=>$product->get_height() );
      	array_push($dimentions, $d_array);
      	$d_vol_all += $diment;
    	}
      while ($quantity > 0) {
        $weight = $product->get_weight();
        if ($weight > 0){
          $alternate_weight += $weight;
        }
        else {
          $weighte = 'Маса вказана не  для всіх товарів в кошику. Радимо уточнити цифри.';
        }


      $quantity--;
    }
    }
}
}


$alternate_vol=0;

$volumemessage = '';
if((sizeof($dimentions) > 1)){

  $alternate_vol = $d_vol_all;
  $volumemessage = 'УВАГА! В відправленні кілька товарів. Ми порахували арифметичний сумарний об\'єм посилки, враховуючи мета-дані товарів. Ви можете змінити об\'єм зараз вручну, щоб бути більш точним.' ;
}
else{
	if ( isset($variations) ){
		if ( is_numeric( $variations[0]['dimensions']['length'] ) &&  is_numeric( $variations[0]['dimensions']['width'] ) &&  is_numeric( $variations[0]['dimensions']['height'] ) ){
  			$alternate_vol = $variations[0]['dimensions']['length'] * $variations[0]['dimensions']['width'] * $variations[0]['dimensions']['height'];
  			$volumemessage = '';
		}
	}
}
$alternate_vol = $alternate_vol / 1000000;


?>

<script src="<?php echo MUP_PLUGIN_URL . 'public/js/script.js?ver='.MUP_PLUGIN_VERSION.' '; ?>"></script>
<link rel="stylesheet" href="<?php echo MUP_PLUGIN_URL . 'public/css/style.css?ver='.MUP_PLUGIN_VERSION.' '; ?>"/>


<nav class="newnaw nav-tab-wrapper woo-nav-tab-wrapper">
      <a href="admin.php?page=morkvaup_plugin" class="nav-tab ">Налаштування</a><a href="admin.php?page=morkvaup_invoice" class="nav-tab nav-tab-active">Нове відправлення</a><a href="admin.php?page=morkvaup_invoices" class="nav-tab ">Мої відправлення</a><a href="admin.php?page=morkvaup_about" class="nav-tab ">Про плагін</a></nav>


<div class="container">

  <?php if($showpage){ ?>

  <form class="form-invoice" action="admin.php?page=morkvaup_invoice" method="post" name="invoice">
    <?php  if($requested){ ?>
    <div id="messagebox" class="messagebox_show updated" data="186" style="height:0px;padding:0px">
				<div class="sucsess-naklandna">
					<h3>Відправлення <?php echo $invoice['barcode']; ?> успішно створене!</h3>
					<p>
						Деталі: <br>Тип: <?php echo $invoice['type']; ?><br>
            Відправник: <?php echo $invoice['sender']['name']; ?></br> з адресою відправлення <?php echo $invoice['sender']['addresses'][0]['address']['postcode']; ?> <?php echo $invoice['sender']['addresses'][0]['address']['detailedInfo']; ?> <br>
            Отримувач: <?php echo $invoice['recipient']['name']; ?></br>з адресою отримання <?php echo $invoice['recipient']['addresses'][0]['address']['postcode']; ?>  <?php echo $invoice['recipient']['addresses'][0]['address']['detailedInfo']; ?><br>

					</p>
				</div>
		</div>
  <?php }
  if( $failed && isset($message) && isset($_POST['sender_first_name'])) { ?>
    <div id="messagebox" class="messagebox_show error" data="110" style="height: 0px;padding:0px;">
      <div class="card text-white bg-danger">
        <h3>Помилка :</h3>
        <p> Введено не вірні дані. <?php echo $message; ?></p>


        <div class="clr"></div>
      </div>
    </div>
  <?php } ?>

    <div class="alink">
      <?php
      if ( !empty( $order_data["id"] ) ) {
        echo '<a class="btn" href="/wp-admin/post.php?post=' . $order_data["id"] . '&action=edit">Повернутись до замовлення</a>';
        echo '';
      }
      ?>
    <a href="edit.php?post_type=shop_order">Повернутись до замовлень</a>
   </div>


<div class="tablecontainer">
  <table class="form-table full-width-input i4x"  id="i4x">
    <tbody>
      <tr class="international-inner" t=user1>
        <th colspan=2>
          <h3 class="formblock_title">Відправник (заповнювати латиницею)</h2>
          <div id="errors"></div>
        </th>
      </tr>
      <tr class="nv-if-international" t=user1>
        <th colspan=2>
          <h3 class="formblock_title">Відправник</h2>
          <div id="errors"></div>
        </th>
      </tr>
      <tr  class="international-inner">
        <th scope="row">
            <label for="latinname">Повне Ім`я *</label>
        </th>
        <td>
            <input type="text" name="latinname0" id=latinname0  value="<?php echo esc_attr( get_option( 'nameslatin' ) ); ?>">
          </td>
      </tr>
      <tr t=user1>
        <th scope="row">
          <label for="sender_first_name">Імя відправника *</label>
        </th>
        <td>
          <input type="text" id="sender_first_name" name="sender_first_name" class="input sender_name"  value="<?php echo  get_option('names1'); ?>" />
        </td>
      </tr>
      <tr t=user1>
        <th scope="row">
          <label for="sender_first_name">Прізвище відправника *</label>
        </th>
        <td>
          <input type="text" id="sender_last_name" name="sender_last_name" class="input sender_name" value="<?php  echo get_option('names2'); ?>" />
        </td>
      </tr>
      <tr  class="international-inner">
        <th scope="row">
            <label for="rec_first_name">Населений пункт</label>
        </th>
        <td>
            <input type="text" name="city0" value="<?php echo esc_attr( get_option( 'citylatin' ) ); ?>" />
          </td>
      </tr>
      <tr  class="international-inner">
        <th scope="row">
            <label for="rec_first_name">вулиця</label>
        </th>
        <td>
            <input type="text" name="street" value="<?php  echo esc_attr( get_option( 'streetlatin' ) ); ?>">
          </td>
      </tr>

      <tr  class="international-inner">
        <th scope="row">
            <label for="rec_first_name">Номер будинку</label>
        </th>
        <td>
            <input type="text" name="housenumber" value="<?php echo esc_attr( get_option( 'numlatin' ) ); ?>">
          </td>
      </tr>
      <tr t=address1>
       <th scope="row">
         <label for="index1">Індекс відділення подачі відправлення</label>
       </th>
       <td>
          <input id="index1" type="text"  value="<?php  echo get_option('woocommerce_store_postcode'); ?>"  name="index1" />
        </td>
      </tr>
      <!--tr t=address1>
       <th scope="row">
         <label for="sender_namecity">Адреса</label>
       </th>
       <td>
          <input id="invoice_sender_address" type="text" value=""  name="invoice_sender_address" />
        </td>
      </tr-->

      <tr t=user1>
       <th scope="row">
         <label for="phone1">Телефон</label>
       </th>
       <td>
          <input id="phone1" type="text"  value="<?php  echo get_option('phone'); ?>"  name="phone1" />
        </td>
      </tr>
      <!--tr>
        <th scope="row">
          <label for="invoice_description">Опис відправлення</label>
        </th>
        <td class="pb7">
          <textarea  type="text" id="invoice_description" name="invoice_description" class="input" minlength="1" required><?php echo get_option('invoice_description'); ?></textarea>
          <p id="error_dec"></p>
        </td>
      </tr-->
    </tbody>
  </table>

  <table id=i5x class="form-table full-width-input i5x">
    <tbody>
      <tr class="nv-if-international">
        <th colspan=2>
          <h3 class="formblock_title">Одержувач</h2>
        </th>
      </tr>
      <tr class="international-inner">
        <th colspan=2>
          <h3 class="formblock_title">Одержувач(Заповнювати латиницею)</h2>
        </th>
      </tr>
      <tr  class="international-inner">
        <th scope="row">
            <label for="rec_first_name">Код країни одержувача *</label>
        </th>
        <td>
          <input type="text" name="country" list="country" />
            <datalist id=country name=countries>за
              <?php require 'countries.php'; ?>
            </select>

          </td>
      </tr>

      <tr  class="international-inner">
        <th scope="row">
            <label for="rec_first_name">Населений пункт</label>
        </th>
        <td>
            <input type="text" name="city">
          </td>
      </tr>


       <tr  class="international-inner">
        <th scope="row">
            <label for="rec_first_name">Детальна адреса</label>
        </th>
        <td>
            <textarea name="foreignStreetHouseApartment"> </textarea>
          </td>
      </tr>
      <tr>
         <!--td colspan="2">
           <div class="flex-space-around">
             <div  class="flexd50">
               <input type="radio" id="contactChoice1"
                name="contact" value="email">
              <label for="contactChoice1">Юридична особа</label>
            </div>
            <div class="flexd50">
              <input type="radio" id="contactChoice2"
               name="contact" value="phone">
              <label for="contactChoice2">Фізична особа</label>
            </div>
          </div>
          <div>
         </td-->
       </tr>
       <tr  class="international-inner">
        <th scope="row">
            <label for="latinname">Ім`я *</label>
        </th>
        <td>
            <input type="text" name="latinname" id=latinname  value="<?php echo $shipping_first_name." ".$shipping_last_name; ?>">
          </td>
      </tr>

       <tr class="nv-if-international">
          <th scope="row">
            <label for="rec_first_name">Прізвище одержувача</label>
          </th>
          <td>
            <input type="text" name="rec_first_name" id="rec_first_name" class="input recipient_name" value="<?php
            $path = MUP_PLUGIN_PATH . '/public/partials/morkvaup-plugin-invoices-page.php';
            if(file_exists($path)){
            echo $shipping_first_name;
            }
            ?>" />
          </td>
        </tr>
        <tr class="nv-if-international">
           <th scope="row">
             <label for="rec_last_name">Ім'я одержувача</label>
           </th>
           <td>
             <input type="text" name="rec_last_name" id="rec_last_name" class="input recipient_name" value="<?php
             if(file_exists($path)){
                echo  $shipping_last_name;
              }
              ?>" />
           </td>
         </tr>
         <?php  $postcode = '';
              if( isset( $order_data['billing']['postcode'] ) && ($order_data['billing']['postcode'] != '') ) {
                $postcode = $order_data['billing']['postcode'];
              }
              if( isset( $order_data['shipping']['postcode'] ) && ($order_data['shipping']['postcode'] != '')  ) {
                $postcode = $order_data['shipping']['postcode'];
              }

              ?>
          <tr class="nv-if-international">
             <th scope="row">
               <label for="index2">Індекс</label>
             </th>
             <td>
              <input id="index2" type="text" name="index2" class="input recipient_region" value="<?php
              if(file_exists($path)){
                echo $postcode;
              }
              ?>" />
           </tr>
           <tr t=user1>
            <th scope="row">
              <label for="phone2">Телефон</label>
            </th>
            <td>
              <?

              $shipping_phone.= ' ';

              if( startsWith($shipping_phone, '38')  ){
                $shipping_phone = substr($shipping_phone, 2);
                echo '<script>console.log("1");</script>';
              }
              if( startsWith($shipping_phone, '+38')  ){
                $shipping_phone = substr($shipping_phone, 3);
                echo '<script>console.log("11");</script>';
              }
              if( startsWith($shipping_phone, '8')  ){
                $shipping_phone = substr($shipping_phone, 1);
                echo '<script>console.log("2");</script>';
              }

              ?>
               <input id="phone2" type="text" value="<?php
               if(file_exists($path)){
                 echo ''.$shipping_phone;
               }
               ?>"  name="phone2" />
               <?php

               if(!file_exists($path)){
                 echo '</td></tr><tr><td colspan=2>В про-версії всі дані в форму вписуватимуться автоматично із замовлення, буде доступна міжнародна доставка, відслідковування замовлення, друкування наклейок для посилок, пріоритетна підтримка та багато іншого.';
               }
                ?>

             </td>
           </tr>
        </tbody>
      </table>
    </div>
    <div class="tablecontainer">
      <table id="i6x" class="form-table full-width-input i6x">
        <tbody>
      <tr>
       <th colspan=2>
         <h3 class="formblock_title">Параметри відправлення</h3>
     </th>
     </tr>
     
     <tr class="international-inner">
         <th scope="row">
           <label for="invoice_payer">Тип упаковки відправлення</label>
         </th>
         <td>
          <?php

              $value = esc_attr( get_option( 'senduptype' ) );
    $values= array('p','p','p','p','p');
    $sendtypes = array('LETTER', 'BANDEROLE', 'SMALL_BAG', 'PARCEL', 'DECLARED_VALUE');
    for( $i=0; $i<sizeof($values); $i++){
      if( $sendtypes[$i] == $value){
        $values[$i] = 'selected';
      }

    }
    echo '
          <select  class="" name="senduptype">
            
            <option '.$values[1].' value="BANDEROLE">BANDEROLE</option>
            <option '.$values[0].' value="LETTER">LETTER</option>
            <option '.$values[2].' value="SMALL_BAG">SMALL_BAG</option>
            <option '.$values[3].' value="PARCEL">PARCEL</option>
            <option '.$values[4].' value="DECLARED_VALUE">DECLARED_VALUE</option>
          </select>';
    echo '<p>';

          ?>
          
         </td>
       </tr>
      <tr>
         <th scope="row">
           <label for="invoice_payer">Платник</label>
         </th>
         <td>
           <select id="invoice_payer" name="paidByRecipient">
             <option value="true">Отримувач</option>
             <option value="false">Відправник</option>
           </select>
         </td>
       </tr>
       <?php
       $path = MUP_PLUGIN_PATH . 'public/partials/morkvaup-plugin-invoices-page.php';
   		if(file_exists($path)){      ?>
        <tr>
         <th scope="row">
           <label for="invoice_payer">Міжнародне відправлення</label>
         </th>
         <td>
          <input type=checkbox name=international id=international value=1 />
         </td>
       </tr>
       <?php }
       // $invoice_weight = get_option( 'invoice_weight' ); ?>
       <?php // if ( !empty( $invoice_weight ) ): ?>


          <tr>
             <th scope="row">
               <label class="light" for="invoice_cargo_mass" >Вага, кг</label>
             </th>
             <?php

             $invoice_addweight = intval (get_option( 'invoice_addweight' ));

             $Weight_object = null ;

             if(isset($order_data['meta_data'][1])){
               $Weight_object = ($order_data['meta_data'][1]);
             }

             $weight_value = null;

             if(isset($Weight_object)){
               $weight_value  =  $Weight_object -> get_data();
             }

             $order_weight = 0;

             if( isset( $weight_value['value']['data']['Weight'] )){
               $order_weight = $weight_value['value']['data']['Weight'];
             }
             else{
               $order_weight = $alternate_weight;
             }

             $all_weight = $order_weight + $invoice_addweight;

             ?>
             <td>
               <input type="text" name="invoice_cargo_mass" required id="invoice_cargo_mass" value="<?php echo $all_weight; ?>"  />
             </td>
           </tr>
           <tr>
            <td colspan=2>
              <p>
                <?php if($order_weight > 0){
                  echo '<span> Вага замовлення: '.$order_weight.'кг. </span>';
                }
                else{
                  echo '<span> Вагу замовлення не пораховано тому що під час оформленні в товарах не було вказано вагу. </span>';
                }

                if($invoice_addweight > 0){
                  echo '<span> Вага упаковки: '.$invoice_addweight.'кг. </span>';
                }
                else{
                  echo '<span> Вагу упаковки не пораховано тому що в настройках не було вказано вагу упаковки. </span>';
                }


                ?>
               </p>
               <p class="light">Якщо залишити порожнім, буде використано мінімальне значення 0.5. <?php echo $weighte; ?></p>
            </td>
           </tr>


           <!--tr>
              <td colspan=2 class="flexd" >
                <label class="inl" for="invoice_x">Ширина (см):
                <input  type="text" id="invoice_x" name="invoice_x" /></label>
                <label class="inl" for="invoice_z">Висота (см):
                <input  type="text" id="invoice_z" name="invoice_z" /></label>
                <label class="inl" for="invoice_y">Довжина (см):
                <input  type="text"  id="invoice_y" name="invoice_y" /></label>
              </td>
            </tr-->
        <?php // endif; ?>
         <tr>
           <th scope="row">
             <label class="light" for="invoice_volumei">Найбільша сторона, см</label>
           </th>
           <td>
             <input type="number" id="invoice_volumei" name="invoice_volume" value="20" required />
           </td>
         </tr>
          <tr>
             <th scope="row">
               <label for="invoice_priceid">Оголошена вартість</label>
             </th>
             <td>
               <input id="invoice_priceid" type="text" name="declaredPrice" value="<?php echo $order_data['total'];?>" required/>
             </td>
           </tr>

             <tr>
                <th scope="row">
                  <label for="invoice_placesi">Післяплата, грн</label>
                </th>
                <td style="padding-bottom: 0;">
                  <input type="text" id="invoice_placesi" name="invoice_places" value="<?php

                  $mess1 = '';

                  if(($order_data['payment_method'] == 'cod'))
                    {
                      echo $order_data['total'];
                      $mess1 = 'При замовленні було обрано оплату при отриманні,  в післяплату автоматично вписана сума замовлення.';
                     }
                     else{
                       echo '0';
                       $mess1 = 'При замовленні було не обрано оплату при отриманні, тому в післяплату автоматично вписано 0.';
                     }
                      ?>
                  " required/>

                </td>
              </tr>
              <tr><td colspan=2><p style="font-size:90%"><?php echo $mess1; ?></p></td></tr>
              <tr>
                <th scope="row">
                  <label for="invoice_description">Додаткова інформація</label>
                </th>
                <td class="pb7">
                  <?php

                  $path = MUP_PLUGIN_PATH . '/public/partials/morkvaup-plugin-invoices-page.php';
                  if(file_exists($path)){


                  $id = $order_data['id'];
                  $descriptionarea = get_option('invoice_description');
                  $descriptionarea = str_replace("[list_qa]", $list2, $descriptionarea);
                  $descriptionarea = str_replace("[list]", $list, $descriptionarea);
                  $descriptionarea = str_replace("[q]", $prod_quantity, $descriptionarea);
                  $descriptionarea = str_replace("[qa]", $prod_quantity2, $descriptionarea);
                  $descriptionarea = str_replace("[p]", $order_data['total'], $descriptionarea);
                  }
                  else{
                    $descriptionarea = '';
                  }

                  ?>
                  <textarea  type="text" id="invoice_description" name="description" class="input" minlength="1" required><?php echo $descriptionarea; ?></textarea>
                  <p id="error_dec"></p>
                </td>
              </tr>
              <tr>
                <th colspan=2  scope="row">У разі не вручення:</th>
              </tr>
              <tr>
                <td colspan=2>
                  <div class="">
                    <div  class="onfail ">
                      <input type="radio" id="dqq"
                       name="onFailReceiveType" value="RETURN">
                     <label for="dqq">повернути відправнику через 14 календарних днів.</label>
                   </div>
                   <div class="onfail ">
                     <input checked type="radio" id="dqq2"
                      name="onFailReceiveType" value="RETURN_AFTER_FREE_STORAGE">
                     <label for="dqq2">повернути відправлення після закінчення строку безкоштовного зберігання (5 робочих днів).</label>
                   </div>
                   <div class="onfail">
                     <input type="radio" id="dqq3"
                      name="onFailReceiveType" value="PROCESS_AS_REFUSAL">
                     <label for="dqq3">знищити відправлення</label>
                   </div>
                </td>
              </tr>
            </tbody>
          </table>
  <table class="form-table full-width-input i7x">
    <tbody>
        <tr>
          <td>
            <input type="hidden" name="sendtype" value="<?php  echo get_option('sendtype'); ?>" />
            <input type="hidden" name="sendwtype" value="<?php  echo get_option('sendwtype'); ?>" />
            <input type="submit" value="Створити" class="checkforminputs button button-primary" id="submit"/>
          </td>
        </tr>
      </tbody>
    </table>
</div>

<?php include 'card.php' ; ?>


  </div>
  * - обов'язковк для заповнення поле
</form>
<?php } ?>
<?php if(!$showpage){ echo '<h3>Для створення накладної перейдіть на <a href="edit.php?post_type=shop_order">сторінку замовлення</a></h3>';} ?>
</div>



<?php
if($showpage){
  $invoice = new MUP_Plugin_Invoice();
  $invoiceController = new MUP_Plugin_Invoice_Controller();

  $invoice->setPosts();

  $owner_address = get_option('warehouse');
  $owner_address = explode(" ", $owner_address);

  if ( empty($owner_address[0] or empty($owner_address[1])) ) {
    $owner_address[0] = "";
    $owner_address[1] = "";
    exit('Поле адреса віділення в налаштуваннях пусте, заповніть його, будь ласка');
  }

  $invoice->sender_street = $owner_address[0];
  //$invoice->sender_building = $owner_address[1];
  $invoice->order_price = $order_data["total"];

  $invoiceController->isEmpty();

  $bad_symbols = array( '+', '-', '(', ')', ' ' );

  $invoice->sender_phone = str_replace( $bad_symbols, '', $invoice->sender_phone );

  $invoice->cargo_weight = str_replace(".", ",", $invoice->cargo_weight);

  $invoice->register();
  $invoice->getCitySender();
  $invoice->getSender();
  $invoice->createSenderContact();
  $invoice->senderFindArea();
  $invoice->senderFindStreet();
  $invoice->createSenderAddress();
  $invoice->newFindRecipientArea();
  $invoice->findRecipientArea();
  $invoice->createRecipient();
  $invoice->howCosts();
  $invoice->order_id = $order_data["id"];
  $invoice->createInvoice();



  $order_id = $order_data["id"];

//add note

  if (isset($order_id)) {
    $order = wc_get_order( $order_id );
    $note = "Номер накладної: " . $_SESSION['invoice_id_for_order'];
    $order->add_order_note( $note );
    $order->save();

    unset( $_SESSION['invoice_id_for_order'] );
  }

}
?>
