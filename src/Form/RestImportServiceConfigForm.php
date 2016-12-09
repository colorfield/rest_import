<?php

namespace Drupal\rest_import\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * Class RestImportServiceConfigForm.
 *
 * @package Drupal\rest_import\Form
 */
class RestImportServiceConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'rest_import.web_service',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rest_import_service_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // @todo define web service status response flag

    $config = $this->config('rest_import.web_service');
    $form['endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Endpoint'),
      '#description' => $this->t('Endpoint of the Rest API with the protocol and without a trailing slash (https://my.domain.com/service)'),
      '#maxlength' => 255,
      '#size' => 80,
      '#default_value' => $config->get('endpoint'),
    ];
    $form['debug_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Debug endpoint'),
      '#description' => $this->t('Debug endpoint of the Rest API with the protocol and without a trailing slash'),
      '#maxlength' => 255,
      '#size' => 80,
      '#default_value' => $config->get('debug_endpoint'),
    ];
    $form['debug_mode'] = [
      '#type' => 'radios',
      '#title' => $this->t('Debug mode'),
      '#description' => $this->t('Fetch test JSON files from debug endpoint and print debug info.'),
      '#options' => array(0 => $this->t('No'), 1 => $this->t('Yes')),
      '#default_value' => $config->get('debug_mode'),
    ];
    $form['items_limit'] = [
      '#type' => 'number',
      '#title' => $this->t('Limit per batch'),
      '#description' => $this->t('Maximum number of items to import per batch.'),
      '#default_value' => $config->get('items_limit'),
    ];
    $form['default_language'] = [
      '#type' => 'language_select',
      '#title' => $this->t('Default language'),
      '#description' => $this->t('Fallback language, if none specified.'),
      '#default_value' => $config->get('default_language'),
    ];
    $form['import_uid'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'user',
      '#title' => $this->t('Import user'),
      '#description' => $this->t('User defined to identify content that is being imported.'),
      '#selection_setttings' => array(
        'include_anonymous' => false,
      ),
      '#default_value' => User::load($config->get('import_uid')),
      // Validation is done in static::validateConfigurationForm().
      '#validate_reference' => false,
    ];
    $form['format'] = [
      '#type' => 'select',
      '#title' => $this->t('Format'),
      '#description' => $this->t('Rest format'),
      '#options' => array('json' => $this->t('JSON'), 'xml' => $this->t('XML'), 'hal' => $this->t('HAL')),
      '#size' => 1,
      '#default_value' => $config->get('format'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $uid = $form_state->getValue('import_user');

    $this->config('rest_import.web_service')
      ->set('endpoint', $form_state->getValue('endpoint'))
      ->set('debug_endpoint', $form_state->getValue('debug_endpoint'))
      ->set('debug_mode', $form_state->getValue('debug_mode'))
      ->set('items_limit', $form_state->getValue('items_limit'))
      ->set('default_language', $form_state->getValue('default_language'))
      ->set('import_uid', $form_state->getValue('import_uid'))
      ->set('format', $form_state->getValue('format'))
      ->save();
  }

}
