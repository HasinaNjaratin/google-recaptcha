<?php

namespace Drupal\google_recaptcha\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements an configuration form.
 */
class ConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'recaptcha.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'recaptcha_setting_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('recaptcha.settings');

    $form['recaptcha_public_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Public Key'),
      '#required' => TRUE,
      '#default_value' => $config->get('recaptcha_public_key')
    ];

    $form['recaptcha_private_key'] = [
      '#type' => 'password',
      '#title' => $this->t('Private Key'),
      '#required' => TRUE,
      '#default_value' => $config->get('recaptcha_private_key')
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('recaptcha.settings')
      ->set('recaptcha_public_key', $form_state->getValue('recaptcha_public_key'))
      ->set('recaptcha_private_key', $form_state->getValue('recaptcha_private_key'))
      ->save();

    drupal_set_message($this->t('Google reCAPTCHA configuration has been updated.'));
  }

}
