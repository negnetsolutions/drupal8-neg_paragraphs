<?php

namespace Drupal\neg_paragraphs\Videos;

/**
 * Video Embedder.
 */
class VideoEmbed {

  protected $url;
  protected $handler;
  protected $options;
  protected $variables;

  /**
   * Implements Constructor.
   */
  public function __construct(string $url, array $options, array &$variables) {
    $this->url = $url;
    $this->options = $options;
    $this->variables = &$variables;

    $this->handler = $this->getHandler();
  }

  /**
   * Gets the right handler by video type.
   */
  protected function getHandler() {
    $type = $this->detectType();
    $handler = new $type($this->url, $this->options, $this->variables);
    return $handler;
  }

  /**
   * Detects video type.
   */
  protected function detectType() {

    $handler = 'Handler';
    if (strstr($this->url, 'vimeo.com') !== FALSE) {
      $handler = 'Vimeo';
    }
    elseif (strstr($this->url, 'youtube.com') !== FALSE) {
      $handler = 'Youtube';
    }

    return "Drupal\\neg_paragraphs\\videos\\$handler";
  }

  /**
   * Embeds a video.
   */
  public function embed() {
    $this->variables['video'] = $this->handler->embed();
    return TRUE;
  }

}
