<?php
/**
 * Created by PhpStorm.
 * User: King
 * Date: 2017/9/26
 * Time: 17:09
 */

namespace king\payment\traits;


use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

trait HttpRequest
{
    protected function get($url, $query = [], $header = [])
    {

    }

    /**
     * post
     * @auth King
     * @param       $url
     * @param array $params
     * @param array ...$options
     *
     * @return mixed|string
     */
    protected function post($url, $params = [], ...$options)
    {
        $options = isset($options[0]) ? $options[0] : [];

        if (!is_array($params)) {
            $options['body'] = $params;
        } else {
            $options['form_params'] = $params;
        }

        return $this->request('post', $url, $options);
    }

    /**
     * request
     * @auth King
     * @param       $method
     * @param       $url
     * @param array $options
     *
     * @return mixed|string
     */
    protected function request($method, $url, $options = [])
    {
        $response = $this->getHttpClient($this->getBaseOptions())->request($method, $url, $options);

        return $this->handleResponse($response);
    }

    /**
     * getBaseOptions
     * @auth King
     * @return array
     */
    protected function getBaseOptions()
    {
        $options = [
            'base_uri' => method_exists($this, 'getBaseUri') ? $this->getBaseUri() : '',
            'timeout'  => property_exists($this, 'timeout') ? $this->timeout : 2.0,
        ];

        return $options;
    }

    /**
     * getHttpClient
     * @auth King
     *
     * @param array $config
     *
     * @return Client
     */
    protected function getHttpClient(array $config = [])
    {
        return new Client($config);
    }

    /**
     * handleResponse
     * @auth King
     *
     * @param ResponseInterface $response
     *
     * @return mixed|string
     */
    protected function handleResponse(ResponseInterface $response)
    {
        $contentType = $response->getHeaderLine('Content-Type');
        $content = $response->getBody()->getContents();

        if (false !== stripos($contentType, 'json') || stripos($contentType, 'javascript')) {
            return json_decode($content, true);
        } elseif (false !== stripos($contentType, 'xml')) {
            return json_decode(json_encode(simplexml_load_string($content)), true);
        }

        return $content;
    }
}
