<?php

class UkrposhtaApi
{
    protected $bearer;
    protected $tbearer;
    protected $token;
    protected $throwErrors = TRUE;
    /**
     * @var string $format Format of returned data - array
     */
    protected $format = 'array';
    /**
     * @var string $url Link to ukrposhtaApi
     */
    protected $url = 'https://www.ukrposhta.ua/';
    /**
     * @var string $apiVersion version for url
     */
    protected $apiVersion = '/0.0.1/';
    /**
     * @var string $responseTime waiting for response from server, sec.
     */
    protected $responseTime = '30';
    /**Default constructor
     * UkrposhtaApi constructor.
     * @param $bearer
     * @param bool $token
     * @param bool $throwErrors
     */
    function __construct($bearer, $token = FALSE, $tbearer, $throwErrors = FALSE)
    {
        $this->throwErrors = $throwErrors;
        return $this
            ->setBearer($bearer)
            ->setToken($token)
            ->setTbearer($tbearer);
    }
    /**Setter for bearer property
     * @param $bearer
     * @return $this
     */
    function setBearer($bearer)
    {
        $this->bearer = $bearer;
        return $this;
    }

    function setTbearer($tbearer)
    {
        $this->$tbearer = $tbearer;
        return $this;
    }
    /**Getter for bearer property
     * @return string
     */
    function getBearer()
    {
        return $this->bearer;
    }

