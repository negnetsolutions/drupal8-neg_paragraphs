<?php

namespace Drupal\neg_paragraphs;

use Drupal\negnet_utility\NegnetUtilities;
use Drupal\neg_paragraphs\videos\videoEmbed;

class paragraphs {

  public function process_paragraph_row(&$variables) {

    $variables['#attached']['library'][] = 'neg_paragraphs/reset';
    $variables['#attached']['library'][] = 'negnet_utility/grid';

    if(isset($variables['elements']['field_columns'])){
      $variables['columns'] = NegnetUtilities::elementChildren($variables['elements']['field_columns']);
      $variables['col_count'] = count($variables['columns']);
      $variables['grid_type'] = NegnetUtilities::numberToName($variables['col_count']);
    }
  }

  public function setupImage(&$image) {

    if($image['#theme'] !== 'responsive_image_formatter') {
      $image['#responsive_image_style_id'] = 'rs_image';
      $image['#theme'] = 'responsive_image_formatter';
    }

  }

  public function process_image(&$variables) {
    if(isset($variables['elements']['#paragraph']->field_image)){
      $variables['image'] = NegnetUtilities::elementChildren($variables['elements']['field_image']);
      foreach($variables['image'] as &$image) {
        $this->setupImage($image);
      }
    }
    if(isset($variables['elements']['field_caption'])){
      $variables['captions'] = NegnetUtilities::elementChildren($variables['elements']['field_caption']);
    }
  }

  public function process_text_box(&$variables) {
    if(isset($variables['elements']['field_body'])){
      $variables['bodies'] = NegnetUtilities::elementChildren($variables['elements']['field_body']);
      $variables['body_count'] = count($variables['bodies']);
      $variables['type'] = NegnetUtilities::numberToName($variables['body_count']);
    }
  }

  public function process_video(&$variables) {

    $options = [];
    if(isset($variables['elements']['field_video_options'])){
      $options = NegnetUtilities::fieldChildren($variables['elements']['#paragraph']->field_video_options);
    }

    if(isset($variables['elements']['field_video_url'])){
      $urlObject = NegnetUtilities::elementChildren($variables['elements']['field_video_url']);
      $url = $urlObject[0]['#url']->toString();
      $video = new videoEmbed($url, $options, $variables);
      $video->embed();
    }

    if(isset($variables['elements']['field_image'])){
      $variables['image'] = NegnetUtilities::elementChildren($variables['elements']['field_image']);
      foreach($variables['image'] as &$image) {
        $this->setupImage($image);
      }
    }

    if(isset($variables['elements']['field_caption'])){
      $variables['captions'] = NegnetUtilities::elementChildren($variables['elements']['field_caption']);
    }

  }

}
