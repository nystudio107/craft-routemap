<?php
/**
 * Route Map plugin for Craft CMS 3.x
 *
 * Returns a list of public routes for elements with URLs
 *
 * @link      https://nystudio107.com/
 * @copyright Copyright (c) 2017 nystudio107
 */

namespace nystudio107\routemap\helpers;

use craft\base\Component;
use craft\base\ElementInterface;
use craft\base\Field as BaseField;
use craft\elements\MatrixBlock;
use yii\base\InvalidConfigException;

/**
 * @author    nystudio107
 * @package   RouteMap
 * @since     1.0.0
 */
class Field extends Component
{
    // Static Methods
    // =========================================================================

    /**
     * Return all of the fields in the $element of the type $fieldType class
     *
     * @param ElementInterface $element
     * @param string  $fieldType
     *
     * @return array
     */
    public static function fieldsOfType(ElementInterface $element, string $fieldType)
    {
        $foundFields = [];

        $layout = $element->getFieldLayout();
        $fields = $layout->getFields();
        /** @var  $field BaseField */
        foreach ($fields as $field) {
            if ($field instanceof $fieldType) {
                $foundFields[] = $field->handle;
            }
        }

        return $foundFields;
    }

    /**
     * Return all of the fields in the $matrixBlock of the type $fieldType class
     *
     * @param MatrixBlock $matrixBlock
     * @param string      $fieldType
     *
     * @return array
     */
    public static function matrixFieldsOfType(MatrixBlock $matrixBlock, string $fieldType)
    {
        $foundFields = [];

        try {
            $matrixBlockTypeModel = $matrixBlock->getType();
        } catch (InvalidConfigException $e) {
            $matrixBlockTypeModel = null;
        }
        if ($matrixBlockTypeModel) {
            $fields = $matrixBlockTypeModel->getFields();
            /** @var  $field BaseField */
            foreach ($fields as $field) {
                if ($field instanceof $fieldType) {
                    $foundFields[] = $field->handle;
                }
            }
        }

        return $foundFields;
    }
}