    function getTbearer()
    {
        return $this->$tbearer;
    }
    /**Setter for token property
     * @param $token
     * @return $this
     */
    function setToken($token)
    {
        $this->token = $token;
        return $this;
    }
    /**Getter for token property
     * @return string
     */
    function getToken()
    {
        return $this->token;
    }
    /**Setter for format property
     * @param $format
     * @return $this
     */
    function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }
    /**Getter for format property
     * @return string
     */
    function getFormat()
    {
        return $this->format;
    }
    /**Setter for property responseTime
     * @param $responseTime
     * @return $this
     */
    function setResponseTime($responseTime)
    {
        if (is_numeric($responseTime)) $this->responseTime = $responseTime;
        return $this;
    }
    /**Getter for property responceTime
     * @return string
     */
    function getResponseTime()
    {
        return $this->responseTime;
    }
    /**Prepare data before return
     * @param $data
     * @return array|mixed
     */
    private function prepare($data)
    {
        //Returns array
        if ($this->format == 'array') {
            $result = is_array($data) ? $data : json_decode($data, 1);
            return $result;
        }
        // Returns json or raw data
        return $data;
    }
    /**Request function for model Adress
     * @param $model
     * @param string $method
     * @param null $params
     * @param string $add
     * @return array|mixed
     * @throws \Exception
     */
    private function request($model, $method = 'HTTPGET', $params = NULL, $add = '')
    {
        /* Get required URL*/
        $url = $this->url . 'ecom' . $this->apiVersion . $model . $add;

        echo '<script>console.log("'.$url.'");</script>';
        /* Convert data to neccessary format*/
        $post = json_encode($params);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $this->bearer));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //curl_setopt($ch, constant(CURLOPT_ . $method), 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->responseTime);
        if ($method != 'HTTPGET') curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($ch);
        curl_close($ch);
        //if (curl_errno($ch) && $this->throwErrors) throw new \Exception(curl_error($ch));
        //echo '<pre>';
        //print_r($this->prepare($result));
        //echo '</pre><hr>';
        echo '<script>console.log("model:'.$model.'");</script>';


        return $this->prepare($result);
    }
    /**Request for model client, smartbox, print with token
     * @param $model
     * @param string $method
     * @param null $params
     * @param string $add
     * @param bool $file
     * @return array|mixed
     * @throws \Exception
     */
    private function requestToken($model, $method = 'HTTPGET', $params = NULL, $add = '', $file = false)
    {
        /* Get required URL*/
        $url = $this->url . 'ecom' . $this->apiVersion . $model . $add . '?token=' . $this->token;
        /* Convert data to neccessary format*/
        $post = json_encode($params);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $this->bearer));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //curl_setopt($ch, constant(CURLOPT_ . $method), 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->responseTime);
        if ($method != 'HTTPGET') curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($ch);
        if (curl_errno($ch) && $this->throwErrors) throw new \Exception(curl_error($ch));
        curl_close($ch);
        if ($file) {
            return $result;

            $downloadPath = "upload/flower10.jpg";
            $file = fopen($downloadPath, "w+");
            fputs($file, $result);
            fclose($file);
            //print_r($result);
        } else {
            return $this->prepare($result);
            //print_r($result);
        }
    }
    /**Similar function to requestToken, but only for PUT request
     * @param $model
     * @param null $params
     * @param string $add
     * @return array|mixed
     * @throws \Exception
     */
    private function requestTokenPut($model, $params = NULL, $add = '')
    {
        /* Get required URL*/
        $url = $this->url . 'ecom' . $this->apiVersion . $model . $add . '?token=' . $this->token;
        /* Convert data to neccessary format*/
        $post = json_encode($params);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $this->bearer));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->responseTime);
        $result = curl_exec($ch);
        if (curl_errno($ch) && $this->throwErrors) throw new \Exception(curl_error($ch));
        curl_close($ch);
        return $this->prepare($result);
    }
    /**Request token for tracking barcode
     * @param $model
     * @param null $params
     * @param string $add
     * @return array|mixed
     * @throws \Exception
     */
    private function requestTracking($model, $params = NULL, $add = '')
    {
        /* Get required URL*/
        $url = $this->url . 'status-tracking' . $this->apiVersion . $model . $add;
        /* Convert data to neccessary format*/
        $post = json_encode($params);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $this->bearer, 'Tracking: Bearer ' . $this->tbearer ));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->responseTime);
        $result = curl_exec($ch);
        //print_r($result);
        if (curl_errno($ch) && $this->throwErrors) throw new \Exception(curl_error($ch));
        curl_close($ch);


        return $this->prepare($result);
    }

    public function RequestDelShipping($id)
      {
          $url = $this->url . 'ecom' .$this->apiVersion . 'shipments/'.$id.'?token='.$this->token;
          //echo $url;
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $this->bearer, 'Tracking: Bearer ' . $this->tbearer ));
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
          $result = curl_exec($ch);
          $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          curl_close($ch);

          return $result;
      }

      public function GetInfo($id){
        $url = 'https://www.ukrposhta.ua/ecom/0.0.1/shipments/'.$id.'?token='.$this->token;

        $authorization = "Authorization: Bearer ".$this->bearer;

        $cur = curl_init($url);
        curl_setopt( $cur, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cur, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
        $html = curl_exec( $cur );
        curl_close ( $cur );
        return  json_decode($html, true);

      }
      public function FunctionDecode($type, $value)
      {
        if($type=='type'){
          $arrayvalues = array('W2W', 'W2D');

          $arraydescriptions = array('Склад - Склад', 'Склад - Двері');

          for($i = 0; $i < sizeof($arrayvalues); $i++ ){

            if($value == $arrayvalues[$i]){
            return $arraydescriptions[$i];
            }
          }
        }
        if($type='fail'){
          $arrayvalues = array('RETURN', 'RETURN_AFTER_FREE_STORAGE', 'PROCESS_AS_REFUSAL');
          $arraydescriptions = array('Повернути через 14 днів', 'Повернути після безкоштовного зберігання', 'знищити відправлення');
          for($j = 0; $j < sizeof($arrayvalues); $j++ ){
            if($value == $arrayvalues[$j]){
            return $arraydescriptions[$j];
          }
          }
        }
      }


    /**Get created address by id
     * @param $id int
     * @return array|mixed
     */
    function modelAdressGet($id)
    {
        return $this->request('addresses', 'HTTPGET', NULL, '/' . $id);
    }
    /**Create address. For example:
     * @param $data array
     * @return array|mixed
     */
    function modelAdressPost($data)
    {
        return $this->request('addresses', 'POST', $data);
    }
    /**Creating new client
     * @param $data array
     * @return array|mixed
     */
    function modelClientsPost($data)
    {
        return $this->requestToken('clients', 'POST', $data);
    }
    /**Change data to existing client
     * @param $id int
     * @param $data array
     * @return array|mixed
     */
    function modelClientsPut($id, $data)
    {
        return $this->requestToken('clients', 'PUT', $data, '/' . $id);
    }
    /**Get created clients by external-id
     * @param $id int
     * @return array|mixed
     */
    function modelClientsGet($id)
    {
        return $this->requestToken('clients', 'HTTPGET', NULL, '/external-id/' . $id);
    }
    /**Creating shipment
     * @param $data array
     * @return array|mixed
     */
    function modelShipmentsPost($data)
    {
        return $this->requestToken('shipments', 'POST', $data);
    }
    /**Get file for print
     * @param $id string
     * @return array|mixed
     */
    function modelPrint($id)
    {



        return $this->requestToken('shipments', 'HTTPGET', NULL, '/' . $id . '/label', TRUE);


    }
    /**Request for use smartbox
     * @param $smartboxcode string
     * @param $clientuuid string
     * @return array|mixed
     */
    function modelSmartBoxPost($smartboxcode, $clientuuid)
    {
        return $this->requestToken('smart-boxes', 'POST', NULL, '/' . $smartboxcode . '/use-with-sender/' . $clientuuid);
    }
    /**Initialization smartbox shipment
     * @param $smartboxcode string
     * @return array|mixed
     */
    function modelSmartBoxGet($smartboxcode)
    {
        return $this->requestToken('smart-boxes', 'HTTPGET', NULL, '/' . $smartboxcode . '/shipments/next');
    }
    /**Creating smartbox shipment
     * @param $smartboxshipmentuuid string
     * @param $data array
     * @return array|mixed
     */
    function modelSmartBoxPut($smartboxshipmentuuid, $data)
    {
        return $this->requestTokenPut('shipments', $data, '/' . $smartboxshipmentuuid);
    }
    /**Getting last status of barcode
     * @param $barcode string
     * @return array|mixed
     */
    function modelStatuses($barcode)
    {
        //print_r($this->requestTracking('statuses/last', null, '?barcode=' . $barcode) );
        return $this->requestTracking('statuses/last', null, '?barcode=' . $barcode);
    }
}
