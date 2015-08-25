<?php

namespace Craft;

use ApiHelper\Http\Request;

class ApiService extends BaseApplicationComponent
{
    /**
     * Get Elements
     *
     * @param Request $request Request
     *
     * @return array Elements
     */
    public function getElements(Request $request)
    {
        return craft()->apiHelper->getElements($request->getCriteria());
    }

    /**
     * Get Element
     *
     * @param Request $request Request
     *
     * @return BaseElementModel Element
     */
    public function getElement(Request $request)
    {
        return craft()->apiHelper->getElement($request);
    }

    /**
     * Save Element
     *
     * @param Request $request Request
     *
     * @return BaseElementModel Element
     */
    public function saveElement(Request $request)
    {
        $element = craft()->apiHelper->getElement($request);

        $populated_element = craft()->apiHelper->populateElement($element, $request);

        $validator = craft()->apiHelper->getValidator($populated_element);

        craft()->apiHelper->validateElement($populated_element, $validator);

        return craft()->apiHelper->saveElement($populated_element, $request);
    }

    /**
     * Delete Element
     *
     * @param Request $request Request
     *
     * @return void
     */
    public function deleteElement(Request $request)
    {
        craft()->apiHelper->deleteElement($request);
    }
}
