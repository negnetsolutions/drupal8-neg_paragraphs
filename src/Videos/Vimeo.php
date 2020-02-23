<?php

namespace Drupal\neg_paragraphs\Videos;

use Drupal\negnet_utility\VimeoVideo;

/**
 * Vimeo Video Handler.
 */
class Vimeo extends Handler {

  /**
   * Gets the video link.
   */
  public function getEmbedLink() {
    $link = "//player.vimeo.com/video/" . $this->getVideoId();

    foreach ($this->options as $option) {
      switch ($option) {
        case 'autoplay':
          $this->addParameter($link, 'autoplay=1');
          break;

        case 'autopause':
          $this->addParameter($link, 'autopause=1');
          break;

        case 'loop':
          $this->addParameter($link, 'loop=1');
          break;

        case 'background':
          $this->addParameter($link, 'background=1');
          break;
      }
    }

    $this->addParameter($link, 'badge=0&byline=0&portrait=0&title=0');

    return $link;
  }

  /**
   * Implements embed function.
   */
  public function embed() {

    parent::embed();

    $this->variables['#attached']['library'][] = 'neg_paragraphs/vimeo';

    $link = $this->getEmbedLink();

    $this->variables['type'] = 'vimeo';

    $this->variables['embedlink'] = $link;
  }

  /**
   * Sets up a popup.
   */
  public function popup() {
    parent::popup();
  }

  /**
   * RenderCoverImage base.
   */
  public function renderCoverImage() {
    // Let's fetch the vimeo poster image.
    $video = new VimeoVideo($this->url);
    return $video->renderCoverImage();
  }

  /**
   * Gets the video id from the video url.
   */
  protected function getVideoId() {
    return substr($this->url, strrpos($this->url, '/') + 1);
  }

}
