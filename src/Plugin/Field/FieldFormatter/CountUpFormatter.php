<?php

namespace Drupal\countup_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\NumericFormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'CountUpFormatter' formatter.
 *
 * @FieldFormatter(
 *   id = "countup_formatter_countupformatter",
 *   label = @Translation("CountUp"),
 *   field_types = {
 *     "integer",
 *     "decimal",
 *     "float"
 *   }
 * )
 */
class CountUpFormatter extends NumericFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'thousand_separator' => '',
      'decimal_separator' => '.',
      'scale' => 2,
      'prefix_suffix' => TRUE,
      'start_val' => 0,
      'duration' => 2,
      'prefix' => '',
      'suffix' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $elements = parent::settingsForm($form, $form_state);

    $elements['decimal_separator'] = [
      '#type' => 'select',
      '#title' => t('Decimal marker'),
      '#options' => ['.' => t('Decimal point'), ',' => t('Comma')],
      '#default_value' => $this->getSetting('decimal_separator'),
      '#weight' => 5,
    ];
    $elements['scale'] = [
      '#type' => 'number',
      '#title' => t('Scale', [], ['context' => 'decimal places']),
      '#min' => 0,
      '#max' => 10,
      '#step' => 1,
      '#default_value' => $this->getSetting('scale'),
      '#description' => t('The number of digits to the right of the decimal.'),
      '#weight' => 6,
    ];
    $elements['start_val'] = [
      '#type' => 'number',
      '#title' => t('Start value'),
      '#default_value' => $this->getSetting('start_val'),
      '#description' => t('Number to start at.'),
      '#weight' => 7,
      '#size' => 6,
      '#step' => 'any',
      '#required' => TRUE,
    ];
    $elements['duration'] = [
      '#type' => 'number',
      '#title' => t('Duration'),
      '#default_value' => $this->getSetting('duration'),
      '#description' => t('animation duration.'),
      '#field_suffix' => t('Seconds'),
      '#min' => 0,
      '#step' => .1,
      '#weight' => 8,
    ];
    $element['prefix'] = [
      '#type' => 'textfield',
      '#title' => t('Prefix'),
      '#default_value' => $this->getSetting('prefix'),
      '#size' => 10,
      '#description' => t("Define a string that should be prefixed to the value, like '$ ' or '&euro; '. Leave blank for none."),
    ];
    $element['suffix'] = [
      '#type' => 'textfield',
      '#title' => t('Suffix'),
      '#default_value' => $this->getSetting('suffix'),
      '#size' => 10,
      '#description' => t("Define a string that should be suffixed to the value, like ' m', ' kb/s'. Leave blank for none."),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  protected function numberFormat($number) {
    return number_format($number, $this->getSetting('scale'), $this->getSetting('decimal_separator'), $this->getSetting('thousand_separator'));
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $settings = $this->getFieldSettings();

    foreach ($items as $delta => $item) {
      $output = $this->numberFormat($item->value);

      // Account for prefix and suffix.
      if ($this->getSetting('prefix_suffix')) {
        $prefixes = isset($settings['prefix']) ? array_map([
          'Drupal\Core\Field\FieldFilteredMarkup',
          'create',
        ], explode('|', $settings['prefix'])) : [''];
        $suffixes = isset($settings['suffix']) ? array_map([
          'Drupal\Core\Field\FieldFilteredMarkup',
          'create',
        ], explode('|', $settings['suffix'])) : [''];
        $prefix = (count($prefixes) > 1) ? $this->formatPlural($item->value, $prefixes[0], $prefixes[1]) : $prefixes[0];
        $suffix = (count($suffixes) > 1) ? $this->formatPlural($item->value, $suffixes[0], $suffixes[1]) : $suffixes[0];
      }
      // Output the raw value in a content attribute if the text of the HTML
      // element differs from the raw value (for example when a prefix is used).
      if (isset($item->_attributes) && $item->value != $output) {
        $item->_attributes += ['content' => $item->value];
      }

      $elements[$delta] = [
        '#markup' => $output,
        '#field_prefix' => isset($settings['prefix']) ? $prefix : '',
        '#field_suffix' => isset($settings['suffix']) ? $suffix : '',
        '#attributes' => [
          'class' => [
            'countup-formatter',
          ],
          'data-enableScrollSpy' => TRUE,
          'data-startVal' => $this->getSetting('start_val'),
          'data-endVal' => $output,
          'data-duration' => $this->getSetting('duration'),
          'data-prefix' => $this->getSetting('prefix'),
          'data-suffix' => $this->getSetting('suffix'),
          'data-decimal' => $this->getSetting('decimal_separator'),
          'data-separator' => $this->getSetting('thousand_separator'),
          'data-useGrouping' => $this->getSetting('thousand_separator') ? TRUE : FALSE,
          'data-decimalPlaces' => $this->getSetting('scale'),
        ],
        '#attached' => [
          'library' => [
            'countup_formatter/countup',
          ],
        ],
      ];
    }

    return $elements;
  }

}
