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

    $options = array_unique($options);

    $id = $this->getVideoId();

    $cloudflareOptions = [
      [
        'controls' => 'false',
      ],
      [
        'preload' => 'auto',
      ],
    ];

    $options = array_merge($options, $cloudflareOptions);

    $getOptions = '';
    if (count($options) > 0) {
      $getOptions .= '?';
      foreach ($options as $option) {
        if (is_array($option)) {
          $keys = array_keys($option);
          $getOptions .= $keys[0] . '=' . urlencode($option[$keys[0]]) . '&';
        }
        else {
          $getOptions .= $option . '=true&';
        }
      }
      $getOptions = substr_replace($getOptions ,'', -1);
    }

    $code = <<<EOL
<stream src="$id">
  <div style="position: relative; height: 0px; width: 100%; padding-top: 56.25%;">
    <iframe
      src="https://iframe.videodelivery.net/$id$getOptions"
      style="height: 100%; width: 100%; position: absolute; top: 0px; left: 0px; border: none;"
      allow="autoplay; encrypted-media; picture-in-picture;"
      allowfullscreen="true"
      title="Video"
    ></iframe>
  </div>
</stream>
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
