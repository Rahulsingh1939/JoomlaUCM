<?php

interface CMSDriverInterface {
    public function getContentById($id);
    public function searchContent($query);
}

class JoomlaExtractor implements CMSDriverInterface {
    protected $baseUrl;
    protected $username;
    protected $password;

    // Constructor for initializing the base URL, username, and password
    public function __construct($baseUrl, $username, $password) {
        $this->baseUrl = rtrim($baseUrl, '/'); 
        $this->username = $username;
        $this->password = $password;
    }

    // Fetch a single content article by its ID
    public function getContentById($id) {
        $url = $this->baseUrl . '/api/articles/' . $id;
        $json = $this->makeRequest($url);
        if ($json === null) {
            throw new Exception("Failed to fetch article with ID: $id");
        }
        return $this->mapToArticle($json);
    }

    // Search for content by a query string
    public function searchContent($query) {
        $url = $this->baseUrl . '/api/articles?search=' . urlencode($query);
        $json = $this->makeRequest($url);
        if ($json === null) {
            return [];
        }

        $articles = [];
        foreach ($json as $item) {
            $articles[] = $this->mapToArticle($item);
        }
        return $articles;
    }

    // A general method to handle API requests using cURL
    protected function makeRequest($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->username}:{$this->password}");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); // Use Basic Authentication

        
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);

        // Check for errors in the cURL request
        if(curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL error: $error_msg");
        }

        curl_close($ch);

        // Decode the JSON response
        return $this->decodeJsonResponse($response);
    }

    // Decodes JSON and handles potential errors
    protected function decodeJsonResponse($response) {
        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("JSON Decode Error: " . json_last_error_msg());
        }
        return $decoded;
    }

    // Map API response to a simplified Article objectD
    protected function mapToArticle($data) {
        if (!isset($data['id'], $data['title'], $data['updated'], 
        $data['content'], $data['author'])) {
            throw new Exception("Invalid article data received from API");
        }

        return new Article(
            $data['id'],
            $data['title'],
            $data['updated'],
            $data['content'],
            $data['author']
        );
    }
}

// Article Class to represent an Article object
class Article {
    public $id;
    public $title;
    public $updated;
    public $content;
    public $author;

    public function __construct($id, $title, $updated, $content, $author) {
        $this->id = $id;
        $this->title = $title;
        $this->updated = $updated;
        $this->content = $content;
        $this->author = $author;
    }

    public function __toString() {
        return "Article ID: {$this->id}, Title: {$this->title},
         Author: {$this->author}, Last Updated: {$this->updated}";
    }
}

?>