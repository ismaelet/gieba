<?php

class APIController
{
    public function processRequest($requestEndpoint, $requestMethod, $requestContent)
    {

        // setup request
        $requestClass = 'API\\'.ucfirst($requestEndpoint);
        $classMethod = strtolower($requestMethod);

        // get the response
        if (method_exists($requestClass, $classMethod)) {
            $response = $requestClass::$classMethod($requestContent);

            // submit the response
            if (! isset($response['header']) && isset($response['content']) && ! isset($response['content']['error'])) {
                switch ($requestMethod) {
                    case 'GET':
                        $response['header'] = 'HTTP/1.1 200 OK';
                        break;
                    case 'POST':
                        $response['header'] = 'HTTP/1.1 201 Created';
                        break;
                    case 'PUT':
                        $response['header'] = 'HTTP/1.1 201 Created';
                        break;
                    case 'PATCH':
                        $response['header'] = 'HTTP/1.1 200 OK';
                        break;
                    case 'DELETE':
                        $response['header'] = 'HTTP/1.1 200 OK';
                        break;
                }
            }
        } else {
            $response['header'] = 'HTTP/1.1 400 Bad Request';
            $response['content'] = $requestClass.'::'.$classMethod.'() method doesn\'t exist';
        }

        if (isset($response['header'])) {
            header($response['header']);
        }
        if (isset($response['content'])) {
            echo json_encode($response['content']);
        }
    }
}
