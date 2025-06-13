<?php

namespace Drupal\ams_keyword_tagging\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form to add a single AMS keyword.
 */
class KeywordAddForm extends FormBase {

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs the form object.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ams_keyword_tagging_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['keyword'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Keyword'),
      '#required' => TRUE,
    ];

    $form['langcode'] = [
      '#type' => 'select',
      '#title' => $this->t('Language'),
      '#options' => [
        'en' => 'English',
        'ms' => 'Malay',
        'zh' => 'Chinese',
        'ta' => 'Tamil',
        'und' => 'Undefined',
      ],
      '#default_value' => 'und',
    ];

    $form['group_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Group Label'),
      '#description' => $this->t('Optional group for organizational purposes.'),
      '#required' => FALSE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $keyword = $form_state->getValue('keyword');
    $langcode = $form_state->getValue('langcode');
    $group_label = $form_state->getValue('group_label');

    $this->database->insert('ams_keywords')
      ->fields([
        'keyword' => $keyword,
        'langcode' => $langcode,
        'group_label' => $group_label,
        'created' => \Drupal::time()->getCurrentTime(),
        'updated' => \Drupal::time()->getCurrentTime(),
      ])
      ->execute();

    $this->messenger()->addMessage($this->t('Keyword "%keyword" has been added.', ['%keyword' => $keyword]));

    $form_state->setRedirect('ams_keyword_tagging.list');
  }

}
