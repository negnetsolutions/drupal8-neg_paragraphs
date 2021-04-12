<?php

namespace Drupal\neg_paragraphs\TwigExtension;

use Drupal\negnet_utility\FieldUtilities;

use Twig_Extension;
use Twig_SimpleFilter;

/**
 * Class TwigFilterExtension.
 */
class NegParagraphsColumnCount extends Twig_Extension {

  /**
   * {@inheritdoc}
   * This function must return the name of the extension. It must be unique.
   */
  public function getName() {
    return 'neg_paragraphs_twig_formatters.twig_extension';
  }

  /**
   * Sheesh.
   */
  public function getFilters() {
    return [
      new Twig_SimpleFilter('numberToName', [$this, 'numberToName']),
    ];
  }

  /**
   * The actual implementation of the filter.
   */
  public function numberToName($context) {
    if (is_numeric($context)) {
      $context = FieldUtilities::numberToName($context);
    }
    return $context;
  }

}
