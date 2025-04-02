<?php

interface Driver {
    public function getById($id, $resourceRoute);
    public function searchResources($searchQuery, $resourceRoute);
}

class GenericResource {
    public $data;

    public function __construct($data) {
        $this->data = $data;
    }
}

class JoomlaDriver implements Driver {
    private $cmsUrl;
    private $user;
    private $pass;
    
    public function __construct($cmsUrl, $user, $pass) {
        $this->cmsUrl = $cmsUrl;
        $this->user = $user;
        $this->pass = $pass;
    }
    
    public function getById($id, $resourceRoute) {
        $endpoint = $this->cmsUrl . $resourceRoute . "/" . $id;
        $jsonResponse = $this->httpGet($endpoint);
        $resource = $this->mapJsonToResource($jsonResponse);
        return $resource;
    }
    
    public function searchResources($searchQuery, $resourceRoute) {
        $endpoint = $this->cmsUrl . $resourceRoute . $searchQuery->getQueryString();
        $jsonResponse = $this->httpGet($endpoint);
        $resources = $this->mapJsonToResources($jsonResponse);
        return $resources;
    }
    
    private function httpGet($endpoint) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user . ":" . $this->pass);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    
    private function mapJsonToResource($jsonResponse) {
        // Convert JSON response to a GenericResource instance
        $data = json_decode($jsonResponse, true);  // Decode to associative array
        return new GenericResource($data);
    }
    
    private function mapJsonToResources($jsonResponse) {
        // Parse JSON array into a list of GenericResource objects
        $data = json_decode($jsonResponse, true);  // Decode to associative array
        $resources = [];
        foreach ($data as $item) {
            $resources[] = new GenericResource($item);
        }
        return $resources;
    }
}

class SearchQuery {
    private $queryString;

    public function __construct($queryString) {
        $this->queryString = $queryString;
    }

    public function getQueryString() {
        return $this->queryString;
    }
}

// Usage example
$driver = new JoomlaDriver('http://example.com/cms', 'user', 'password');
$resource = $driver->getById('123', '/content');
$searchQuery = new SearchQuery('?filter=example');
$resources = $driver->searchResources($searchQuery, '/content');
?>
