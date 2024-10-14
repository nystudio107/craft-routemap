<?php
/**
 * Route Map plugin for Craft CMS
 *
 * Returns a list of public routes for elements with URLs
 *
 * @link      https://nystudio107.com/
 * @copyright Copyright (c) 2017 nystudio107
 */

namespace nystudio107\routemap\services;

use nystudio107\routemap\services\Routes as RoutesService;
use yii\base\InvalidConfigException;

/**
 * @author    nystudio107
 * @package   routemap
 * @since     4.0.1
 *
 * @property  RoutesService $routes
 */
trait ServicesTrait
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function config(): array
    {
        return [
            'components' => [
                'routes' => RoutesService::class,
            ],
        ];
    }

    // Public Methods
    // =========================================================================

    /**
     * Returns the routes service
     *
     * @return RoutesService The helper service
     * @throws InvalidConfigException
     */
    public function getRoutes(): RoutesService
    {
        return $this->get('routes');
    }
}
