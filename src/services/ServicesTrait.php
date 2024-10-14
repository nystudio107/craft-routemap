<?php
/**
 * Route Map plugin for Craft CMS 3.x
 *
 * Returns a list of public routes for elements with URLs
 *
 * @link      https://nystudio107.com/
 * @copyright Copyright (c) nystudio107
 */

namespace nystudio107\routemap\services;

use craft\helpers\ArrayHelper;
use nystudio107\routemap\services\Routes as RoutesService;
use yii\base\InvalidConfigException;

/**
 * @author    nystudio107
 * @package   RouteMap
 * @since     1.1.9
 *
 * @property RoutesService $routes
 */
trait ServicesTrait
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        // Merge in the passed config, so it our config can be overridden by Plugins::pluginConfigs['vite']
        // ref: https://github.com/craftcms/cms/issues/1989
        $config = ArrayHelper::merge([
            'components' => [
                'routes' => RoutesService::class,
            ],
        ], $config);

        parent::__construct($id, $parent, $config);
    }

    /**
     * Returns the routes service
     *
     * @return RoutesService The routes service
     * @throws InvalidConfigException
     */
    public function getRoutes(): RoutesService
    {
        return $this->get('routes');
    }
}
