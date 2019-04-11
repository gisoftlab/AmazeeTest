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
 *   id = "mathematical_inline_formatter",
 *   label = @Translation("Mathematical Inline Formatter"),
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
class MathematicalInlineFormatter extends FormatterBase {
    /**
     * {@inheritdoc}
     */
    public function viewElements(FieldItemListInterface $items, $langcode) {

        /**
         * @var $math MathematicalService
         */
        $math = \Drupal::service('mathematical_service.compute');

        $elements = [];
        // The ProcessedText element already handles cache context & tag bubbling.
        // @see \Drupal\filter\Element\ProcessedText::preRenderText()
        /**
         * @var $items[] FieldItemList
         */
        foreach ($items as $delta => $item) {
            $elements[$delta] = [
                '#type' => 'processed_text',
                '#text' => $math->parse([$item->value]),
                '#format' => $item->format,
                '#langcode' => $item
                    ->getLangcode(),
            ];
        }
        return $elements;
    }
}
