<?php
defined('_JEXEC') or die;

class ContentTransformer {
    public static function toCommonModel($source_cms, $source_data) {
        switch (strtolower($source_cms)) {
            case 'wordpress':
                return [
                    'source_cms' => 'wordpress',
                    'source_id' => $source_data['id'],
                    'content_type' => 'post',
                    'title' => $source_data['title']['rendered'],
                    'body' => $source_data['content']['rendered'],
                    'metadata' => json_encode($source_data['meta'] ?? []),
                    'created' => $source_data['date'],
                    'modified' => $source_data['modified'],
                ];
            case 'joomla':
                return [
                    'source_cms' => 'joomla',
                    'source_id' => $source_data['id'],
                    'content_type' => 'article',
                    'title' => $source_data['title'],
                    'body' => $source_data['introtext'] . $source_data['fulltext'],
                    'metadata' => json_encode($source_data['metadata'] ?? []),
                    'created' => $source_data['created'],
                    'modified' => $source_data['modified'],
                ];
            default:
                throw new Exception('Unsupported source CMS: ' . $source_cms);
        }
    }

    public static function toTargetModel($target_cms, $common_data) {
        switch (strtolower($target_cms)) {
            case 'wordpress':
                return [
                    'title' => $common_data['title'],
                    'content' => $common_data['body'],
                    'status' => 'publish',
                ];
            case 'joomla':
                return [
                    'title' => $common_data['title'],
                    'introtext' => substr($common_data['body'], 0, 255),
                    'fulltext' => $common_data['body'],
                    'state' => 1, // Published
                    'catid' => 2, // Default category ID (adjust as needed)
                ];
            default:
                throw new Exception('Unsupported target CMS: ' . $target_cms);
        }
    }
}