<?php

namespace Craft;

use ApiHelper\Http\Request;

class ApiController extends ApiHelper_BaseController
{
    /**
     * Action Show
     *
     * @param array $variables Variables
     *
     * @return Response Response
     */
    public function actionIndex(array $variables = [])
    {
        $elements = craft()->api->getElements($this->request);

        return $this->response->setPaginatedCollection($elements);
    }

    /**
     * Action Show
     *
     * @param array $variables Variables
     *
     * @return Response Response
     */
    public function actionShow(array $variables = [])
    {
        $element = craft()->api->getElement($this->request);

        return $this->response
            ->setTransformer(new EntryTransformer)
            ->setItem($element);
    }

    /**
     * Action Store
     *
     * @param array $variables Variables
     *
     * @return Response Response
     */
    public function actionStore(array $variables = [])
    {
        $element = craft()->api->saveElement($this->request);

        return $this->response
            ->setCreated()
            ->setItem($element);
    }

    /**
     * Action Update
     *
     * @param array $variables Variables
     *
     * @return Response Response
     */
    public function actionUpdate(array $variables = [])
    {
        $element = craft()->api->saveElement($this->request);

        return $this->response
            ->setStatus(200, 'Element updated successfully.')
            ->setItem($element);
    }

    /**
     * Action Delete
     *
     * @param array $variables Variables
     *
     * @return Response Response
     */
    public function actionDelete(array $variables = [])
    {
        craft()->api->deleteElement($this->request);

        return $this->response->setStatus(204, 'Element deleted successfully.');
    }

    /**
     * Action Show Variables
     *
     * @param array $variables Variables
     *
     * @return Response Response
     */
    public function actionShowVariables(array $variables)
    {
        return $this->response
            ->setTransformer(new ArrayTransformer)
            ->setItem($variables);
    }

}
