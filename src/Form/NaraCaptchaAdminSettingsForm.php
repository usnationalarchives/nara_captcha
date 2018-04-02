<?php

namespace Drupal\nara_captcha\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

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

    // Set Known Ids and Url from Config.
    $form_state->set('known_url', $config->get('known_url'));
    $form_state->set('known_ids', $config->get('known_ids'));
    $knownnaIds = $form_state->get('known_ids');
    if ($knownnaIds === NULL) {
      drupal_set_message(t('No known items have been added. Click the button below to add some.'), 'warning');
    }

    // Set Unknown Ids and Url from Config.
    $form_state->set('unknown_url', $config->get('unknown_url'));
    $form_state->set('unknown_ids', $config->get('unknown_ids'));
    $unKnownnaIds = $form_state->get('unknown_ids');
    if ($unKnownnaIds === NULL) {
      drupal_set_message(t('No unknown items have been addeed. Click the button below to add some.'), 'warning');
    }

    $form['general'] = [
      '#type' => 'details',
      '#title' => $this->t('General Settings'),
      '#open' => TRUE,
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

    $form['general']['number_of_known'] = [
      '#default_value' => $config->get('number_of_known'),
      '#required' => TRUE,
      '#title' => $this->t('Number of known items in Captcha'),
      '#type' => 'select',
      '#options' => [
        '1' => $this->t('1'),
        '2' => $this->t('2'),
      ],
    ];

    $form['general']['honeypot_fieldname'] = [
      '#default_value' => $config->get('honeypot_fieldname'),
      '#description' => $this->t('Use a common field term, with only uppercase, lower case, and spaces.'),
      '#required' => FALSE,
      '#title' => $this->t('HoneyPot Field Name'),
      '#type' => 'textfield',
    ];

    // Login Section.
    $form['credentials'] = [
      '#type' => 'details',
      '#title' => $this->t('API Settings'),
      '#open' => TRUE,
    ];

    $form['credentials']['api_url'] = [
      '#default_value' => $config->get('api_url'),
      '#description' => $this->t('The API endpoint to submit responses. Must end with "/".'),
      '#required' => TRUE,
      '#title' => $this->t('API Url'),
      '#type' => 'textfield',
    ];

    $form['credentials']['username'] = [
      '#default_value' => $config->get('username'),
      '#description' => $this->t('Username for API.'),
      '#required' => FALSE,
      '#title' => $this->t('API Username'),
      '#type' => 'textfield',
    ];

    $form['credentials']['password'] = [
      '#default_value' => $config->get('password'),
      '#description' => $this->t('Password for API'),
      '#required' => FALSE,
      '#title' => $this->t('API Password'),
      '#type' => 'textfield',
    ];

    $form['credentials']['tag'] = [
      '#default_value' => $config->get('tag'),
      '#description' => $this->t('The tag to pass to the API'),
      '#required' => FALSE,
      '#title' => $this->t('Tag String'),
      '#type' => 'textfield',
    ];

    $form['credentials']['api_token'] = [
      '#default_value' => $config->get('api_token'),
      '#description' => $this->t('Token from API'),
      '#required' => FALSE,
      '#title' => $this->t('API Token'),
      '#type' => 'textfield',
      '#attributes' => [
        'disabled' => 'disabled',
      ],
    ];

    // Token only lasts for a short time, so may need to get token on cron.
    $form['credentials']['get_token'] = [
      '#type' => 'submit',
      '#submit' => ['::getApiToken'],
      '#ajax' => [
        'callback' => '::returnApiToken',
        'wrapper' => 'edit-credentials',
      ],
      '#value' => $this->t('Get Token'),
    ];

    $form['captcha_items'] = [
      '#type' => 'details',
      '#title' => 'Captcha Items',
      '#open' => 'TRUE',
    ];

    // Get Known items, these will block captcha if chosen.
    $form['captcha_items']['known'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Known Items'),
    ];

    $form['captcha_items']['known']['known_url'] = [
      '#type' => 'textarea',
      '#title' => $this->t('The API call to return known objects with "handwritten" tags.'),
      '#default_value' => $form_state->get('known_url'),
      '#description' => $this->t('Known Items are used as a control in the captcha. If chosen, this item will block form submission.'),
    ];
    $known_btn = '';
    $known_btn = 'Add Known Items';
    if ($knownnaIds !== NULL) {
      $known_btn = 'Update Known Items';
    }

    $form['captcha_items']['known']['get_known'] = [
      '#type' => 'submit',
      '#submit' => ['::getKnownItems'],
      '#ajax' => [
        'callback' => '::returnKnownItems',
        'wrapper' => 'edit-known',
      ],
      '#value' => $this->t('@known_btn', ['@known_btn' => $known_btn]),
    ];

    // Get Unknown Items to compare against.
    $form['captcha_items']['unknown'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Unknown Items'),
    ];

    $form['captcha_items']['unknown']['unknown_url'] = [
      '#type' => 'textarea',
      '#title' => $this->t('The API call to return objects with no tags.'),
      '#default_value' => $form_state->get('unknown_url'),
      '#description' => $this->t('These items fill out the rest of the captcha. They may or may not have handwritten language, but when chosen as typed, will submit a tag back to the API.'),
    ];

    $unknown_btn = 'Add Unknown Items';
    if ($unKnownnaIds !== NULL) {
      $unknown_btn = 'Update Unknown Items';
    }

    $form['captcha_items']['unknown']['get_unknown'] = [
      '#type' => 'submit',
      '#submit' => ['::getUnknownItems'],
      '#ajax' => [
        'callback' => '::returnUnknownItems',
        'wrapper' => 'edit-unknown',
      ],
      '#value' => $this->t('@unknown_btn', ['@unknown_btn' => $unknown_btn]),
    ];

    $form['accessibility'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Accessibility Options'),
    ];

    $form['accessibility']['word_list'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Possible Accessibility Words'),
      '#default_value' => $config->get('accessibility_words'),
      '#description' => $this->t('Please enter a comma separated list of possible words to appear.'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('nara_captcha.settings');
    $config
      ->set('theme', $form_state->getValue('theme'))
      ->set('number_of_known', $form_state->getValue('number_of_known'))
      ->set('honeypot_fieldname', $form_state->getValue('honeypot_fieldname'))
      ->set('accessibility_words', $form_state->getValue('word_list'))
      ->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getKnownItems(array &$form, FormStateInterface $form_state) {
    $client = \Drupal::httpClient();
    $api_url = trim($form_state->getValue('known_url'));
    $knownItems = '';
    $res = json_decode($client->request('GET', $api_url)->getBody()->getContents());
    if (!$res->opaResponse->results->result) {
      drupal_set_message(t('No results for for your current query.'), 'error', TRUE);
    }
    foreach ($res->opaResponse->results->result as $result) {
      $knownItems .= $result->naId . ',';
    }

    $form_state->set('known_items', trim($knownItems, ','));
    $config = $this->config('nara_captcha.settings');
    $config
      ->set('known_ids', $form_state->get('known_items'))
      ->set('known_url', $form_state->getValue('known_url'))
      ->save();
    $form_state->setRebuild();
    drupal_get_messages();
  }

  /**
   * {@inheritdoc}
   */
  public function getUnknownItems(array &$form, FormStateInterface $form_state) {
    $client = \Drupal::httpClient();
    $api_url = trim($form_state->getValue('unknown_url'));
    $unknownItems = '';
    $res = json_decode($client->request('GET', $api_url)->getBody()->getContents());

    if (!$res->opaResponse->results->result) {
      drupal_set_message(t('No results for for your current query.'), 'error', TRUE);
    }

    foreach ($res->opaResponse->results->result as $result) {
      $unknownItems .= $result->naId . ',';
    }

    $form_state->set('unknown_items', trim($unknownItems, ','));
    $config = $this->config('nara_captcha.settings');
    $config
      ->set('unknown_ids', $form_state->get('unknown_items'))
      ->set('unknown_url', $form_state->getValue('unknown_url'))
      ->save();
    $form_state->setRebuild();
    drupal_get_messages();
  }

  /**
   * {@inheritdoc}
   */
  public function getApiToken(array &$form, FormStateInterface $form_state) {
    $client = \Drupal::httpClient();
    $config = $this->config('nara_captcha.settings');

    $api_url = $form_state->getValue('api_url');
    $user = $form_state->getValue('username');
    $password = $form_state->getValue('password');
    $api_url .= 'login?user=' . $user . '&password=' . $password;
    $res = json_decode($client->request('POST', $api_url)->getBody()->getContents());
    if ($res->opaResponse->header->{'@status'} === '200') {
      $config
        ->set('api_token', $res->opaResponse->credentials)
        ->set('tag', $form_state->getValue('tag'))
        ->set('api_url', $form_state->getValue('api_url'))
        ->set('username', $form_state->getValue('username'))
        ->set('password', $form_state->getValue('password'))
        ->save();
    }
    else {
      drupal_set_message($res->opaResponse->header->{'@status'}, 'error');
    }

    $form_state->setRebuild();
    drupal_get_messages();
  }

  /**
   * {@inheritdoc}
   */
  public function returnKnownItems(array &$form, FormStateInterface $form_state) {
    return $form['captcha_items']['known'];
  }

  /**
   * {@inheritdoc}
   */
  public function returnUnknownItems(array &$form, FormStateInterface $form_state) {
    return $form['captcha_items']['unknown'];
  }

  /**
   * {@inheritdoc}
   */
  public function returnApiToken(array &$form, FormStateInterface $form_state) {
    return $form['credentials'];
  }

}
