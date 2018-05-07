<?php
namespace Afiqiqmal\Library;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApiRequest
{
    protected $baseUrl = null;
    protected $requestBody = [];
    protected $param = [];
    protected $method = "GET";
    protected $requestUrl = null;
    protected $header = null;
    protected $appendJson = null;
    protected $isRaw = false;
    protected $initOption = [];

    function baseUrl($url)
    {
        $this->baseUrl = $url;
        return $this;
    }

    function setParam($option = [])
    {
        $this->param = $option;
        return $this;
    }

    function setRequestBody($param = [])
    {
        $this->requestBody = $param;
        return $this;
    }

    function getMethod()
    {
        $this->method = "GET";
        return $this;
    }

    function postMethod()
    {
        $this->method = "POST";
        return $this;
    }

    function patchMethod()
    {
        $this->method = "PATCH";
        return $this;
    }

    function deleteMethod()
    {
        $this->method = "DELETE";
        return $this;
    }

    function setMethod($method = "GET")
    {
        $this->method = $method;
        return $this;
    }

    function setHeader($header = null)
    {
        $this->header = $header;
        return $this;
    }

    function requestUrl($requestUrl)
    {
        $this->requestUrl = $requestUrl;
        return $this;
    }

    function appendToResult(array $appendJson)
    {
        $this->appendJson = $appendJson;
        return $this;
    }

    function getRaw()
    {
        $this->isRaw = true;
        return $this;
    }

    function fetch($requestUrl = null, $requestBody = [], $method = null, $header = null)
    {
        if ($requestUrl) {
            $this->requestUrl = $requestUrl;
        }

        if (count($requestBody) > 0) {
            $this->requestBody = $requestBody;
        }

        if ($method) {
            $this->method = $method;
        }

        if ($header) {
            $this->header = $header;
        }

        if (!$this->baseUrl) {
            throw new \RuntimeException('Base URL need to be set!!');
        }

//        if (substr($this->baseUrl, -1) != '/') {
//            $this->baseUrl = $this->baseUrl."/";
//        }

        if ($this->requestUrl && substr($this->requestUrl, -1) == "/") {
            $this->requestUrl = ltrim($this->requestUrl, "/");
        }
//
//        if (!$this->requestUrl) {
//            $this->baseUrl = rtrim($this->baseUrl, '/');
//        }

        $url = $this->baseUrl . $this->requestUrl;
        try {
            $client = new Client();
            switch ($this->method) {
                case PARCEL_METHOD_GET:
                    $param = [
                        'query' => $this->requestBody,
                        'headers' => $this->header
                    ];
                    break;
                case PARCEL_METHOD_POST:
                    $param = [
                        'form_params' => $this->requestBody,
                        'headers' => $this->header
                    ];
                    break;
                case PARCEL_METHOD_PATCH:
                    $param = [];
                    break;
                case PARCEL_METHOD_DELETE:
                    $param = [];
                    break;
                default:
                    $param = null;
                    break;
            }

            if (!$this->header) {
                unset($param['headers']);
            }
            $param = array_merge($param, $this->param);
            $response = $client->request($this->method, $url, $param);

            $data = [
                'error' => false,
                'body' =>  $this->isRaw ? $response->getBody()->getContents() : json_decode($response->getBody(), true),
                'header' =>  $response->getHeaders(),
                'status_code' => $response->getStatusCode(),
            ];

            return array_merge($data, $this->appendJson);

        } catch (\Exception $ex) {
            return [
                'error' => true,
                'message' => $ex->getMessage(),
                'reference' => $ex->getTraceAsString()
            ];
        } catch (GuzzleException $ex) {
            return [
                'error' => true,
                'message' => $ex->getMessage(),
                'reference' => $ex->getTraceAsString()
            ];
        }
    }
}
