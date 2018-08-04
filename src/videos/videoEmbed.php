<?php

namespace Drupal\neg_paragraphs\videos;

use Drupal\neg_paragraphs\videos\youtube;
use Drupal\neg_paragraphs\videos\vimeo;

class videoEmbed {

  protected $url;
  protected $handler;
  protected $options;
  protected $variables;

  public function __construct(string $url, array $options, array &$variables) {
    $this->url = $url;
    $this->options = $options;
    $this->variables = &$variables;

    $this->handler = $this->getHandler();
  }

  protected function getHandler() {
    $type = $this->detectType();
    $handler = new $type($this->url, $this->options, $this->variables);
    return $handler;
  }
  protected function detectType() {

    $handler = 'handler';
    if(strstr($this->url,'vimeo.com') !== false) {
      $handler = 'vimeo';
    } else if(strstr($this->url,'youtube.com') !== false) {
      $handler = 'youtube';
    }

    return "Drupal\\neg_paragraphs\\videos\\$handler";
  }

  public function embed() {
    $this->variables['video'] = $this->handler->embed();
    return true;
  }

}
