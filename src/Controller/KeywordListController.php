<?php

namespace Drupal\ams_keyword_tagging\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns AMS Keyword listing page.
 */
class KeywordListController extends ControllerBase {

  /**
   * Displays a placeholder keyword list page.
   */
  public function list() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('AMS Keyword list will appear here.'),
    ];
  }

}
