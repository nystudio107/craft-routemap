<?php
/**
 * Route Map plugin for Craft CMS
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
use craft\elements\Entry;
use craft\models\FieldLayout;
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
     * Return all the fields in the $element of the type $fieldType class
     *
     * @param ElementInterface $element
     * @param string $fieldType
     * @return array
     */
    public static function fieldsOfType(ElementInterface $element, string $fieldType): array
    {
        $foundFields = [];

        $layout = $element->getFieldLayout();
        if (!$layout instanceof FieldLayout) {
            return [];
        }

        $fields = $layout->getCustomFields();
        /** @var BaseField $field */
        foreach ($fields as $field) {
            if ($field instanceof $fieldType) {
                $foundFields[] = $field->handle;
            }
        }

        return $foundFields;
    }

    /**
     * Return all the fields in the $matrixBlock of the type $fieldType class
     *
     * @param Entry $matrixEntry
     * @param string $fieldType
     * @return ?array
     */
    public static function matrixFieldsOfType(Entry $matrixEntry, string $fieldType): ?array
    {
        $foundFields = [];

        try {
            $matrixEntryTypeModel = $matrixEntry->getType();
        } catch (InvalidConfigException $e) {
            $matrixEntryTypeModel = null;
        }
        if ($matrixEntryTypeModel) {
            $fields = $matrixEntryTypeModel->getCustomFields();
            /** @var BaseField $field */
            foreach ($fields as $field) {
                if ($field instanceof $fieldType) {
                    $foundFields[$field->handle] = $field->name;
                }
            }
        }

        return $foundFields;
    }
}
