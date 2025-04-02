<?php
// Instantiate the JoomlaExtractor with the Joomla API URL and credentials
$joomla = new JoomlaExtractor('https://example.com', 'username', 'password');

// Fetch by ID
try {
    $article = $joomla->getContentById(123);
    echo $article;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

// Search for articles
try {
    $articles = $joomla->searchContent('technology');
    foreach ($articles as $article) {
        echo $article . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>