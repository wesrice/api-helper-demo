<?php

namespace Craft;

use ApiHelper\Http\Request;
use ApiHelper\Validation\Validators\AbstractValidator;
use ApiHelper\Validation\Exceptions\ApiHelperException;

class ApiHelperService extends BaseApplicationComponent
{
    /**
     * Get Elements
     *
     * @param ElementCriteriaModel $criteria Criteria
     *
     * @return array Elements
     */
    public function getElements(ElementCriteriaModel $criteria)
    {
        return $criteria->find();
    }

    /**
     * Get Element
     *
     * @param int $id
     *
     * @return object
     */
    public function getElement(Request $request)
    {
        $element_id = $request->getAttribute('elementId');

        if ($element_id) {
            $element = craft()->elements->getElementById($element_id);
        } else {
            $element = sprintf('Craft\\%sModel', $request->getAttribute('elementType'));

            $element = new $element;
        }

        return $element;
    }

    /**
     * Populate Element
     *
     * @param BaseElementModel $element Element
     * @param Request          $request Request
     *
     * @return BaseElementModel Element
     */
    public function populateElement(BaseElementModel $element, Request $request)
    {
        $fields_key = craft()->config->get('contentModelFieldsLocation', 'apiHelper');

        $attributes = $request->getParsedBody();

        $element->setAttributes($attributes);

        if (isset($attributes[$fields_key])) {
            if (isset($attributes[$fields_key]['title'])) {
                $element->getContent()->title = $attributes[$fields_key]['title'];
            }

            $element->setContent($attributes[$fields_key]);
        }

        return $element;
    }

    /**
     * Get Validator
     *
     * @param BaseElementModel $element [description]
     *
     * @return Validator Validator
     */
    public function getValidator(BaseElementModel $element)
    {
        $validator = sprintf('ApiHelper\\Validation\\Validators\\%sValidator', $element->getElementType());

        return new $validator;
    }

    /**
     * Validate Element
     *
     * @param BaseElementModel $element   Element
     * @param Validator        $validator Validator
     *
     * @return void
     */
    public function validateElement(BaseElementModel $element, AbstractValidator $validator)
    {
        $validator->validate($element);

        if ($validator->hasErrors()) {
            $exception = new ApiHelperException();

            $exception
                ->setStatus(422)
                ->setMessage('The request contains invalid arguments.')
                ->setErrors($validator->getErrors());

            throw $exception;
        }
    }

    /**
     * Save Element
     *
     * @param array $params Parameters
     *
     * @return BaseElementModel $model
     */
    public function saveElement(BaseElementModel $element, Request $request)
    {
        $element_type = craft()->elements->getElementType($element->getElementType());

        $result = $element_type->saveElement($element, null);

        craft()->content->saveContent($element);

        if (!$result) {
            $exception = new ApiHelperException();

            $exception
                ->setStatus(400)
                ->setMessage('Element could not be stored.');

            throw $exception;
        }

        return $element;
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
        craft()->elements->deleteElementById($request->getAttribute('elementId'));
    }
}
