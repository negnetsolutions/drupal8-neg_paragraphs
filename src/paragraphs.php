<?php

namespace Drupal\neg_paragraphs;

use Drupal\negnet_utility\FieldUtilities;
use Drupal\neg_paragraphs\videos\videoEmbed;

class paragraphs {

  public function process_paragraph_row(&$variables) {

    $variables['#attached']['library'][] = 'neg_paragraphs/reset';
    $variables['#attached']['library'][] = 'negnet_utility/grid';

    if(isset($variables['elements']['field_columns'])){
      $variables['columns'] = FieldUtilities::elementChildren($variables['elements']['field_columns']);
      $variables['col_count'] = count($variables['columns']);
      $variables['grid_type'] = FieldUtilities::numberToName($variables['col_count']);
    }
  }

  public function setupImage(&$image) {

    if($image['#theme'] !== 'responsive_image_formatter') {
      $image['#responsive_image_style_id'] = 'rs_image';
      $image['#theme'] = 'responsive_image_formatter';
    }

  }

  public function process_webform(&$variables) {
    if(isset($variables['elements']['field_webform'])){
      $variables['webform'] = FieldUtilities::elementChildren($variables['elements']['field_webform']);
    }
  }

  public function process_image(&$variables) {
    if(isset($variables['elements']['#paragraph']->field_image)){
      $variables['image'] = FieldUtilities::elementChildren($variables['elements']['field_image']);
      foreach($variables['image'] as &$image) {
        $this->setupImage($image);
      }
    }
    if(isset($variables['elements']['field_caption'])){
      $variables['captions'] = FieldUtilities::elementChildren($variables['elements']['field_caption']);
    }
  }

  public function process_text_box(&$variables) {
    if(isset($variables['elements']['field_body'])){
      $variables['bodies'] = FieldUtilities::elementChildren($variables['elements']['field_body']);
      $variables['body_count'] = count($variables['bodies']);
      $variables['type'] = FieldUtilities::numberToName($variables['body_count']);
    }
  }

  public function process_video(&$variables) {

    $options = [];
    if(isset($variables['elements']['field_video_options'])){
      $options = FieldUtilities::fieldChildren($variables['elements']['#paragraph']->field_video_options);
    }

    if(isset($variables['elements']['field_video_url'])){
      $urlObject = FieldUtilities::elementChildren($variables['elements']['field_video_url']);
      $url = $urlObject[0]['#url']->toString();
      $video = new videoEmbed($url, $options, $variables);
      $video->embed();
    }

    if(isset($variables['elements']['field_image'])){
      $variables['image'] = FieldUtilities::elementChildren($variables['elements']['field_image']);
      foreach($variables['image'] as &$image) {
        $this->setupImage($image);
      }
    }

    if(isset($variables['elements']['field_caption'])){
      $variables['captions'] = FieldUtilities::elementChildren($variables['elements']['field_caption']);
    }

  }

}
