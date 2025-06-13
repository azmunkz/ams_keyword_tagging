<?php

namespace Drupal\ams_keyword_tagging\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Datetime\DateFormatterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class KeywordListController extends ControllerBase {

  /**
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  public function __construct(DateFormatterInterface $date_formatter) {
    $this->dateFormatter = $date_formatter;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter')
    );
  }

  /**
   * Display the list of AMS keywords.
   */
  public function list(): array {
    $header = [
      'id' => $this->t('ID'),
      'keyword' => $this->t('Keyword'),
      'group_label' => $this->t('Group Label'),
      'langcode' => $this->t('Language'),
      'created' => $this->t('Created'),
      'updated' => $this->t('Updated'),
      'operations' => $this->t('Operations'),
    ];

    $rows = [];
    $results = \Drupal::database()->select('ams_keywords', 'k')
      ->fields('k', ['id', 'keyword', 'group_label', 'langcode', 'created', 'updated'])
      ->orderBy('id', 'DESC')
      ->execute();

    foreach ($results as $record) {
      $rows[] = [
        'id' => $record->id,
        'keyword' => $record->keyword,
        'group_label' => $record->group_label,
        'langcode' => $record->langcode,
        'created' => $this->dateFormatter->format($record->created, 'short'),
        'updated' => $this->dateFormatter->format($record->updated, 'short'),
        'operations' => [
          'data' => [
            '#type' => 'operations',
            '#links' => [
              'edit' => [
                'title' => $this->t('Edit'),
                'url' => Url::fromRoute('ams_keyword_tagging.edit', ['id' => $record->id]),
              ],
              'delete' => [
                'title' => $this->t('Delete'),
                'url' => Url::fromRoute('ams_keyword_tagging.delete', ['id' => $record->id]),
              ],
            ],
          ],
        ],
      ];
    }

    return [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No keywords found.'),
    ];
  }
}
