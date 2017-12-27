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
        return RouteMap::$plugin->routeMap->getAllUrls($criteria, $siteId);
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
        return RouteMap::$plugin->routeMap->getSectionUrls($section, $criteria, $siteId);
    }

    /**
     * Return all of the section route rules
     *
     * @param string $format 'Craft'|'React'|'Vue'
     *
     * @return array
     */
    public function getAllRouteRules(string $format = 'Craft'): array
    {
        return RouteMap::$plugin->routeMap->getAllRouteRules($format);
    }

    /**
     * Return the route rules for a specific section
     *
     * @param string $section
     * @param string $format 'Craft'|'React'|'Vue'
     *
     * @return array
     */
    public function getSectionRouteRules(string $section, string $format = 'Craft'): array
    {
        return RouteMap::$plugin->routeMap->getSectionRouteRules($section, $format);
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
        return RouteMap::$plugin->routeMap->getUrlAssetUrls($url, $assetTypes, $siteId);
    }
}
