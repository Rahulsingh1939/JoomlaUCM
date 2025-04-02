<?php
$cmsExtractor = new CMSExtractor("https://example.com", "username", "password");

try {
    $umlModel = $cmsExtractor->extractSchema();

    foreach ($umlModel->getClasses() as $umlClass) {
        echo "Class: " . $umlClass->getName() . "\n";
        
        foreach ($umlClass->getAttributes() as $attribute) {
            echo "  Attribute: " . $attribute->getName() . " (Type: " . $attribute->getType() . ")\n";
        }

        foreach ($umlClass->getRelationships() as $relationship) {
            echo "  Relationship: " . $relationship->getType() . " to " . $relationship->getTargetClass() . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>