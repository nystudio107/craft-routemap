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
     *
     * @return mixed[]
     */
    public static function fieldsOfType(ElementInterface $element, string $fieldType): array
    {
        $foundFields = [];

        $layout = $element->getFieldLayout();
        if (!$layout instanceof \craft\models\FieldLayout) {
            return [];
        }

        $fields = $layout->getCustomFields();
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
     *
     * @return string[]|null[]
     */
    public static function matrixFieldsOfType(MatrixBlock $matrixBlock, string $fieldType): array
    {
        $foundFields = [];

        try {
            $matrixBlockTypeModel = $matrixBlock->getType();
        } catch (InvalidConfigException) {
            $matrixBlockTypeModel = null;
        }

        if ($matrixBlockTypeModel !== null) {
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
