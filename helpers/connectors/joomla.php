<?php
defined('_JEXEC') or die;

use Joomla\CMS\Http\HttpFactory;

class JoomlaConnector {
    private $api_url;
    private $token;
    private $http;

    public function __construct($api_url, $token) {
        $this->api_url = rtrim($api_url, '/');
        $this->token = $token;
        $this->http = HttpFactory::getHttp();
    }

    public function getArticles($params = []) {
        $url = $this->api_url . '/api/index.php/v1/content/articles';
        $response = $this->makeRequest($url, 'GET', $params);
        return json_decode($response->body, true);
    }

    public function createArticle($data) {
        $url = $this->api_url . '/api/index.php/v1/content/articles';
        $response = $this->makeRequest($url, 'POST', [], $data);
        return json_decode($response->body, true);
    }

    private function makeRequest($url, $method, $params = [], $data = null) {
        $options = [
            'headers' => [
                'X-Joomla-Token' => $this->token,
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