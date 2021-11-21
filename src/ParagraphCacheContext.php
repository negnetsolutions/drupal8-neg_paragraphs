<?php

namespace Drupal\neg_paragraphs;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\RequestStackCacheContextBase;
use Drupal\Core\Cache\Context\CalculatedCacheContextInterface;

/**
 * Defines the CookiesCacheContext service, for "per cookie" caching.
 *
 * Cache context ID: 'paragraph_view_mode' (to vary by all cookies).
 * Calculated cache context ID: 'cookies:%name', e.g. 'cookies:device_type' (to
 * vary by the 'device_type' cookie).
 */
class ParagraphCacheContext extends RequestStackCacheContextBase implements CalculatedCacheContextInterface {

  /**
   * {@inheritdoc}
   */
  public static function getLabel() {
    return t('Paragraph Cache Context');
  }

  /**
   * {@inheritdoc}
   */
  public function getContext($cookie = NULL) {
    $route_name = \Drupal::routeMatch()->getRouteName();

    if (strstr($route_name, 'edit_form')) {
      return 'paragraph_preview';
    }

    return 'normal';
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata($cookie = NULL) {
    return new CacheableMetadata();
  }

}
