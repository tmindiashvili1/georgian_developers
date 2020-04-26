<?php

namespace App\Gateways;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

abstract class BaseGateway implements IBaseGateway
{

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    protected  $baseUrl;

    /**
     * @var
     */
    protected $endPoint;

    /**
     * @var
     */
    protected $method;

    /**
     * @var
     */
    protected $params;

    /**
     * @var
     */
    protected $response;

    /**
     * @var
     */
    protected $token;

    /**
     * @var string
     */
    protected $requestOption = 'json';

    /**
     * @var array
     */
    protected $headers = [
        'Content-Type'  => 'application/json',
        'Accept'        => 'application/json',
    ];

    /**
     * MyPostGateway constructor.
     */
    public function __construct()
    {

        /**
         * Set gateway base credentials.
         */
        $this->setBaseCredentials();

        $this->response = [
            'status'    => true,
            'code'      => 200,
            'message'   => '',
            'data'      => ''
        ];
    }

    /**
     * @return void
     */
    abstract protected function setBaseCredentials();

    /**
     * Get response.
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Do request in Logistic.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function doRequest()
    {

        try {
            $client = new Client([
                'verify' => false
            ]);

            $request = $client->request($this->method, $this->baseUrl . $this->endPoint, [
                'headers' => $this->headers,
                $this->requestOption => $this->params
            ]);

            $this->response['data'] = json_decode($request->getBody()->getContents(),true);
            $this->response['headers'] = $request->getHeaders();

        } catch (ClientException $ex) {

            $request = json_decode($ex->getResponse()->getBody()->getContents(),true);

            Log::error('Error Client Gateway ', ['data' => $this->params, 'url' => $this->baseUrl . $this->endPoint , 'message' => $request]);
            $this->response['status'] = false;
            $this->response['code'] = $ex->getCode();
            $this->response['message'] = $request['message'];

        } catch (\Exception $ex) {

            Log::error('Error Gateway ', ['data' => $this->params, 'url' => $this->baseUrl . $this->endPoint , 'message' => $ex->getMessage()]);
            $this->response['status'] = false;
            $this->response['code'] = $ex->getCode();
            $this->response['message'] = $ex->getMessage();
        }

        return $this;
    }

}
