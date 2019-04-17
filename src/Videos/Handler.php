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
   * Implements Embed().
   */
  public function embed() {
    $this->variables['type'] = 'none';
    return '';
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
