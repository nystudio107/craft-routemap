<?php
/**
 * Route Map plugin for Craft CMS 3.x
 *
 * Returns a list of public routes for elements with URLs
 *
 * @link      https://nystudio107.com/
 * @copyright Copyright (c) 2017 nystudio107
 */

namespace nystudio107\routemap\controllers;

use craft\base\ElementInterface;
use craft\web\Controller;
use nystudio107\routemap\RouteMap;
use yii\web\Response;

/**
 * @author    nystudio107
 * @package   RouteMap
 * @since     1.0.0
 */
class RoutesController extends Controller
{
    // Protected Properties
    // =========================================================================

    protected array|bool|int $allowAnonymous = [
        'get-all-urls',
        'get-section-urls',
        'get-all-route-rules',
        'get-section-route-rules',
        'get-url-asset-urls',
        'get-element-urls',
    ];

    // Public Methods
    // =========================================================================
    /**
     * Return the public URLs for all elements that have URLs
     */
    public function actionGetAllUrls(array $criteria = [], ?int $siteId = null): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getAllUrls($criteria, $siteId));
    }

    /**
     * Return the public URLs for a section
     */
    public function actionGetSectionUrls(string $section, array $criteria = [], ?int $siteId = null): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getSectionUrls($section, $criteria, $siteId));
    }

    /**
     * Return the public URLs for a category
     */
    public function actionGetCategoryUrls(string $category, array $criteria = [], ?int $siteId = null): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getCategoryUrls($category, $criteria, $siteId));
    }

    /**
     * Return all the section route rules
     *
     * @param string $format 'Craft'|'React'|'Vue'
     * @param ?int $siteId
     * @return Response
     */
    public function actionGetAllRouteRules(string $format = 'Craft', ?int $siteId = null): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getAllRouteRules($format, $siteId));
    }

    /**
     * Return the route rules for a specific section
     *
     * @param string $section
     * @param string $format 'Craft'|'React'|'Vue'
     * @param ?int $siteId
     * @return Response
     */
    public function actionGetSectionRouteRules(string $section, string $format = 'Craft', ?int $siteId = null): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getSectionRouteRules($section, $format, $siteId));
    }

    /**
     * Return the route rules for a specific category
     *
     * @param string $category
     * @param string $format 'Craft'|'React'|'Vue'
     * @param ?int $siteId
     * @return Response
     */
    public function actionGetCategoryRouteRules(string $category, string $format = 'Craft', ?int $siteId = null): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getCategoryRouteRules($category, $format, $siteId));
    }

    /**
     * Return the Craft Control Panel and `routes.php` rules
     *
     * @param ?int $siteId
     * @param bool $includeGlobal
     * @return Response
     */
    public function actionGetRouteRules(?int $siteId = null, bool $includeGlobal = true): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getRouteRules($siteId, $includeGlobal));
    }

    /**
     * Get all the assets of the type $assetTypes that are used in the Entry
     * that matches the $url
     *
     * @param string $url
     * @param array $assetTypes
     * @param ?int $siteId
     * @return Response
     */
    public function actionGetUrlAssetUrls(string $url, array $assetTypes = ['image'], ?int $siteId = null): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getUrlAssetUrls($url, $assetTypes, $siteId));
    }

    /**
     * Returns all of the URLs for the given $elementType based on the passed in
     * $criteria and $siteId
     *
     * @param string|ElementInterface $elementType
     * @param array $criteria
     * @param ?int $siteId
     * @return Response
     */
    public function actionGetElementUrls(string|ElementInterface $elementType, array $criteria = [], ?int $siteId = null): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getElementUrls($elementType, $criteria, $siteId));
    }
}
