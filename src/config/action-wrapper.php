<?php

return [
    // The default model key type, when the model key type did not define
    'model_key_type' => 'int',

    'action' => [
        // The default return type of actions without a model
        'return_type' => 'void',

        // Make an action dto as a readonly (or not) class when an action creates the dto
        'readonly_dto' => true,
    ],
];