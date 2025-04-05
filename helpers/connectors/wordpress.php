<?php
defined('_JEXEC') or die;

use Joomla\CMS\Http\HttpFactory;

class WordpressConnector {
    private $api_url;
    private $username;
    private $password;
    private $http;

    public function __construct($api_url, $username, $password) {
        $this->api_url = rtrim($api_url, '/');
        $this->username = $username;
        $this->password = $password;
        $this->http = HttpFactory::getHttp();
    }

    public function getPosts($params = []) {
        $url = $this->api_url . '/wp/v2/posts';
        $response = $this->makeRequest($url, 'GET', $params);
        return json_decode($response->body, true);
    }

    public function createPost($data) {
        $url = $this->api_url . '/wp/v2/posts';
        $response = $this->makeRequest($url, 'POST', [], $data);
        return json_decode($response->body, true);
    }

    private function makeRequest($url, $method, $params = [], $data = null) {
        $options = [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password),
                'Content-Type' => 'application/json',
            ],
        ];

        if ($method === 'GET') {
            $response = $this->http->get($url . '?' . http_build_query($params), $options['headers']);
        } elseif ($method === 'POST') {
            $response = $this->http->post($url, json_encode($data), $options['headers']);
        } else {
            throw new Exception('Unsupported HTTP method: ' . $method);
        }

        if ($response->code !== 200 && $response->code !== 201) {
            throw new Exception('API request failed with code ' . $response->code . ': ' . $response->body);
        }

        return $response;
    }
}