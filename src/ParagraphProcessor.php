<?php

namespace Drupal\neg_paragraphs;

use Drupal\negnet_utility\FieldUtilities;
use Drupal\neg_paragraphs\Videos\VideoEmbed;
use Drupal\Core\Url;

/**
 * Paragraph preprocess handlers.
 */
class ParagraphProcessor {

  /**
   * Preprocesses Row.
   */
  public function processParagraphRow(&$variables) {

    $variables['#attached']['library'][] = 'neg_paragraphs/reset';

    if (\Drupal::config('negnet_utility.grid')->get('grid') !== NULL) {
      $variables['#attached']['library'][] = \Drupal::config('negnet_utility.grid')->get('grid');
    }
    else {
      $variables['#attached']['library'][] = 'negnet_utility/grid';
    }

    if (!isset($GLOBALS['paragraph_row_count'])) {
      $GLOBALS['paragraph_row_count'] = 0;
    }

    $GLOBALS['paragraph_row_count']++;

    $variables['attributes']['class'][] = 'grid';

    if (isset($variables['elements']['field_columns'])) {
      $variables['columns'] = FieldUtilities::elementChildren($variables['elements']['field_columns']);
      $variables['col_count'] = count($variables['columns']);
      $variables['grid_type'] = FieldUtilities::numberToName($variables['col_count']);
      $variables['attributes']['class'][] = $variables['grid_type'];
    }

    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_margins')) {
      $variables['margins'] = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_margins);
      foreach ($variables['margins'] as &$margin) {
        $margin = 'margin_' . $margin['value'];
      }
    }

    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_horizontal_alignment')) {
      $variables['horizontal_alignment'] = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_horizontal_alignment)[0]['value'];
      $variables['attributes']['class'][] = $variables['horizontal_alignment'];
    }

    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_vertical_alignment')) {
      $variables['vertical_alignment'] = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_vertical_alignment)[0]['value'];
      $variables['attributes']['class'][] = $variables['vertical_alignment'];
    }

    $GLOBALS['paragraph_col_count'] = 0;
    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_columns')) {
      $cols = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_columns);
      $GLOBALS['paragraph_col_count'] = count($cols);
    }

    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_row_options')) {
      $options = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_row_options);
      foreach ($options as $option) {
        $option = $option['value'];
        switch ($option) {
          case 'padded':
            $variables['padded'] = 'padded';
            $variables['attributes']['class'][] = 'padded';
            break;

          case 'fullscreen':
            $this->setIsFullScreen($variables);
            break;

          case 'slider':
            if ($GLOBALS['paragraph_col_count'] > 1) {
              // Enable the slider if we have more than 1 item.
              $variables['#attached']['library'][] = 'neg_paragraphs/tinyslideParagraph';
              $variables['row_layout'] = 'layout_slider';
              $GLOBALS['paragraph_col_count'] = 1;
            }

            break;
        }
      }
    }

  }

  /**
   * Inform's the theme that the row should be set to full screen mode.
   */
  public function setIsFullScreen(&$variables) {
    // Only set fullscreen flag if this paragraph row is the first row.
    if ($variables['view_mode'] !== 'preview' && isset($GLOBALS['paragraph_row_count']) && $GLOBALS['paragraph_row_count'] == 1) {
      $GLOBALS['neg_fullscreen'] = TRUE;
    }
  }

  /**
   * Set's up default image settings for Neg_paragraphs.
   */
  public function setupImage(&$image) {

    if ($image['#theme'] !== 'responsive_image_formatter') {
      if (!isset($image['#type']) || $image['#type'] !== 'SVG') {
        $image['#responsive_image_style_id'] = 'rs_image';
        $image['#theme'] = 'responsive_image_formatter';
      }
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

    $variables['attributes']['class'][] = 'col';
    $variables['attributes']['class'][] = 'paragraph';
    $variables['attributes']['class'][] = 'paragraph-image';
    $variables['attributes']['class'][] = 'responsive_image';
    $variables['attributes']['class'][] = 'rs_image';

    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_link')) {
      $uri = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_link)[0]['uri'];
      try {
        $variables['link'] = Url::fromUri($uri)->toString();
      }
      catch (\Exception $e) {
      }
    }

    if (isset($variables['elements']['#paragraph']->field_image)) {
      $variables['image'] = FieldUtilities::elementChildren($variables['elements']['field_image']);
      foreach ($variables['image'] as &$image) {
        $this->setupImage($image);
      }
    }

    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_caption')) {
      $caption = $variables['elements']['#paragraph']->get('field_caption');
      if (!$caption->isEmpty()) {
        $values = $caption->getValue();
        $variables['captions'] = [];
        $hasText = FALSE;

        foreach ($values as $value) {
          $markup = check_markup($value['value'], $value['format']);
          $variables['captions'][] = $markup;

          if (strlen(trim(strip_tags($markup))) > 0) {
            $hasText = TRUE;
          }
        }

        if ($hasText) {
          $variables['attributes']['class'][] = 'has_caption';
        }
        else {
          unset($variables['captions']);
        }
      }
    }

    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_size')) {
      $variables['sizing'] = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_size)[0]['value'];
      $variables['attributes']['class'][] = $variables['sizing'];
    }
  }

  /**
   * Preprocesses Picture.
   */
  public function processPicture(&$variables) {

    if (!isset($GLOBALS['rs_image_count'])) {
      $GLOBALS['rs_image_count'] = 0;
    }

    $paragraph = $variables['elements']['#paragraph'];

    $variables['image'] = [];
    $variables['mobile'] = [];

    $variables['attributes']['class'][] = 'col';
    $variables['attributes']['class'][] = 'paragraph';
    $variables['attributes']['class'][] = 'paragraph-image';
    $variables['attributes']['class'][] = 'responsive_image';
    $variables['attributes']['class'][] = 'rs_image';

    $variables['lazyload'] = TRUE;
    if (isset($variables['elements']['#lazyload'])) {
      $variables['lazyload'] = $variables['elements']['#lazyload'];
    }

    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_link')) {
      $uri = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_link)[0]['uri'];
      try {
        $variables['link'] = Url::fromUri($uri)->toString();
      }
      catch (\Exception $e) {
      }
    }

    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_caption')) {
      $caption = $variables['elements']['#paragraph']->get('field_caption');
      if (!$caption->isEmpty()) {
        $variables['captions'] = [];
        $hasText = FALSE;

        foreach ($caption->getIterator() as $caption) {
          $value = $caption->getValue();
          $markup = $value['value'];
          $variables['captions'][] = $caption->view();

          if (strlen(trim(strip_tags($markup))) > 0) {
            $hasText = TRUE;
          }
        }

        if ($hasText) {
          $variables['attributes']['class'][] = 'has_caption';
        }
        else {
          unset($variables['captions']);
        }
      }
    }

    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_size')) {
      $variables['sizing'] = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_size)[0]['value'];
      $variables['attributes']['class'][] = $variables['sizing'];
    }

    // Image setup.
    $variables['lazyload'] = TRUE;
    if (isset($variables['elements']['#lazyload'])) {
      $variables['lazyload'] = $variables['elements']['#lazyload'];
    }

    if (!isset($GLOBALS['paragraph_row_count'])) {
      $GLOBALS['paragraph_row_count'] = 1;
    }

    if ($GLOBALS['paragraph_row_count'] === 1 || $GLOBALS['rs_image_count'] < 1) {
      $variables['lazyload'] = FALSE;
    }

    // Force auto sizing.
    if ($variables['lazyload'] === TRUE) {
      $variables['elements']['#sizes'] = 'auto';
    }

    $image = $variables['content']['field_image'][0];

    if (!$paragraph->field_image_style->isEmpty()) {
      $image['#responsive_image_style_id'] = $paragraph->field_image_style->first()->getValue()['value'];
    }

    if (isset($variables['elements']['#responsive_image_style_id'])) {
      $image['#responsive_image_style_id'] = $variables['elements']['#responsive_image_style_id'];
    }

    if (!isset($GLOBALS['rs_image_count']) || $GLOBALS['rs_image_count'] < 1) {
      // Don't allow lazyloading of first image.
      $variables['lazyload'] = FALSE;
    }

    $imageDomItem = ParagraphHelper::getImageAttributes($image);
    if ($imageDomItem === FALSE) {
      return;
    }

    $variables['image'] = [];
    $variables['attributes']['class'][] = $image['#responsive_image_style_id'];

    $variables['image']['srcset'] = ($imageDomItem->hasAttribute('data-srcset')) ? $imageDomItem->getAttribute('data-srcset') : $imageDomItem->getAttribute('srcset');
    $variables['image']['src'] = ($imageDomItem->hasAttribute('data-src')) ? $imageDomItem->getAttribute('data-src') : $imageDomItem->getAttribute('src');

    if (strlen($variables['image']['srcset']) === 0) {
      unset($variables['image']['srcset']);
    }

    if (isset($variables['elements']['#sizes'])) {
      $variables['image']['sizes'] = $variables['elements']['#sizes'];
    }
    else {
      $variables['image']['sizes'] = $imageDomItem->getAttribute('sizes');
    }

    if ($variables['lazyload'] === TRUE) {
      if ($variables['image']['sizes'] == '100vw') {
        $variables['image']['sizes'] = 'auto';
      }
    }
    else {
      if ($variables['image']['sizes'] == 'auto') {

        $variables['image']['sizes'] = '100vw';

        $row = $paragraph->getParentEntity();
        if ($row->hasField('field_columns')) {
          $columns = $row->get('field_columns');
          switch ($columns->count()) {
            case 2:
              $variables['image']['sizes'] = '50vw';
              break;

            case 3:
              $variables['image']['sizes'] = '33vw';
              break;

            case 4:
              $variables['image']['sizes'] = '25vw';
              break;

            case 5:
              $variables['image']['sizes'] = '20vw';
              break;

            case 6:
              $variables['image']['sizes'] = '17vw';
              break;
          }
        }
      }
    }

    $variables['image']['class'] = $imageDomItem->getAttribute('class');

    if ($variables['lazyload'] === FALSE) {
      $variables['image']['class'] = preg_replace('/lazyload\\s/um', ' ', $variables['image']['class']);
    }

    $variables['alt'] = $imageDomItem->getAttribute('alt');

    $variables['image']['width'] = ($imageDomItem->hasAttribute('width')) ? $imageDomItem->getAttribute('width') : $imageDomItem->getAttribute('data-width');
    $variables['image']['height'] = ($imageDomItem->hasAttribute('height')) ? $imageDomItem->getAttribute('height') : $imageDomItem->getAttribute('data-height');

    // Mobile Image Setup.
    if (!$paragraph->field_mobile_image->isEmpty() && isset($variables['content']['field_mobile_image'])) {
      $mobile = $variables['content']['field_mobile_image'][0];

      if (!$paragraph->field_mobile_image_style->isEmpty()) {
        $mobile['#responsive_image_style_id'] = $paragraph->field_mobile_image_style->first()->getValue()['value'];
      }

      if (isset($variables['elements']['#mobile_responsive_image_style_id'])) {
        $mobile['#responsive_image_style_id'] = $variables['elements']['#mobile_responsive_image_style_id'];
      }

      $mobileDomItem = ParagraphHelper::getImageAttributes($mobile);

      $variables['attributes']['class'][] = 'mobile--' . $mobile['#responsive_image_style_id'];

      $variables['mobile']['srcset'] = ($mobileDomItem->hasAttribute('data-srcset')) ? $mobileDomItem->getAttribute('data-srcset') : $mobileDomItem->getAttribute('srcset');
      $variables['mobile']['src'] = ($mobileDomItem->hasAttribute('data-src')) ? $mobileDomItem->getAttribute('data-src') : $mobileDomItem->getAttribute('src');

      if (strlen($variables['mobile']['srcset']) === 0) {
        unset($variables['mobile']['srcset']);
      }

      if (isset($variables['elements']['#mobileSizes'])) {
        $variables['mobile']['sizes'] = $variables['elements']['#mobileSizes'];
      }
      else {
        $variables['mobile']['sizes'] = $mobileDomItem->getAttribute('sizes');
      }

      $variables['mobile']['width'] = ($mobileDomItem->hasAttribute('width')) ? $mobileDomItem->getAttribute('width') : $mobileDomItem->getAttribute('data-width');
      $variables['mobile']['height'] = ($mobileDomItem->hasAttribute('height')) ? $mobileDomItem->getAttribute('height') : $mobileDomItem->getAttribute('data-height');

      if ($variables['lazyload'] === FALSE) {
        if ($variables['mobile']['sizes'] === 'auto') {
          $variables['mobile']['sizes'] = '100vw';
        }
      }

      $variables['attributes']['class'][] = 'mobile-alt';
    }

    // Alt.
    if (!$paragraph->field_alt->isEmpty()) {
      $alt = $paragraph->field_alt->first()->getValue()['value'];
      $variables['alt'] = $alt;
    }

    $GLOBALS['rs_image_count']++;
  }

  /**
   * Preprocesses Text Boxes.
   */
  public function processTextBox(&$variables) {

    $variables['attributes']['class'][] = 'col';
    $variables['attributes']['class'][] = 'paragraph';
    $variables['attributes']['class'][] = 'paragraph-textbox';

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
      $field_options = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_video_options);
      foreach ($field_options as $option) {
        $options[] = $option['value'];
      }
    }

    if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_video_url')) {

      if (FieldUtilities::fieldHasChildren($variables['elements']['#paragraph'], 'field_image')) {
        $variables['image'] = FieldUtilities::elementChildren($variables['elements']['field_image']);
        foreach ($variables['image'] as &$image) {
          $this->setupImage($image);
        }
      }

      $urlObject = FieldUtilities::elementChildren($variables['elements']['field_video_url']);
      $url = $urlObject[0]['#url']->toString();
      try {
        $video = new VideoEmbed($url, $options, $variables);
        $video->embed();
      }
      catch (\Exception $e) {
      }
    }

    if (isset($variables['elements']['field_caption'])) {
      $variables['captions'] = FieldUtilities::elementChildren($variables['elements']['field_caption']);
    }

    if (isset($variables['image'])) {
      $variables['attributes']['class'][] = 'has_poster';
    }

  }

}
