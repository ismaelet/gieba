<?php

class HttpResponse
{
    public $response;

    public function __construct($method, $url, $queryFields = [], $headers = false, $type = false)
    {
        $method = strtoupper($method);

        switch ($method) {
            case 'POST':
                $body = $type == 'json' ? json_encode($queryFields) : http_build_query($queryFields);

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url);
                if ($headers) {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                }
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $this->response = curl_exec($ch);

                curl_close($ch);
                break;

            case 'PUT':
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url);
                if ($headers) {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                }
                curl_setopt($ch, CURLOPT_PUT, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($queryFields));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $this->response = curl_exec($ch);

                curl_close($ch);
                break;

            case 'GET':
                if (! empty($queryFields)) {
                    $url = $url.'?'.http_build_query($queryFields);
                }

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $this->response = curl_exec($ch);
                break;

            case 'REDIRECT':
                if (! empty($queryFields)) {
                    $url = $url.'?'.http_build_query($queryFields);
                }
                legacy_redirect($url);
                break;
        }
    }

    public function __get($name)
    {
        switch ($name) {
            // case 'icon': return $this->icon = $this->icon();
        }
    }

    public function json()
    {
        return json_decode($this->response, true);
    }

    public function xml()
    {
        $xml = simplexml_load_string($this->response, 'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_encode($xml);

        return json_decode($json, true);
    }
}
