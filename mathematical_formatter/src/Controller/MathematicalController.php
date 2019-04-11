<?php

namespace Drupal\mathematical_formatter\Controller;

use Drupal\mathematical_formatter\Services\MathematicalService;
use Drupal\mathematical_formatter\Utility\DescriptionTemplateTrait;

/**
 * Controller for field example description page.
 *
 * This class uses the DescriptionTemplateTrait to display text we put in the
 * templates/description.html.twig file.
 */
class MathematicalController
{

    use DescriptionTemplateTrait;

    /**
     * Generate a render array with our templated content.
     *
     * @return array
     *   A render array.
     */
    public function description()
    {
        /**
         * @var $math MathematicalService
         */
        $math = \Drupal::service('mathematical_service.compute');
        $example =  '2+3*4/2-2';

        $template_path = $this->getDescriptionTemplatePath();
        $template = file_get_contents($template_path);
        $build = [
            'description' => [
                '#type' => 'inline_template',
                '#template' => $template,
                '#context' => [
                    'module' => $this->getModuleName(),
                    'formula' => $example,
                    'compute' => $math->parse([$example]),
                ],
                '#attached' => array(
                    'library' =>  array(
                        'mathematical_formatter/react-js',
                        'mathematical_formatter/react-app',
                    ),
                ),
            ],
        ];

        return $build;
    }

    /**
     * {@inheritdoc}
     */
    protected function getModuleName()
    {
        return 'mathematical_formatter';
    }

}
