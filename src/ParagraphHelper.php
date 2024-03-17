<?php

namespace Drupal\neg_paragraphs;

use Drupal\Core\Render\RenderContext;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Helper function for neg_paragraphs.
 */
class ParagraphHelper {

  /**
   * Checks to see if a row has a type of col.
   */
  public static function hasColOfType(&$cols, $type) {
    foreach ($cols as $col) {
      $p = Paragraph::load($col['target_id']);
      if ($p->bundle() == $type) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Gets all of the rows on Type.
   */
  public static function fetchRowsOfType(array $types, $node) {
    $rows = [];

    // Make sure we have a node with the right fields.
    if ($node && $node->hasField('field_layout_rows')) {
      $paragraph_row_field_values = $node->get('field_layout_rows')->getValue();

      // paragraph_row.
      foreach ($paragraph_row_field_values as $paragraph_row_field_value) {
        $paragraph_row_entity = Paragraph::load($paragraph_row_field_value['target_id']);

        // Check to see if this entity is one of
        // our text-based paragraphs.
        if ($paragraph_row_entity && in_array($paragraph_row_entity->getType(), $types)) {
          $rows[] = $paragraph_row_entity;
        }

      }
    }

    return $rows;
  }

  /**
   * Gets all the columns of Type.
   */
  public static function fetchColumnsOfType(array $types, $node) {
    $columns = [];

    // Make sure we have a node with the right fields.
    if ($node && $node->hasField('field_layout_rows')) {
      $paragraph_row_field_values = $node->get('field_layout_rows')->getValue();

      // paragraph_row.
      foreach ($paragraph_row_field_values as $paragraph_row_field_value) {
        $paragraph_row_entity = Paragraph::load($paragraph_row_field_value['target_id']);

        if ($paragraph_row_entity && $paragraph_row_entity->getType() == 'paragraph_row') {

          // Get paragraph row's field columns.
          if ($paragraph_row_entity->hasField('field_columns')) {
            $field_columns = $paragraph_row_entity->get('field_columns')->getValue();
            foreach ($field_columns as $field_column) {
              $paragraph_entity = Paragraph::load($field_column['target_id']);
              // Check to see if this entity is one of
              // our text-based paragraphs.
              if ($paragraph_entity && in_array($paragraph_entity->getType(), $types)) {
                $columns[] = $paragraph_entity;
              }
            }
          }
        }
      }
    }

    return $columns;
  }

  /**
   * Fetches all fields of type.
   */
  public static function fetchFieldsOfType(array $types, $object) {
    $fields = [];

    // Iterate over text fields, creating the text body.
    foreach ($types as $field_type) {
      if ($object->hasField($field_type)) {
        $fields[] = $object->get($field_type);
      }
    }

    return $fields;
  }

  /**
   * Formats text using text filters.
   */
  public static function formatText($text_content, $summary_length = 158) {
    $summary_formatter = 'plain_text';
    $text_content = trim($text_content);
    $text_content = strip_tags($text_content);
    $text_content = html_entity_decode($text_content);
    $text_content = htmlspecialchars($text_content);
    $text_content = text_summary($text_content, $summary_formatter, $summary_length);

    return $text_content;
  }

  /**
   * Parses an image for attributes.
   */
  public static function getImageAttributes($image) {
    $b = self::renderImage($image);

    if (strlen($b) === 0) {
      return FALSE;
    }

    $dom = new \DOMDocument();
    $dom->loadHTML($b);
    foreach ($dom->getElementsByTagName('img') as $node) {
      return $node;
    }
    return FALSE;
  }

  /**
   * Renders and image.
   */
  protected static function renderImage($image) {
    $b = NULL;

    $context = new RenderContext();
    \Drupal::service('renderer')->executeInRenderContext($context, function () use (&$b, &$image) {
      $b = \Drupal::service('renderer')
        ->render($image, FALSE);
    });

    return (string) $b;
  }

}
