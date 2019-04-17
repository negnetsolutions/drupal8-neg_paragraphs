<?php

/**
 * @file
 * Negnet Solutions Paragraph Tokens.
 */

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\neg_paragraphs\ParagraphHelper;
use Drupal\image\Entity\ImageStyle;
use Drupal\file\Entity\File;

/**
 * Implements hook_token_info().
 */
function neg_paragraphs_token_info() {
  return [
    'tokens' => [
      'node' => [
        'paragraph_text' => [
          'name' => t('Paragraph Text'),
          'description' => t('The full output of text-based paragraph fields'),
        ],
        'paragraph_images' => [
          'name' => t('Paragraph Images'),
          'description' => t("A summary image based on the node's paragraphs."),
        ],
      ],
    ],
  ];
}

/**
 * Implements hook_tokens().
 */
function neg_paragraphs_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $replacements = [];

  if ($type == 'node') {

    $token_service = \Drupal::token();

    foreach ($tokens as $name => $original) {
      if ($paragraph_text_tokens = $token_service->findWithPrefix($tokens, 'paragraph_images')) {

        $images = [];

        if ($node = $data['node']) {

          $paragraph_entities = ParagraphHelper::fetchColumnsOfType(['image', 'text_box'], $node);
          foreach ($paragraph_entities as $paragraph_entity) {
            switch ($paragraph_entity->getType()) {
              case 'image':
                $fields = ParagraphHelper::fetchFieldsOfType(['field_image'], $paragraph_entity);
                foreach ($fields as $field) {
                  $field_images = $field->getValue();
                  foreach ($field_images as $image) {
                    $file = File::load($image['target_id']);
                    if ($file) {
                      $path = $file->getFileUri();
                      $url = ImageStyle::load('rs_image_1200w')->buildUrl($path);
                      $images[] = $url;
                    }
                  }
                }
                break;

              case 'text_box':
                $fields = ParagraphHelper::fetchFieldsOfType(['field_body'], $paragraph_entity);
                foreach ($fields as $field) {
                  $textboxes = $field->getValue();
                  foreach ($textboxes as $textbox) {
                    if (isset($textbox['value'])) {
                      if (preg_match_all('/<img[\\w\\W]+?class="([a-zA-Z-_0-9]+)"[\\w\\W]+?src="\/sites\/default\/files\/([\\w\\s-?=\\.\/]+)"[\\w\\W]+?\/>/u', $textbox['value'], $matches_code)) {
                        foreach ($matches_code[0] as $ci => $code) {
                          // Get image filename.
                          $img = $matches_code[2][$ci];

                          // Remove any parameters.
                          $imgf = strstr($img, '?', TRUE);
                          if ($imgf !== FALSE) {
                            $img = $imgf;
                          }

                          $uri = "public://" . $img;
                          $url = ImageStyle::load('rs_image_1200w')->buildUrl($uri);
                          $images[] = $url;
                        }
                      }
                    }
                  }

                }
                break;
            }

          }
        }

        if (count($images) > 0) {
          foreach ($paragraph_text_tokens as $token_key => $token_full) {
            switch ($token_key) {
              case 'single':
                $images = [reset($images)];
                break;
            }

          }
          $replacement = implode(',', $images);
          $replacements[$original] = $replacement;
        }
      }

      if ($paragraph_text_tokens = $token_service->findWithPrefix($tokens, 'paragraph_text')) {

        $text = [];

        if ($node = $data['node']) {

          $paragraph_entities = ParagraphHelper::fetchColumnsOfType(['text_box'], $node);
          foreach ($paragraph_entities as $paragraph_entity) {
            $fields = ParagraphHelper::fetchFieldsOfType(['field_body'], $paragraph_entity);
            foreach ($fields as $field) {
              $text_values = $field->getValue();
              foreach ($text_values as $text_value) {
                $text[] = $text_value['value'];
              }
            }
          }
        }

        if (count($text) > 0) {
          foreach ($paragraph_text_tokens as $token_key => $token_full) {
            foreach (explode(':', $token_key) as $key) {
              switch ($key) {
                case 'summary':
                  foreach ($text as $i => &$t) {
                    $t = ParagraphHelper::formatText($t, 158);
                  }
                  break;

                case 'single-line':
                  foreach ($text as $i => &$t) {
                    $breaks = ["<br />", "<br>", "<br/>"];
                    $t = str_ireplace($breaks, "\n", $t);
                    $t = str_replace("\n", ' ', $t);
                  }
                  break;

                case 'single':
                  $text = [reset($text)];
                  break;
              }
            }
          }

          $text_content = implode(' ', $text);
          $replacements[$original] = $text_content;
        }
      }
    }
  }

  return $replacements;
}
