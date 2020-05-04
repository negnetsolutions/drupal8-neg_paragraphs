<?php

namespace Drupal\neg_paragraphs\Videos;

/**
 * Youtube Video Handler.
 */
class Youtube extends Handler {

  protected $videoId;
  const IMAGE_DIRECTORY = 'youtube_images';

  /**
   * Gets the video link.
   */
  public function getEmbedLink() {
    $link = "//www.youtube.com/embed/" . $this->getVideoId();

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
   * Embeds a youtube video.
   */
  public function embed() {

    parent::embed();

    $this->variables['type'] = 'youtube';

    $this->variables['#attached']['library'][] = 'neg_paragraphs/youtube';

    $link = $this->getEmbedLink();
    $this->variables['embedlink'] = $link;
  }

  /**
   * Sets up a popup.
   */
  public function popup() {
    parent::popup();
  }

  /**
   * Renders the youtube cover image.
   */
  public function renderCoverImage() {
    if (!$this->isImageDownloaded()) {
      if (!$this->downloadImage()) {
        return [];
      }
    }

    list($width, $height, $type, $attr) = getimagesize($this->getImageUri());

    $image = [
      '#theme' => 'responsive_image',
      '#width' => $width,
      '#height' => $height,
      '#responsive_image_style_id' => 'rs_image',
      '#uri' => $this->getImageUri(),
    ];

    return $image;
  }

  /**
   * Checks to see if downloaded image exists.
   */
  protected function isImageDownloaded() {
    $uri = $this->getImageUri();
    if (is_file($uri)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Gets local image path.
   */
  protected function getImageUri() {
    return $this->getImageDirectory() . '/' . $this->getImageFilename();
  }

  /**
   * Gets the image filename.
   */
  protected function getImageFilename() {
    return $this->getVideoId() . '.jpg';
  }

  /**
   * Gets image storage path.
   */
  protected function getImageDirectory() {
    $default_scheme = file_default_scheme();
    return $default_scheme . '://' . self::IMAGE_DIRECTORY;
  }

  /**
   * Fetches external link to cachepath.
   */
  protected function fetch($url, $cachepath) {
    $http   = \Drupal::httpClient();
    try {
      $result = $http->request('get', $url);
    }
    catch (\Exception $e) {
      drupal_set_message('Could not fetch youtube thumbnail image at ' . $url, 'error');
      return FALSE;
    }

    $code   = floor($result->getStatusCode() / 100) * 100;
    if (!empty($result->getBody()) && $code != 400 && $code != 500) {
      return file_unmanaged_save_data($result->getBody(), $cachepath, FILE_EXISTS_REPLACE);
    }

    return FALSE;
  }

  /**
   * Fetches remote image.
   */
  protected function downloadImage() {
    $external_uri = $this->getExternalImageUri();

    if (file_prepare_directory($this->getImageDirectory(), FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS)) {
      return $this->fetch(
        $this->getExternalImageUri(),
        $this->getImageUri()
      );
    }

    return FALSE;
  }

  /**
   * Gets the external vimeo image uri.
   */
  protected function getExternalImageUri() {
    return 'http://img.youtube.com/vi/' . $this->getVideoId() . '/maxresdefault.jpg';
  }

  /**
   * Gets the video id from the video url.
   */
  protected function getVideoId() {

    if (isset($this->videoId)) {
      return $this->videoId;
    }

    if (strstr($this->url, 'youtube.com/embed') !== FALSE) {
      $this->videoId = substr($this->url, strrpos($this->url, '/') + 1);
      return $this->videoId;
    }
    elseif (preg_match('/\\?v=([a-zA-Z-_0-9]+)/u', $this->url, $matches) !== FALSE) {
      $this->videoId = $matches[1];
      return $this->videoId;
    }

    $this->videoId = FALSE;
    return $this->videoId;
  }

}
