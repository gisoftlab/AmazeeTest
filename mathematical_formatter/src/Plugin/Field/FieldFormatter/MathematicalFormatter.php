<?php
/**
 * @file
 * Contains Drupal\mathematical_formatter\Plugin\Field\FieldFormatter\MathematicalFormatter.
 */
namespace Drupal\mathematical_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Annotation\FieldFormatter;
/**
 * Plugin implementation of the 'mathematical_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "mathematical_formatter",
 *   label = @Translation("Mathematical Formatter"),
 *   weight = "11",
 *   field_types = {
 *     "string",
 *     "text",
 *   },
 *   quickedit = {
 *     "editor" = "plain_text"
 *   }
 * )
 */
class MathematicalFormatter extends FormatterBase {
    /**
     * {@inheritdoc}
     */
    public function viewElements(FieldItemListInterface $items, $langcode) {
        $elements = [];
        /**
         * @var $math MathematicalService
         */
        $math = \Drupal::service('mathematical_service.compute');

        foreach ($items as $delta => $item) {
            $elements[$delta] = [
                // See theme_html_tag().
                '#type' => 'html_tag',
                '#tag' => 'span',
                '#attributes' => [
                    'class' => 'mathematical_formatter',
                    'compute' => $math->parse([$item->value]),
                    'formula' => $item->value,
                ],
                '#value' => $this->t('@formula', ['@formula' => $item->value]),
                '#attached' => array(
                    'library' =>  array(
                        'mathematical_formatter/react-js',
                        'mathematical_formatter/dynamic-compute',
                    ),
                ),
            ];
        }

        return $elements;
    }
}
