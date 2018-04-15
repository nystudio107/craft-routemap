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

use nystudio107\routemap\RouteMap;

use craft\base\ElementInterface;
use craft\web\Controller;

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

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = [
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
     *
     * @param array    $criteria
     * @param int|null $siteId
     *
     * @return Response
     */
    public function actionGetAllUrls(array $criteria = [], $siteId = null): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getAllUrls($criteria, $siteId));
    }

    /**
     * Return the public URLs for a section
     *
     * @param string   $section
     * @param array    $criteria
     * @param int|null $siteId
     *
     * @return Response
     */
    public function actionGetSectionUrls(string $section, array $criteria = [], $siteId = null): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getSectionUrls($section, $criteria, $siteId));
    }

    /**
     * Return the public URLs for a category
     *
     * @param string   $category
     * @param array    $criteria
     * @param int|null $siteId
     *
     * @return Response
     */
    public function actionGetCategoryUrls(string $category, array $criteria = [], $siteId = null): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getCategoryUrls($category, $criteria, $siteId));
    }

    /**
     * Return all of the section route rules
     *
     * @param string $format 'Craft'|'React'|'Vue'
     * @param int|null $siteId
     *
     * @return Response
     */
    public function actionGetAllRouteRules(string $format = 'Craft', $siteId = null): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getAllRouteRules($format, $siteId));
    }

    /**
     * Return the route rules for a specific section
     *
     * @param string $section
     * @param string $format 'Craft'|'React'|'Vue'
     * @param int|null $siteId
     *
     * @return Response
     */
    public function actionGetSectionRouteRules(string $section, string $format = 'Craft', $siteId = null): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getSectionRouteRules($section, $format, $siteId));
    }

    /**
     * Return the route rules for a specific category
     *
     * @param string   $category
     * @param string   $format 'Craft'|'React'|'Vue'
     * @param int|null $siteId
     *
     * @return Response
     */
    public function actionGetCategoryRouteRules(string $category, string $format = 'Craft', $siteId = null): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getCategoryRouteRules($category, $format, $siteId));
    }

    /**
     * Return the Craft AdminCP and `routes.php` rules
     *
     * @param int|null $siteId
     * @param bool     $includeGlobal
     *
     * @return Response
     */
    public function actionGetRouteRules($siteId = null, $includeGlobal = true): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getRouteRules($siteId, $includeGlobal));
    }

    /**
     * Get all of the assets of the type $assetTypes that are used in the Entry
     * that matches the $url
     *
     * @param string   $url
     * @param array    $assetTypes
     * @param int|null $siteId
     *
     * @return Response
     */
    public function actionGetUrlAssetUrls($url, array $assetTypes = ['image'], $siteId = null): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getUrlAssetUrls($url, $assetTypes, $siteId));
    }

    /**
     * Returns all of the URLs for the given $elementType based on the passed in
     * $criteria and $siteId
     *
     * @var string|ElementInterface $elementType
     * @var array                   $criteria
     * @var int|null                $siteId
     *
     * @return Response
     */
    public function actionGetElementUrls($elementType, array $criteria = [], $siteId = null): Response
    {
        return $this->asJson(RouteMap::$plugin->routes->getElementUrls($elementType, $criteria, $siteId));
    }
}
