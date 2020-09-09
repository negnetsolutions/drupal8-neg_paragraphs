<?php

namespace Drupal\neg_paragraphs\Videos;

/**
 * Video Handler.
 */
class Handler {

  protected $url;
  protected $options;
  protected $variabled;

  /**
   * Implements constructor().
   */
  public function __construct(string $url, array $options, array &$variables) {
    $this->url = $url;
    $this->options = $options;
    $this->variables = &$variables;
  }

  /**
   * Gets the embed link.
   */
  public function getEmbedLink() {
    return NULL;
  }

  /**
   * Inserts the video.
   */
  public function insert() {

    $type = 'embed';

    // Detect if we have a popup.
    foreach ($this->options as $option) {
      if ($option == 'lightbox') {
        $type = 'popup';
        break;
      }
    }

    $this->$type();

    if (!isset($this->variables['image'])) {
      // Let's fetch the vimeo poster image.
      $this->variables['image'] = $this->renderCoverImage();
    }

  }

  /**
   * RenderCoverImage base.
   */
  public function renderCoverImage() {
    return [];
  }

  /**
   * Implements Embed().
   */
  public function embed() {
    $this->variables['type'] = 'none';
    $this->variables['attributes']['class'][] = 'embedded';
    return '';
  }

  /**
   * Sets up a popup.
   */
  public function popup() {
    $this->variables['link'] = $this->url;
    $this->variables['attributes']['class'][] = 'lightbox';
    $this->variables['#attached']['library'][] = 'neg_paragraphs/lightbox';
  }


  /**
   * Adds a parameter to a string.
   */
  protected function addParameter(&$link, $parameter) {
    $query = parse_url($link, PHP_URL_QUERY);
    $delim = '?';
    if ($query) {
      $delim = '&';
    }

    $link .= $delim . $parameter;
  }

}
