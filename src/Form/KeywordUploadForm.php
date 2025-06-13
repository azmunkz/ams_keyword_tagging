<?php

namespace Drupal\ams_keyword_tagging\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for uploading AMS keywords via CSV.
 */
class KeywordUploadForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ams_keyword_tagging_keyword_upload';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#attributes']['enctype'] = 'multipart/form-data';

    $form['csv_upload'] = [
      '#type' => 'file',
      '#title' => $this->t('Upload CSV File'),
      '#description' => $this->t('Must be a .csv file. Format: keyword,langcode,group_label'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Upload'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    if (!isset($_FILES['files']['tmp_name']['csv_upload'])) {
      $form_state->setErrorByName('csv_upload', $this->t('Please upload a CSV file.'));
      return;
    }

    $filename = $_FILES['files']['name']['csv_upload'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if ($ext !== 'csv') {
      $form_state->setErrorByName('csv_upload', $this->t('Only .csv files are allowed.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    if (!isset($_FILES['files']['tmp_name']['csv_upload'])) {
      $this->messenger()->addError($this->t('CSV file not found.'));
      return;
    }

    $file_path = $_FILES['files']['tmp_name']['csv_upload'];
    $timestamp = \Drupal::time()->getCurrentTime();
    $count = 0;
    $skipped = 0;

    if (($handle = fopen($file_path, 'r')) !== FALSE) {
      while (($data = fgetcsv($handle)) !== FALSE) {
        if (count($data) >= 3) {
          [$keyword, $langcode, $group_label] = array_map('trim', $data);
          if (empty($keyword)) {
            continue;
          }

          // Check for duplicate
          $exists = \Drupal::database()->select('ams_keywords', 'k')
            ->fields('k', ['id'])
            ->where('LOWER(k.keyword) = :keyword', [':keyword' => strtolower($keyword)])
            ->condition('k.langcode', $langcode)
            ->condition('k.group_label', $group_label)
            ->execute()
            ->fetchField();

          if ($exists) {
            $skipped++;
            continue;
          }

          // Insert new keyword
          \Drupal::database()->insert('ams_keywords')
            ->fields([
              'keyword' => $keyword,
              'langcode' => $langcode,
              'group_label' => $group_label,
              'created' => $timestamp,
              'updated' => $timestamp,
            ])
            ->execute();

          $count++;
        }
      }
      fclose($handle);
    }

    $this->messenger()->addStatus($this->t('@count keywords imported. @skipped duplicates skipped.', [
      '@count' => $count,
      '@skipped' => $skipped,
    ]));

    $form_state->setRedirect('ams_keyword_tagging.list');
  }

}
