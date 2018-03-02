<?php
/**
 * Route Map plugin for Craft CMS 3.x
 *
 * Returns a list of public routes for elements with URLs
 *
 * @link      https://nystudio107.com/
 * @copyright Copyright (c) 2017 nystudio107
 */

namespace nystudio107\routemap;

use nystudio107\routemap\services\Routes as RoutesService;
use nystudio107\routemap\variables\RouteMapVariable;

use Craft;
use craft\base\Plugin;
use craft\base\Element;
use craft\elements\Entry;
use craft\events\ElementEvent;
use craft\events\RegisterCacheOptionsEvent;
use craft\services\Elements;
use craft\utilities\ClearCaches;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

/**
 * Class RouteMap
 *
 * @author    nystudio107
 * @package   RouteMap
 * @since     1.0.0
 *
 * @property  RoutesService routes
 */
class RouteMap extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var RouteMap
     */
    public static $plugin;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('routeMap', RouteMapVariable::class);
            }
        );

        // Handler: Elements::EVENT_AFTER_SAVE_ELEMENT
        Event::on(
            Elements::class,
            Elements::EVENT_AFTER_SAVE_ELEMENT,
            function (ElementEvent $event) {
                Craft::debug(
                    'Elements::EVENT_AFTER_SAVE_ELEMENT',
                    __METHOD__
                );
                /** @var Element $element */
                $element = $event->element;
                $isNewElement = $event->isNew;
                $bustCache = true;
                // Only bust the cache if the element is ENABLED or LIVE
                if (($element->getStatus() != Element::STATUS_ENABLED)
                    && ($element->getStatus() != Entry::STATUS_LIVE)
                ) {
                    $bustCache = false;
                }
                if ($bustCache) {
                    Craft::debug(
                        "Cache busted due to saving: " . get_class($element) . " - " . $element->title,
                        __METHOD__
                    );
                    RouteMap::$plugin->routes->invalidateCache();
                }
            }
        );

        // Handler: ClearCaches::EVENT_REGISTER_CACHE_OPTIONS
        Event::on(
            ClearCaches::class,
            ClearCaches::EVENT_REGISTER_CACHE_OPTIONS,
            function (RegisterCacheOptionsEvent $event) {
                $event->options[] = [
                    'key' => 'route-map',
                    'label' => Craft::t('route-map', 'Route Map Cache'),
                    'action' => function () {
                        RouteMap::$plugin->routes->invalidateCache();
                    },
                ];
            }
        );

        Craft::info(
            Craft::t(
                'route-map',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================
}
