<?php

namespace Craft;

use ApiHelper\Http\Router;
use ApiHelper\Http\Dispatcher;
use ApiHelper\Http\Request;
use ApiHelper\Http\Response;
use ApiHelper\Validation\Exceptions\ApiHelperException as Exception;

class ApiHelper_BaseController extends BaseController
{
    /**
     * Allow Anonymous
     *
     * @var boolean
     */
    protected $allowAnonymous = true;

    /**
     * Router
     *
     * @var ApiHelper\Http\Router
     */
    protected $router;

    /**
     * Dispatcher
     *
     * @var ApiHelper\Http\Dispatcher
     */
    protected $dispatcher;

    /**
     * Request
     *
     * @var ApiHelper\Http\Request
     */
    protected $request;

    /**
     * Response
     *
     * @var ApiHelper\Http\Response
     */
    protected $response;

    /**
     * Constructor
     */
    public function __construct()
    {
        try {
            $this->router     = \Craft\craft()->urlManager;
            $this->dispatcher = new Dispatcher($this->router);
            $this->request    = new Request();
            $this->response   = new Response($this->request);


        } catch (Exception $exception) {
            $response = new Response();

            $response
                ->setStatus($exception->getStatusCode(), $exception->getStatusPhrase())
                ->setError($exception)
                ->send();
        }
    }

    /**
     * Get Request
     *
     * @return Request Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Resource Router
     *
     * @param array $variables Variables
     *
     * @return void
     */
    public function actionResourceRouter(array $variables = [])
    {
        try {
            $this->dispatcher->handle($this, $variables);

            $this->response->send();
        } catch (Exception $exception) {
            $exception->setInput($this->request->getParsedBody());

            $response = new Response();

            $response
                ->setStatus($exception->getStatusCode(), $exception->getStatusPhrase())
                ->setError($exception)
                ->send();
        }


    }
}
