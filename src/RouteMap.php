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
