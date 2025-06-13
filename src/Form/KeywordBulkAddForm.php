<?php

namespace Drupal\ams_keyword_tagging\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form to add multiple AMS keywords at once.
 */
class KeywordBulkAddForm extends FormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs the form.
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
    return 'ams_keyword_bulk_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['keywords'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Keywords'),
      '#description' => $this->t('Enter one keyword per line.'),
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
      '#description' => $this->t('Optional group for all keywords.'),
      '#required' => FALSE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Keywords'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $keywords_input = $form_state->getValue('keywords');
    $langcode = $form_state->getValue('langcode');
    $group_label = $form_state->getValue('group_label');
    $timestamp = \Drupal::time()->getCurrentTime();

    $keywords = array_filter(array_map('trim', explode("\n", $keywords_input)));

    $insert = $this->database->insert('ams_keywords')->fields([
      'keyword',
      'langcode',
      'group_label',
      'created',
      'updated',
    ]);

    foreach ($keywords as $keyword) {
      $insert->values([
        $keyword,
        $langcode,
        $group_label,
        $timestamp,
        $timestamp,
      ]);
    }

    $insert->execute();

    $this->messenger()->addMessage($this->t('Added %count keywords successfully.', ['%count' => count($keywords)]));
    $form_state->setRedirect('ams_keyword_tagging.list');
  }

}
