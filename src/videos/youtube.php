<?php

namespace Drupal\neg_paragraphs\videos;

/**
 * Youtube Video Handler.
 */
class Youtube extends Handler {

  /**
   * Embeds a youtube video.
   */
  public function embed() {
    $this->variables['type'] = 'youtube';

    $this->variables['#attached']['library'][] = 'neg_paragraphs/youtube';

    $video_id = $this->getVideoId();
    $link = "//www.youtube.com/embed/" . $video_id;

    foreach ($this->options as $option) {
      switch ($option) {
        case 'autoplay':
          $this->addParameter($link, 'autoplay=1');
          break;

        case 'loop':
          $this->addParameter($link, 'loop=1');
          break;
      }
    }

    $this->addParameter($link, 'rel=0&showinfo=0&enablejsapi=1&modestbranding=1');
    return $link;
  }

  /**
   * Gets the video id from the video url.
   */
  protected function getVideoId() {

    if (strstr($this->url, 'youtube.com/embed') !== FALSE) {
      return substr($this->url, strrpos($this->url, '/') + 1);
    }
    elseif (preg_match('/\\?v=([a-zA-Z-_0-9]+)/u', $this->url, $matches) !== FALSE) {
      return $matches[1];
    }

    return FALSE;
  }

}
