<?php

return [

    /**
     * Api Route Prefix
     *
     * The Api Route Prefix acts as a namespace and is prepended to all
     * routes that the API plugin defines.
     */
    'apiRoutePrefix' => 'api',

    /**
     * Autoload
     *
     * Determine which directories should have thier files autoloaded.
     *
     * `true` loads the files in that directory across ALL plugins.
     * `false` loads the files in that directory only in the ApiHelper plugin.
     */
    'autoload' => [
        'transformers' => true,
        'validators'   => true,
    ],

    /**
     * Content Model Fields Location
     *
     * This is the key, in the body of $_POST or php://input, in which
     * content that will need to be added to the element's content
     * model.
     */
    'contentModelFieldsLocation' => 'fields',

    /**
     * Default Serializers
     *
     * A Serializer structures your Transformed data in certain ways.
     * For more info, see http://fractal.thephpleague.com/serializers/.
     */

    'defaultSerializer' => 'DataArraySerializer',

    'serializers' => [
        'ArraySerializer'     => 'League\Fractal\Serializer\ArraySerializer',
        'DataArraySerializer' => 'League\Fractal\Serializer\DataArraySerializer',
        'JsonApiSerializer'   => 'League\Fractal\Serializer\JsonApiSerializer',
    ],

    /**
     * Supported Element Types
     *
     * Define which element types are supported, along with configurations
     * for each type.
     */
    'supportedElementTypes' => [
        '*' => [
            'public' => ['GET', 'POST', 'PUT', 'DELETE'],
            'authenticated' => [],
        ],

        'Entry' => [
            'public' => ['GET'],
            'authenticated' => [
                'users' => [
                    'wesrice' => ['GET', 'POST', 'PUT', 'DELETE'],
                ],
                'groups' => [
                    'admin' => ['DELETE'],
                ]
            ],
        ],
    ],



];
