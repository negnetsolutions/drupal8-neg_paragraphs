<?php

namespace Drupal\neg_paragraphs\Videos;

use Drupal\negnet_utility\YoutubeVideo;

/**
 * Cloudflare Video Handler.
 */
class Cloudflare extends Handler {

  protected $videoId;

  /**
   * Inserts the video.
   */
  public function insert() {

    $type = 'embed';
    $this->$type();

  }

  /**
   * Gets embedCode.
   */
  protected function getEmbedCode() {

    $options = [];
    foreach ($this->options as $option) {
      switch ($option) {
        case 'autoplay':
          $options[] = $option;
          break;

        case 'muted':
          $options[] = $option;
          break;

        case 'loop':
          $options[] = $option;
          break;

        case 'background':
          $options[] = 'autoplay';
          $options[] = 'muted';
          $options[] = 'loop';
          break;
      }
    }

    if (!in_array('autoplay', $options)) {
      $options[] = 'controls';
    }

    $options = array_unique($options);
    $options = implode(' ', $options);

    $id = $this->getVideoId();
    $code = <<<EOL
<stream src="$id" $options></stream>
<script data-cfasync="false" defer type="text/javascript" src="https://embed.videodelivery.net/embed/r4xu.fla9.latest.js?video=$id"></script>
EOL;

    return $code;
  }

  /**
   * Embeds a youtube video.
   */
  public function embed() {

    parent::embed();

    $this->variables['type'] = 'cloudflare';

    $id = $this->getVideoId();
    if (!isset($GLOBALS['cloudFlareVideoID'])) {
      $GLOBALS['cloudFlareVideoID'] = [];
    }
    $GLOBALS['cloudFlareVideoID'][] = $id;

    $this->variables['#attached']['library'][] = 'neg_paragraphs/cloudflare_player';
    $this->variables['embedCode'] = $this->getEmbedCode();
  }

  /**
   * Sets up a popup.
   */
  public function popup() {
    parent::popup();
  }

  /**
   * Gets the video id from the video url.
   */
  protected function getVideoId() {

    if (isset($this->videoId)) {
      return $this->videoId;
    }

    if (strstr($this->url, 'watch.cloudflarestream.com') !== FALSE) {
      $this->videoId = substr($this->url, strrpos($this->url, '/') + 1);
      return $this->videoId;
    }

    $this->videoId = FALSE;
    return $this->videoId;
  }

}
