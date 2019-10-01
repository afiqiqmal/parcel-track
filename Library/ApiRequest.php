<?php
namespace Afiqiqmal\Library;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApiRequest
{
    protected $baseUrl = null;
    protected $requestBody = [];
    protected $param = [];
    protected $method = PARCEL_METHOD_GET;
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
        $this->method = PARCEL_METHOD_GET;
        return $this;
    }

    function postMethod()
    {
        $this->method = PARCEL_METHOD_POST;
        return $this;
    }

    function patchMethod()
    {
        $this->method = PARCEL_METHOD_PATCH;
        return $this;
    }

    function deleteMethod()
    {
        $this->method = PARCEL_METHOD_DELETE;
        return $this;
    }

    function setMethod($method = PARCEL_METHOD_GET)
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

        if ($this->requestUrl && substr($this->requestUrl, -1) == "/") {
            $this->requestUrl = ltrim($this->requestUrl, "/");
        }

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
                    $queryParam = $this->requestBody['parameter'] ?? null;
                    if ($queryParam) {
                        $param['query'] = $queryParam;
                        unset($this->requestBody['parameter']);
                    }
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

        } catch (\Exception $ex) {
            $data = [
                'error' => true,
                'message' => $ex->getMessage(),
                'reference' => $ex->getTraceAsString(),
                'status_code' => 400
            ];
        } catch (GuzzleException $ex) {
            $data = [
                'error' => true,
                'message' => $ex->getMessage(),
                'reference' => $ex->getTraceAsString(),
                'status_code' => 400
            ];
        }

        return array_merge($data, $this->appendJson);
    }
}
