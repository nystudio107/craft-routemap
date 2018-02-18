<?php
/**
 * Route Map plugin for Craft CMS 3.x
 *
 * Returns a list of public routes for elements with URLs
 *
 * @link      https://nystudio107.com/
 * @copyright Copyright (c) 2017 nystudio107
 */

namespace nystudio107\routemap\variables;

use nystudio107\routemap\RouteMap;

use craft\base\ElementInterface;

/**
 * @author    nystudio107
 * @package   RouteMap
 * @since     1.0.0
 */
class RouteMapVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Return the public URLs for all elements that have URLs
     *
     * @param array    $criteria
     * @param int|null $siteId
     *
     * @return array
     */
    public function getAllUrls($criteria = [], $siteId = null)
    {
        return RouteMap::$plugin->routes->getAllUrls($criteria, $siteId);
    }

    /**
     * Return all of the section and category route rules
     *
     * @param string $format 'Craft'|'React'|'Vue'
     * @param int|null $siteId
     *
     * @return array
     */
    public function getAllRouteRules(string $format = 'Craft', $siteId = null): array
    {
        return RouteMap::$plugin->routes->getAllRouteRules($format, $siteId);
    }


    /**
     * Return the public URLs for a section
     *
     * @param string   $section
     * @param array    $criteria
     * @param int|null $siteId
     *
     * @return array
     */
    public function getSectionUrls(string $section, $criteria = [], $siteId = null)
    {
        return RouteMap::$plugin->routes->getSectionUrls($section, $criteria, $siteId);
    }


    /**
     * Return all of the section route rules
     *
     * @param string $format 'Craft'|'React'|'Vue'
     * @param int|null $siteId
     *
     * @return array
     */
    public function getAllSectionRouteRules(string $format = 'Craft', $siteId = null): array
    {
        return RouteMap::$plugin->routes->getAllSectionRouteRules($format, $siteId);
    }

    /**
     * Return the route rules for a specific section
     *
     * @param string $section
     * @param string $format 'Craft'|'React'|'Vue'
     * @param int|null $siteId
     *
     * @return array
     */
    public function getSectionRouteRules(string $section, string $format = 'Craft', $siteId = null): array
    {
        return RouteMap::$plugin->routes->getSectionRouteRules($section, $format, $siteId);
    }

    /**
     * Return the public URLs for a category group
     *
     * @param string   $category
     * @param array    $criteria
     * @param int|null $siteId
     *
     * @return array
     */
    public function getCategoryUrls(string $category, $criteria = [], $siteId = null)
    {
        return RouteMap::$plugin->routes->getCategoryUrls($category, $criteria, $siteId);
    }

    /**
     * Return all of the cateogry group route rules
     *
     * @param string $format 'Craft'|'React'|'Vue'
     * @param int|null $siteId
     *
     * @return array
     */
    public function getAllCategoryRouteRules(string $format = 'Craft', $siteId = null): array
    {
        return RouteMap::$plugin->routes->getAllCategoryRouteRules($format, $siteId);
    }

    /**
     * Return the route rules for a specific category
     *
     * @param string $category
     * @param string $format 'Craft'|'React'|'Vue'
     * @param int|null $siteId
     *
     * @return array
     */
    public function getCategoryRouteRules(string $category, string $format = 'Craft', $siteId = null): array
    {
        return RouteMap::$plugin->routes->getCategoryRouteRules($category, $format, $siteId);
    }


    /**
     * Get all of the assets of the type $assetTypes that are used in the Entry
     * that matches the $url
     *
     * @param string   $url
     * @param array    $assetTypes
     * @param int|null $siteId
     *
     * @return array
     */
    public function getUrlAssetUrls($url, $assetTypes = ['image'], $siteId = null)
    {
        return RouteMap::$plugin->routes->getUrlAssetUrls($url, $assetTypes, $siteId);
    }

    /**
     * Returns all of the URLs for the given $elementType based on the passed in
     * $criteria and $siteId
     *
     * @var string|ElementInterface $elementType
     * @var array                   $criteria
     * @var int|null                $siteId
     *
     * @return array
     */
    public function getElementUrls($elementType, $criteria = [], $siteId = null)
    {
        return RouteMap::$plugin->routes->getElementUrls($elementType, $criteria, $siteId);
    }
}
