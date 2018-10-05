<?php

namespace Drupal\neg_paragraphs;

use Drupal\negnet_utility\FieldUtilities;
use Drupal\neg_paragraphs\videos\videoEmbed;

/**
 * Paragraph preprocess handlers.
 */
class ParagraphProcessor {

  /**
   * Preprocesses Row.
   */
  public function processParagraphRow(&$variables) {

    $variables['#attached']['library'][] = 'neg_paragraphs/reset';
    $variables['#attached']['library'][] = 'negnet_utility/grid';

    if (!isset($GLOBALS['paragraph_row_count'])) {
      $GLOBALS['paragraph_row_count'] = 0;
    }

    $GLOBALS['paragraph_row_count']++;

    if (isset($variables['elements']['field_columns'])) {
      $variables['columns'] = FieldUtilities::elementChildren($variables['elements']['field_columns']);
      $variables['col_count'] = count($variables['columns']);
      $variables['grid_type'] = FieldUtilities::numberToName($variables['col_count']);
    }

    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_margins')) {
      $variables['margins'] = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_margins);
      foreach ($variables['margins'] as &$margin) {
        $margin = 'margin_' . $margin;
      }
    }

    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_horizontal_alignment')) {
      $variables['horizontal_alignment'] = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_horizontal_alignment)[0];
    }

    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_vertical_alignment')) {
      $variables['vertical_alignment'] = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_vertical_alignment)[0];
    }

    $GLOBALS['paragraph_col_count'] = 0;
    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_columns')) {
      $cols = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_columns);
      $GLOBALS['paragraph_col_count'] = count($cols);
    }

    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_row_options')) {
      $options = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_row_options);
      foreach ($options as $option) {
        switch ($option) {
          case 'padded':
            $variables['padded'] = 'padded';
            break;

          case 'fullscreen':
            $this->setIsFullScreen();
            break;

          case 'slider':
            $variables['#attached']['library'][] = 'neg_paragraphs/tinyslide';
            $variables['row_layout'] = 'layout_slider';
            $GLOBALS['paragraph_col_count'] = 1;
            break;
        }
      }
    }

  }

  /**
   * Inform's the theme that the row should be set to full screen mode.
   */
  public function setIsFullScreen() {
    // Only set fullscreen flag if this paragraph row is the first row.
    if (isset($GLOBALS['paragraph_row_count']) && $GLOBALS['paragraph_row_count'] == 1) {
      $GLOBALS['neg_fullscreen'] = TRUE;
    }
  }

  /**
   * Set's up default image settings for Neg_paragraphs.
   */
  public function setupImage(&$image) {

    if ($image['#theme'] !== 'responsive_image_formatter') {
      $image['#responsive_image_style_id'] = 'rs_image';
      $image['#theme'] = 'responsive_image_formatter';
    }

  }

  /**
   * Preprocesses Webforms.
   */
  public function processWebform(&$variables) {
    if (isset($variables['elements']['field_webform'])) {
      $variables['webform'] = FieldUtilities::elementChildren($variables['elements']['field_webform']);
    }
  }

  /**
   * Preprocesses Images.
   */
  public function processImage(&$variables) {
    if (isset($variables['elements']['#paragraph']->field_image)) {
      $variables['image'] = FieldUtilities::elementChildren($variables['elements']['field_image']);
      foreach ($variables['image'] as &$image) {
        $this->setupImage($image);
      }
    }
    if (isset($variables['elements']['field_caption'])) {
      $variables['captions'] = FieldUtilities::elementChildren($variables['elements']['field_caption']);
    }

    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_size')) {
      $variables['sizing'] = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_size)[0];
    }
  }

  /**
   * Preprocesses Text Boxes.
   */
  public function processTextBox(&$variables) {
    if (FieldUtilities::fieldHasChildren($variables['elements'], 'field_body')) {
      $variables['bodies'] = FieldUtilities::elementChildren($variables['elements']['field_body']);
      $variables['body_count'] = count($variables['bodies']);
      $variables['type'] = FieldUtilities::numberToName($variables['body_count']);
    }
  }

  /**
   * Preprocesses Videos.
   */
  public function processVideo(&$variables) {

    $options = [];
    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_video_options')) {
      $options = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_video_options);
    }

    if (isset($variables['elements']['field_video_url'])) {
      $urlObject = FieldUtilities::elementChildren($variables['elements']['field_video_url']);
      $url = $urlObject[0]['#url']->toString();
      $video = new videoEmbed($url, $options, $variables);
      $video->embed();
    }

    if (isset($variables['elements']['field_image'])) {
      $variables['image'] = FieldUtilities::elementChildren($variables['elements']['field_image']);
      foreach ($variables['image'] as &$image) {
        $this->setupImage($image);
      }
    }

    if (isset($variables['elements']['field_caption'])) {
      $variables['captions'] = FieldUtilities::elementChildren($variables['elements']['field_caption']);
    }

  }

}
