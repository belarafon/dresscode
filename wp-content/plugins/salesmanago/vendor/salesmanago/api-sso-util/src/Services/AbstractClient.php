<?php

namespace SALESmanago\Services;

use Lib\GuzzleHttp\Client as GuzzleClient;
use Lib\GuzzleHttp\Exception\ConnectException;
use Lib\GuzzleHttp\Exception\ClientException;
use Lib\GuzzleHttp\Exception\GuzzleException;
use Lib\GuzzleHttp\Exception\ServerException;

use SALESmanago\Entity\Settings;
use SALESmanago\Exception\SalesManagoError;
use SALESmanago\Exception\SalesManagoException;


abstract class AbstractClient
{
    use ClientTrait;

    const METHOD_POST = 'POST';

    /** @var GuzzleClient $client */
    private $client;

    /**
     * @param Settings $settings
     * @param array $headers
     */
    final public function setClient(
        Settings $settings,
        $headers = array(
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json;charset=UTF-8'
        )
    ) {
        $this->client = new GuzzleClient([
            'base_uri' => $settings->getRequestEndpoint(),
            'verify'   => false,
            'timeout'  => 45.0,
            'defaults' => [
                'headers' => $headers,
            ]
        ]);
    }

    /**
     * @throws SalesManagoException
     * @param string $method
     * @param string $uri
     * @param array $data
     * @return array
     */
    final protected function request($method, $uri, $data)
    {
        try {
            $response = $this->client->request($method, $uri, array('json' => $data));
            $this->setStatusCode($response->getStatusCode());
            $rawResponse = $response->getBody()->getContents();

            return json_decode($rawResponse, true);
        } catch (ConnectException $e) {
            $error = $e->getHandlerContext();
            throw SalesManagoError::handleError($e->getMessage(), 0, true, $error['errno']);
        } catch (ClientException $e) {
            $error = $e->getResponse();
            throw SalesManagoError::handleError($e->getMessage(), $error->getStatusCode());
        } catch (ServerException $e) {
            $error = $e->getResponse();
            throw SalesManagoError::handleError($e->getMessage(), $error->getStatusCode());
        } catch (GuzzleException $e) {
            throw SalesManagoError::handleError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @throws SalesManagoException
     * @param string $method
     * @param string $uri
     * @param array $data
     * @return array
     */
    final protected function requestCustomizable($method, $uri, $data)
    {
        try {
            $response = $this->client->request($method, $uri, $data);
            $this->setStatusCode($response->getStatusCode());
            $rawResponse = $response->getBody()->getContents();

            return json_decode($rawResponse, true);
        } catch (ConnectException $e) {
            $error = $e->getHandlerContext();
            throw SalesManagoError::handleError($e->getMessage(), 0, true, $error['errno']);
        } catch (ClientException $e) {
            $error = $e->getResponse();
            throw SalesManagoError::handleError($e->getMessage(), $error->getStatusCode());
        } catch (ServerException $e) {
            $error = $e->getResponse();
            throw SalesManagoError::handleError($e->getMessage(), $error->getStatusCode());
        } catch (GuzzleException $e) {
            throw SalesManagoError::handleError($e->getMessage(), $e->getCode());
        }
    }
}
