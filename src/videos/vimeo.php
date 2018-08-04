<?php

namespace Drupal\neg_paragraphs\videos;

use Drupal\neg_paragraphs\videos\handler;

class vimeo extends handler {

  public function embed() {

    $this->variables['#attached']['library'][] = 'neg_paragraphs/vimeo';

    $video_id = $this->getVideoID();
    $link = "//player.vimeo.com/video/".$video_id;

    foreach($this->options as $option) {
      switch($option) {
        case 'autoplay':
          $this->addParameter($link,'autoplay=1');
          break;
        case 'autopause':
          $this->addParameter($link,'autopause=1');
          break;
        case 'loop':
          $this->addParameter($link,'loop=1');
          break;
        case 'background':
          $this->addParameter($link,'background=1');
          break;
      }
    }

    $this->addParameter($link,'badge=0&byline=0&portrait=0&title=0');

    $this->variables['type'] = 'vimeo';

    return $link;
  }

  protected function getVideoID() {
    return substr($this->url, strrpos($this->url, '/') + 1);
  }

}
