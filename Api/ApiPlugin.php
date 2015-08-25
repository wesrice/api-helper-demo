<?php

namespace Craft;

class ApiPlugin extends BasePlugin
{
    /**
     * Get Name
     *
     * @return string Name
     */
    public function getName()
    {
         return Craft::t('Api');
    }

    /**
     * Get Version
     *
     * @return string Version
     */
    public function getVersion()
    {
        return '0.0.0';
    }

    /**
     * Get Developer
     *
     * @return string Developer
     */
    public function getDeveloper()
    {
        return 'Airtype';
    }

    /**
     * Get Developer Url
     *
     * @return string Developer Url
     */
    public function getDeveloperUrl()
    {
        return 'http://airtype.com';
    }

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        //
    }

    /**
     * Register Site Routes
     *
     * @return array Site Routes
     */
    public function registerSiteRoutes()
    {
        $route_prefix = craft()->config->get('apiRoutePrefix', 'api');

        return [
            sprintf('%s/(?P<elementType>\w+)(/(?P<elementId>\d+)(/(?P<action>\w+))?)?', $route_prefix) => ['action' => 'api/resourceRouter'],
        ];
    }
}
