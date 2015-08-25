<?php

namespace Craft;

class ApiHelperPlugin extends BasePlugin
{
    /**
     * Get Name
     *
     * @return string Name
     */
    public function getName()
    {
         return Craft::t('Api Helper');
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
        $this->autoload_files();
    }

    /**
     * Autoload Files
     *
     * @return void
     */
    public function autoload_files()
    {
        $autoload = craft()->config->get('autoload', 'apiHelper');

        if ($autoload['transformers']) {
            Craft::import('plugins.*.transformers.*', true);
        } else {
            Craft::import('plugins.apihelper.transformers.*', true);
        }

        if ($autoload['validators']) {
            Craft::import('plugins.*.validators.*', true);
        } else {
            Craft::import('plugins.apihelper.validators.*', true);
        }

        Craft::import('plugins.apihelper.vendor.autoload', true);
    }
}
