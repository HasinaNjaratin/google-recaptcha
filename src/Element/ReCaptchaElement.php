<?php

namespace Drupal\google_recaptcha\Element;

use Symfony\Component\HttpFoundation;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;
use Drupal\google_recaptcha\ReCaptcha;


/**
 * Provides a form input element for entering an CAPTCHA.
 *
 *
 * Example usage:
 * @code
 * $form['captcha'] = array(
 *   '#type' => 'recaptcha',
 * );
 * @end
 *
 * @see \Drupal\google_recaptcha\Element\ReCaptchaElement
 *
 * @FormElement("recaptcha")
 */
class ReCaptchaElement extends FormElement {


  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);

    return [
      '#input' => FALSE,
      '#element_validate' => [
        [$class, 'validateCaptcha'],
      ],
      '#theme' => 'input_recaptcha',
      '#pre_render' => [
        [$class, 'preRenderEmail'],
      ],
      '#theme_wrappers' => ['form_element'],
    ];
  }

  /**
   * Form element validation handler for #type 'recaptcha'.
   *
   */
  public static function validateCaptcha(&$element, FormStateInterface $form_state, &$complete_form) {
    $response = null;

    $input = $form_state->getUserInput();
    if (isset($input['g-recaptcha-response']) && $input['g-recaptcha-response']) {
      $config = \Drupal::config('recaptcha.settings');
      $reCaptcha = new ReCaptcha($config->get('recaptcha_private_key'));
      $response = $reCaptcha->verifyResponse(
	        \Drupal::request()->getClientIp(),
	        $input['g-recaptcha-response']
      );
    }

    if ($response === null || !$response->success) {
      $form_state->setError($element, t('CAPTCHA invalid. Please try again.'));
    }

  }


  /**
   * Prepares a #type 'recaptcha' render element for re-captcha.
   *
   * @param array $element
   *   An associative array containing the properties of the element.
   *   Properties used: #title, #value, #description, #size, #maxlength,
   *   #placeholder, #required, #attributes.
   *
   * @return array
   *   The $element with prepared variables ready for input.html.twig.
   */
  public static function preRenderEmail($element) {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $config = \Drupal::config('recaptcha.settings');

    $element['#attached']['library'][] = 'google_recaptcha/recaptcha-api';
    $element['#public_key'] = $config->get('recaptcha_public_key');

    return $element;
  }

}
