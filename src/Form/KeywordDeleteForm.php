<?php

namespace Drupal\ams_keyword_tagging\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class KeywordDeleteForm extends ConfirmFormBase {

  protected $id;

  public function getFormId(): string {
    return 'ams_keyword_tagging_delete_form';
  }

  public function getQuestion(): string {
    return $this->t('Are you sure you want to delete this keyword?');
  }

  public function getCancelUrl(): Url {
    return Url::fromRoute('ams_keyword_tagging.list');
  }

  public function getConfirmText(): string {
    return $this->t('Delete');
  }

  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL): array {
    $this->id = $id;
    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    \Drupal::database()->delete('ams_keywords')
      ->condition('id', $this->id)
      ->execute();

    $this->messenger()->addStatus($this->t('Keyword deleted.'));
    $form_state->setRedirectUrl($this->getCancelUrl());
  }
}
