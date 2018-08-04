<?php

namespace Drupal\neg_paragraphs\videos;

use Drupal\neg_paragraphs\videos\handler;

class youtube extends handler {

  public function embed() {
    $this->variables['type'] = 'youtube';

    $this->variables['#attached']['library'][] = 'neg_paragraphs/youtube';

    $video_id = $this->getVideoID();
    $link = "//www.youtube.com/embed/".$video_id;

    foreach($this->options as $option) {
      switch($option) {
        case 'autoplay':
          $this->addParameter($link,'autoplay=1');
          break;
        case 'loop':
          $this->addParameter($link,'loop=1');
          break;
      }
    }

    $this->addParameter($link,'rel=0&showinfo=0&enablejsapi=1&modestbranding=1');
    return $link;
  }

  protected function getVideoID() {

    if(strstr($this->url,'youtube.com/embed') !== false) {
      return substr($this->url, strrpos($this->url, '/') + 1);
    }
    else if(preg_match('/\\?v=([a-zA-Z-_0-9]+)/u', $this->url, $matches) !==false) {
      return $matches[1];
    }

    return false;
  }

}
