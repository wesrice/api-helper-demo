<?php

namespace ApiHelper\Http;

use Craft\UrlManager;
use Craft\UserModel;
use Craft\BaseController;
use ApiHelper\Validation\Exceptions\ApiHelperException as Exception;

class Dispatcher
{
    /**
     * Router
     *
     * @var Craft\UrlManager
     */
    protected $router;

    /**
     * Request
     *
     * @var ApiHelper\Request
     */
    protected $request;

    /**
     * Config
     *
     * @var array
     */
    protected $config;

    /**
     * User
     *
     * @var Craft\UserModel
     */
    protected $user;

    /**
     * User Permissions
     *
     * @var array
     */
    protected $user_permissions;

    /**
     * Element Types
     *
     * @var array
     */
    protected $element_types;

    /**
     * Constructor
     */
    public function __construct(UrlManager $router)
    {
        $this->router           = $router;
        $this->config           = \Craft\craft()->config->get('supportedElementTypes', 'apiHelper');
        $this->user             = \Craft\craft()->userSession->getUser();
        $this->element_types    = \Craft\craft()->elements->getAllElementTypes();
        $this->user_permissions = $this->getUserPermissions($this->user);
    }

    /**
     * Get Route Parameters
     *
     * @return array Route Parameters
     */
    protected function getRouteParameters()
    {
        $attributes = $this->router->getRouteParams();
        unset($attributes['variables']['matches']);

        return $attributes['variables'];
    }

    /**
     * Get User Permissions
     *
     * @param UserModel $user User
     *
     * @return array User Permissions
     */
    protected function getUserPermissions(UserModel $user = null)
    {
        $permissions = [];

        foreach($this->element_types as $element_type) {

            $element_handle = $element_type->getClassHandle();

            $permissions[$element_handle] = [];

            if (isset($this->config[$element_handle])) {

                if (isset($this->config[$element_handle]['public'])) {
                    $permissions[$element_handle] = array_merge($permissions[$element_handle], $this->config[$element_handle]['public']);
                }

                if (isset($this->config[$element_handle]['authenticated']) && $user) {

                    if (isset($this->config[$element_handle]['authenticated']['users'])) {
                        $authenticated_users = $this->config[$element_handle]['authenticated']['users'];

                        if (isset($authenticated_users[$user->username])) {
                            $permissions[$element_handle] = array_merge($permissions[$element_handle], $authenticated_users[$user->username]);
                        }
                    }

                    if (isset($this->config[$element_handle]['authenticated']['groups'])) {
                        $authenticated_groups = $this->config[$element_handle]['authenticated']['groups'];
                    }
                }

            } else {

                if (isset($this->config['*']['public'])) {
                    $permissions[$element_handle] = array_merge($permissions[$element_handle], $this->config['*']['public']);
                }

                if (isset($this->config[$element_handle]['authenticated']) && $user) {
                    $permissions[$element_handle] = array_merge($permissions[$element_handle], $this->config['*']['authenticated']);
                }

            }
        }

        foreach ($permissions as $key => $element_type) {
            $permissions[$key] = array_unique($element_type);
        }

        return $permissions;
    }

    /**
     * Validate Route
     *
     * @param Request $request Request
     *
     * @return void
     */
    public function validateRoute(Request $request)
    {
        $this->validateElementType($request->getAttribute('elementType'), array_keys($this->element_types));

        if (!empty($request->getAttribute('elementId'))) {
            $this->validateElement($request->getAttribute('elementId'), $request->getAttribute('elementType'));
        }

        $this->validateUserPermission($request->getMethod(), $request->getAttribute('elementType'));
    }

    /**
     * Validate Element Type
     *
     * @param string $element_type  Element Type
     * @param array  $element_types Element Types
     *
     * @return void
     */
    protected function validateElementType($element_type, $element_types)
    {
        if (!in_array($element_type, $element_types)) {
            throw new Exception(sprintf('`%s` is not a valid element type.', $element_type));
        }
    }

    /**
     * Validate Element
     *
     * @param string $element_id   Element Id
     * @param string $element_type Element Type
     *
     * @return void
     */
    protected function validateElement($element_id, $element_type)
    {
        $element = \Craft\craft()->elements->getElementById($element_id);

        if (!$element) {
            $exception = new Exception();
            $exception
                ->setStatus(404)
                ->setMessage(sprintf('An element with an id of `%s` was not found.', $element_id));

            throw $exception;
        }

        if ($element->getElementType() !== $element_type) {
            $exception = new Exception();
            $exception
                ->setStatus(404)
                ->setMessage(sprintf('An element with an id of `%s` was found, but the element type is `%s`, not `%s`.', $element_id, $element->getElementType(), $element_type));

            throw $exception;
        }
    }

    /**
     * Validate User Permission
     *
     * @param string $method       Method
     * @param string $element_type Element Type
     *
     * @return void
     */
    protected function validateUserPermission($method, $element_type)
    {
        if (!in_array($method, $this->user_permissions[$element_type])) {
            $exception = new Exception();
            $exception
                ->setStatus(401)
                ->setMessage(sprintf('User `%s` is not authorized to perform method `%s` on `%s` element type.', $this->user->username, $method, $element_type));

            throw $exception;
        }
    }

    /**
     * Handle
     *
     * @param Request $request Request
     *
     * @return void
     */
    public function handle(BaseController $class, array $variables)
    {
        $request = $class->getRequest();

        $this->validateRoute($request);

        if ($request->getAttribute('action')) {
            $method = 'action'.ucwords($request->getAttribute('action'));

            if (!method_exists($class, $method)) {
                $exception = new Exception();
                $exception
                    ->setStatus(404)
                    ->setMessage(sprintf('`%s` method of `%s` was not found.', $method, get_class($class)));

                throw $exception;
            }

            return $class->$method($variables);
        }

        if ($request->getMethod() === 'GET' && !$request->getAttribute('elementId')) {
            return $class->actionIndex($variables);
        }

        if ($request->getMethod() === 'GET' && $request->getAttribute('elementId')) {
            return $class->actionShow($variables);
        }

        if ($request->getMethod() === 'POST' && !$request->getAttribute('elementId')) {
            return $class->actionStore($variables);
        }

        if ($request->getMethod() === 'PUT' && $request->getAttribute('elementId')) {
            return $class->actionUpdate($variables);
        }

        if ($request->getMethod() === 'DELETE' && $request->getAttribute('elementId')) {
            return $class->actionDelete($variables);
        }

        $exception = new Exception();
        $exception
            ->setStatus(404)
            ->setMessage(sprintf('`%s` method of `%s` was not found.', $method, get_class($class)));

        throw $exception;
    }
}
