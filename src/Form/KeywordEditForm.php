<?php

namespace Drupal\ams_keyword_tagging\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class KeywordEditForm extends FormBase {

  public function getFormId(): string {
    return 'ams_keyword_tagging_edit_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL): array {
    $record = \Drupal::database()->select('ams_keywords', 'k')
      ->fields('k')
      ->condition('id', $id)
      ->execute()
      ->fetchAssoc();

    if (!$record) {
      $this->messenger()->addError($this->t('Keyword not found.'));
      return $form;
    }

    $form['id'] = [
      '#type' => 'hidden',
      '#value' => $record['id'],
    ];

    $form['keyword'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Keyword'),
      '#default_value' => $record['keyword'],
      '#required' => TRUE,
    ];

    $form['langcode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Language'),
      '#default_value' => $record['langcode'],
    ];

    $form['group_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Group Label'),
      '#default_value' => $record['group_label'],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $id = $form_state->getValue('id');
    \Drupal::database()->update('ams_keywords')
      ->fields([
        'keyword' => $form_state->getValue('keyword'),
        'langcode' => $form_state->getValue('langcode'),
        'group_label' => $form_state->getValue('group_label'),
        'updated' => \Drupal::time()->getCurrentTime(),
      ])
      ->condition('id', $id)
      ->execute();

    $this->messenger()->addStatus($this->t('Keyword updated.'));
    $form_state->setRedirect('ams_keyword_tagging.list');
  }
}
