<?php

namespace Drupal\neg_paragraphs\videos;

class handler {

  protected $url;
  protected $options;
  protected $variabled;

  public function __construct(string $url, array $options, array &$variables) {
    $this->url = $url;
    $this->options = $options;
    $this->variables = &$variables;
  }

  public function embed() {
    $this->variables['type'] = 'none';
    return '';
  }

  protected function addParameter(&$link,$parameter){
    $query = parse_url($link,PHP_URL_QUERY);
    $delim = '?';
    if($query){
      $delim = '&';
    }

    $link .= $delim.$parameter;
  }

}
