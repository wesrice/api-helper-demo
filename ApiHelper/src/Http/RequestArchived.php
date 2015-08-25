<?php

namespace ApiHelper\Http;

use Craft\Craft;
use Craft\BaseElementModel;
use ApiHelper\Validation\Validators\AbstractValidator;

class RequestArchived
{
    /**
     * Type
     *
     * @var string
     */
    protected $type;

    /**
     * Route Parameters
     *
     * @var array
     */
    protected $route_params = [];

    /**
     * Parameters
     *
     * @var array
     */
    protected $params = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Set the request type
        $this->setType($_SERVER['REQUEST_METHOD']);

        // Set the route params
        $variables = \Craft\craft()->urlManager->getRouteParams()['variables'];
        unset($variables['matches']);
        $this->setRouteParams($variables);

        // Set the params
        $this->setDefaultParams();
    }

    /**
     * Set Type
     *
     * @param string $type Type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get Type
     *
     * @param string $type Type
     *
     * @return string Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set Route Parameters
     *
     * @param array $route_params Route Parameters
     *
     * @return Request Request
     */
    public function setRouteParams(array $params) {
        $this->route_params = $params;

        return $this;
    }

    /**
     * Get Route Parameters
     *
     * @return array Route Parameters
     */
    public function getRouteParams()
    {
        return $this->route_params;
    }

    /**
     * Get Route Parameter
     *
     * @param string $route_param Route Parameter
     *
     * @return string Route Parameter
     */
    public function getRouteParam($route_param)
    {
        return isset($this->route_params[$route_param]) ? $this->route_params[$route_param] : null;
    }

    /**
     * Set Parameters
     *
     * @param array $params Parameters
     *
     * @return Request Request
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get Parameters
     *
     * @return array Parameters
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set Default Params
     *
     * @return void
     */
    public function setDefaultParams()
    {
        $params = [];

        $sources = [];

        $sources[] = \Craft\craft()->request->getQuery();

        $sources[] = \Craft\craft()->request->getPost();

        $sources[] = \Craft\craft()->request->getRestParams();

        foreach ($sources as $source) {
            $params = array_merge($params, $source);
        }

        $this->setParams($params);
    }

    /**
     * Get Param
     *
     * @param string $param Param
     *
     * @return mixed Param
     */
    public function getParam($param)
    {
        return isset($this->params[$param]) ? $this->params[$param] : null;
    }

}
