<?php

class CMSExtractor {
    private $cmsUrl;
    private $username;
    private $password;

    public function __construct($cmsUrl, $username, $password) {
        $this->cmsUrl = $cmsUrl;
        $this->username = $username;
        $this->password = $password;
    }
    public function extractSchema() {
        // Endpoint to retrieve the CMS schema
        $apiEndpoint = $this->cmsUrl . "/api/schema";
        $jsonResponse = $this->makeApiCall($apiEndpoint);
        $umlModel = $this->mapJsonToUML($jsonResponse);
        return $umlModel;
    }
    private function makeApiCall($endpoint) {
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); // Use Basic Authentication
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->username}:{$this->password}");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set a timeout for the request

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL error: $error_msg");
        }

        curl_close($ch);
        return $response;
    }

    private function mapJsonToUML($jsonResponse) {
        $data = json_decode($jsonResponse, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("JSON Decode Error: " . json_last_error_msg());
        }

        // Initialize UML model
        $umlModel = new UMLModel();

        // Example logic for parsing the schema and populating the UML model
        foreach ($data['classes'] as $classData) {
            $umlClass = new UMLClass();
            $umlClass->setName($classData['name']);
            
            // Add attributes to the UML class
            foreach ($classData['attributes'] as $attributeData) {
                $attribute = new UMLAttribute();
                $attribute->setName($attributeData['name']);
                $attribute->setType($attributeData['type']);
                $umlClass->addAttribute($attribute);
            }
            
            // Add relationships to the UML class
            foreach ($classData['relationships'] as $relationshipData) {
                $relationship = new UMLRelationship();
                $relationship->setTargetClass($relationshipData['target_class']);
                $relationship->setType($relationshipData['type']);
                $umlClass->addRelationship($relationship);
            }

            // Add the class to the UML model
            $umlModel->addClass($umlClass);
        }

        return $umlModel;
    }
}

// UMLModel class to represent the UML model
class UMLModel {
    private $classes = [];

    public function addClass(UMLClass $umlClass) {
        $this->classes[] = $umlClass;
    }

    public function getClasses() {
        return $this->classes;
    }
}

// UMLClass class to represent a UML class
class UMLClass {
    private $name;
    private $attributes = [];
    private $relationships = [];

    public function setName($name) {
        $this->name = $name;
    }

    public function addAttribute(UMLAttribute $attribute) {
        $this->attributes[] = $attribute;
    }

    public function addRelationship(UMLRelationship $relationship) {
        $this->relationships[] = $relationship;
    }

    public function getName() {
        return $this->name;
    }

    public function getAttributes() {
        return $this->attributes;
    }

    public function getRelationships() {
        return $this->relationships;
    }
}

// UMLAttribute class to represent a UML attribute
class UMLAttribute {
    private $name;
    private $type;

    public function setName($name) {
        $this->name = $name;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getName() {
        return $this->name;
    }

    public function getType() {
        return $this->type;
    }
}

// UMLRelationship class to represent a UML relationship
class UMLRelationship {
    private $targetClass;
    private $type;

    public function setTargetClass($targetClass) {
        $this->targetClass = $targetClass;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getTargetClass() {
        return $this->targetClass;
    }

    public function getType() {
        return $this->type;
    }
}

?>
