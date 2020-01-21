<?php
/**
 * Registering callbacks for settings admin page
 *
 * @link        http://morkva.co.ua/
 * @since       1.0.0
 *
 * @package     morkvaup-plugin
 * @subpackage  morkvaup-plugin/includes
 */
/**
 * Registering callbacks for settings admin page
 *
 *
 * @package    morkvaup-plugin
 * @subpackage morkvaup-plugin/includes
 * @author     MORKVA <hello@morkva.co.ua>
 */
 class MUP_Plugin_Callbacks {
 	public function adminDashboard()
	{
		return require_once( "$this->plugin_path/templates/admin.php" );
	}

	public function adminInvoice()
	{
		return require_once( "$this->plugin_path/templates/invoice.php" );
	}

	public function adminSettings()
	{
		return require_once( "$this->plugin_path/templates/taxonomy.php" );
	}

	public function morkvaupOptionsGroup( $input )
	{
		return $input;
	}

	public function morkvaupAdminSection()
	{
		echo 'Введіть свій API ключ для початку щоб плагін міг працювати.';
	}



  public function morkvaupAuthBearer()
	{
		$value = esc_attr( get_option( 'production_bearer_ecom' ) );
		echo '<input type="text" class="regular-text" name="production_bearer_ecom" value="' . $value . '" placeholder="API ключ">';
		echo '';
	}
  public function morkvaupProdBearer()
  {
    $value = esc_attr( get_option( 'production_bearer_status_tracking' ) );
    echo '<input type="text" class="regular-text" name="production_bearer_status_tracking" value="' . $value . '" placeholder="API ключ">';
    echo '';
  }
  public function morkvaupCpToken()
  {
    $value = esc_attr( get_option( 'production_cp_token' ) );
    echo '<input type="text" class="regular-text" name="production_cp_token" value="' . $value . '" placeholder="API ключ">';
    echo '';
  }
  public function morkvaupprinttype()
  {
    $value = esc_attr( get_option( 'proptype' ) );
    $values= array('p','p','p');
    for( $i=0; $i<sizeof($values); $i++){
      if( $i == $value){
        $values[$i] = 'selected';
      }

    }
    echo '
          <select  class="regular-text" name="proptype">
            <option '.$values[0].' value="0">100*100 мм</option>
            <option '.$values[1].' value="1">100*100 мм для друку на форматі А4</option>
            <option '.$values[2].' value="2">100*100 мм для друку на форматі А5</option>
          </select>';
    echo '<p></p>';
  }

  public function morkvaupsenduptype()
  {
    $value = esc_attr( get_option( 'senduptype' ) );
    $values= array('p','p','p','p','p');
    $sendtypes = array('LETTER', 'BANDEROLE', 'SMALL_BAG', 'PARCEL', 'DECLARED_VALUE');
    for( $i=0; $i<sizeof($values); $i++){
      if( $sendtypes[$i] == $value){
        $values[$i] = 'selected';
      }

    }
    echo '
          <select  class="regular-text" name="senduptype">
            
            <option '.$values[1].' value="BANDEROLE">BANDEROLE</option>
            <option '.$values[0].' value="LETTER">LETTER</option>
            <option '.$values[2].' value="SMALL_BAG">SMALL_BAG</option>
            <option '.$values[3].' value="PARCEL">PARCEL</option>
            <option '.$values[4].' value="DECLARED_VALUE">DECLARED_VALUE</option>
          </select>';
    echo '<p>Використовується при створенні міжнародного відправлення</p>';
  }
    public function morkvaupsendtype()
  {
    $value = esc_attr( get_option( 'sendtype' ) );
    $values= array('p','p');
    $sendtypes = array('EXPRESS', 'STANDARD');
    for( $i=0; $i<sizeof($values); $i++){
      if( $sendtypes[$i] == $value){
        $values[$i] = 'selected';
      }

    }
    echo '
          <select  class="regular-text" name="sendtype">
            <option '.$values[0].' value="EXPRESS">EXPRESS</option>
            <option '.$values[1].' value="STANDARD">STANDARD</option>
          </select>';
    echo '<p></p>';
  }

  public function morkvaupsendwtype()
  {
    $value = esc_attr( get_option( 'sendwtype' ) );
    $values= array('p','p');
    $sendtypes = array('W2W', 'W2D');
    for( $i=0; $i<sizeof($values); $i++){
      if( $sendtypes[$i] == $value){
        $values[$i] = 'selected';
      }

    }
    echo '
          <select  class="regular-text" name="sendwtype">
            <option '.$values[0].' value="W2W">Склад - Склад</option>
            <option '.$values[1].' value="W2D">Склад - Двері</option>
          </select>';
    echo '<p></p>';
  }







	public function morkvaupActivate() {
		$activate = get_option( 'activate_plugin' );
		$checked = $activate;
		$current = 1;
		$echo = false;
		echo '<input type="checkbox" class="regular-text" name="activate_plugin" value="1" list="cities" ' . checked($checked, $current, $echo) . ' />';
	}

	public function morkvaupPhone() {
		$phone = esc_attr( get_option( 'phone' ) );
		echo '<input type="text" class="regular-text" name="phone" value="' . $phone . '" placeholder="0901234567">';
		echo '<p>Підказка: основний формат 0987654321 (без +38)</p>';
	}

	public function morkvaupNames() {
		$names = esc_attr( get_option( 'names1' ) );
		echo '<input type="text" class="regular-text" name="names1" value="' . $names . '" placeholder="Невинний">
    <p>Використовується при створенні відправлення</p>';
	}



  public function morkvaupCityLatin() {
    $names = esc_attr( get_option( 'citylatin' ) );
    echo '<input type="text" class="regular-text" name="citylatin" value="' . $names . '" placeholder="Kyiv">
    <p>Використовується при створенні міжнародного відправлення</p>';
  }



  public function morkvaupStreetLatin() {
    $names = esc_attr( get_option( 'streetlatin' ) );
    echo '<input type="text" class="regular-text" name="streetlatin" value="' . $names . '" placeholder="street">
    <p>Використовується при створенні міжнародного відправлення</p>';
  }


  public function morkvaupNumLatin() {
    $names = esc_attr( get_option( 'numlatin' ) );
    echo '<input type="text" class="regular-text" name="numlatin" value="' . $names . '" placeholder="Kyiv">
    <p>Використовується при створенні міжнародного відправлення</p>';
  }
  public function morkvaupNamesLatin() {
    $names = esc_attr( get_option( 'nameslatin' ) );
    echo '<input type="text" class="regular-text" name="nameslatin" value="' . $names . '" placeholder="Ivanov Ivan Ivanovich">
    <p>Використовується при створенні міжнародного відправлення</p>';
  }

  public function morkvaupNames2() {
    $names = esc_attr( get_option( 'names2' ) );
    echo '<input type="text" class="regular-text" name="names2" value="' . $names . '" placeholder="Іван">
    <p>Використовується при створенні відправлення</p>';
  }
  public function morkvaupNames3() {
    $names = esc_attr( get_option( 'names3' ) );
    echo '<input type="text" class="regular-text" name="names3" value="' . $names . '" placeholder="Іванович">
    <p>Використовується при створенні відправлення</p>';
  }


	public function morkvaupFlat() {
		$flat = esc_attr( get_option( 'flat' ) );
		echo '<input type="text" class="regular-text" name="flat" value="' . $flat . '" placeholder="номер">';
	}

	public function morkvaupWarehouseAddress()
	{
		$warehouse = esc_attr( get_option( 'woocommerce_store_postcode' ) );


		echo '<input type="text" class="regular-text" name="warehouse" value="' . $warehouse . '" placeholder="Франка 14" readonly>';
		echo '<p>Налаштування цього поля беруться із <a href="admin.php?page=wc-settings&tab=general">налаштувань Woocommerce </a></p>';
	}

	public function morkvaupInvoiceDescription()
	{
		$invoice_description = get_option('invoice_description');



    echo '<textarea  id=td45 name="invoice_description" rows="5" cols="54">' . $invoice_description . '</textarea>
<span id=sp1 class=shortspan>+ Вартість</span>
<select class=shortspan id=shortselect>
  <option value="0" disabled selected style="display:none"> + Перелік</option>
  <option value="list" > + Перелік товарів (з кількістю)</option>
  <option value="list_qa"> + Перелік товарів ( з артикулами та кількістю)</option>
</select>
<select class=shortspan id=shortselect2>
  <option value="0" disabled selected style="display:none"> + Кількість</option>
  <option value="qa"> + Кількість позицій</option>
  <option value="q"> + кількість товарів</option>
</select>
<p>значення шорткодів, при натисненні кнопок додаються в кінець текстового поля</p>
';

    $path = MUP_PLUGIN_PATH . 'public/partials/morkvaup-plugin-invoices-page.php';
		if(!file_exists($path)){
		 echo '<p>Функція опису за промовчанням працює у PRO версії. у Free потрібно буде заповнювати опис кожного відправлення врчну.</p>';
		}


	}

	public function morkvaupInvoiceWeight()
	{
		$activate = get_option( 'invoice_weight' );
		$checked = $activate;
		$current = 1;
		$echo = false;
		echo '<input type="checkbox" class="regular-text" name="invoice_weight" value="1" ' . checked($checked, $current, $echo) . ' />';
	}



	public function morkvaupEmailTemplate()
	{
		$content = get_option( 'morkvaup_email_template' );
		$editor_id = 'morkvaup_email_editor_id';

		wp_editor( $content, $editor_id, array( 'textarea_name' => 'morkvaup_email_template' ) );
	}
	public function morkvaupEmailSubject()
	{
		$subject = get_option( 'morkvaup_email_subject' );

		echo '<input type="text" name="morkvaup_email_subject" class="regular-text" value="' . $subject . '" />';
	}



 }
