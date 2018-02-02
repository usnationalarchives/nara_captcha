<?php

namespace Drupal\nara_captcha\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Configure NARA Captcha settings for this site.
 */
class NaraCaptchaAdminSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'nara_captcha_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['nara_captcha.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('nara_captcha.settings');

    $form['general'] = [
      '#type' => 'details',
      '#title' => $this->t('General Settings'),
      '#open' => TRUE,
    ];

    $form['general']['api_url'] = [
      '#default_value' => $config->get('api_url'),
      '#description' => $this->t('The API endpoint to submit responses'),
      '#required' => TRUE,
      '#title' => $this->t('API Url'),
      '#type' => 'textfield',
    ];

    $form['general']['theme'] = [
      '#default_value' => $config->get('theme'),
      '#required' => TRUE,
      '#title' => $this->t('Theme'),
      '#type' => 'select',
      '#options' => [
        '1' => $this->t('Light'),
        '2' => $this->t('Dark'),
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('nara_captcha.settings');
    $config
      ->set('api_url', $form_state->getValue('api_url'))
      ->set('theme', $form_state->getValue('theme'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
