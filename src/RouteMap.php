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

use Craft;
use craft\base\Element;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\events\ElementEvent;
use craft\events\RegisterCacheOptionsEvent;
use craft\services\Elements;
use craft\utilities\ClearCaches;
use craft\web\twig\variables\CraftVariable;
use nystudio107\routemap\services\Routes as RoutesService;
use nystudio107\routemap\variables\RouteMapVariable;
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
    // Public Static Properties
    // =========================================================================

    /**
     * @var ?RouteMap
     */
    public static ?RouteMap $plugin = null;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    /**
     * @var bool
     */
    public bool $hasCpSection = false;

    /**
     * @var bool
     */
    public bool $hasCpSettings = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        $config['components'] = [
            'routes' => RoutesService::class,
        ];

        parent::__construct($id, $parent, $config);
    }

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            static function (Event $event): void {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('routeMap', RouteMapVariable::class);
            }
        );

        // Handler: Elements::EVENT_AFTER_SAVE_ELEMENT
        Event::on(
            Elements::class,
            Elements::EVENT_AFTER_SAVE_ELEMENT,
            static function (ElementEvent $event): void {
                Craft::debug(
                    'Elements::EVENT_AFTER_SAVE_ELEMENT',
                    __METHOD__
                );
                /** @var Element $element */
                $element = $event->element;
                $bustCache = true;
                // Only bust the cache if the element is ENABLED or LIVE
                if (($element->getStatus() !== Element::STATUS_ENABLED)
                    && ($element->getStatus() !== Entry::STATUS_LIVE)
                ) {
                    $bustCache = false;
                }

                if ($bustCache) {
                    Craft::debug(
                        'Cache busted due to saving: ' . $element::class . ' - ' . $element->title,
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
            static function (RegisterCacheOptionsEvent $event): void {
                $event->options[] = [
                    'key' => 'route-map',
                    'label' => Craft::t('route-map', 'Route Map Cache'),
                    'action' => function (): void {
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
