<?php

return [

    /**
     * Allow Email Match
     */
    'allowEmailMatch' => false,

    /**
     * Twitter Configuration
     */
    'twitter' => [
        'userMapping' => [
            'address' => '{{ location is defined and location ? location : null }}',
            'twitter' => '{{ nickname is defined and nickname ? nickname : null }}',
        ],
    ],

    /**
     * Facebook Configuration
     */
    'facebook' => [
        'userMapping' => [
            'firstName' => '{{ first_name ?? null }}',
            'lastName' => '{{ last_name ?? null }}',
            'address2' => '{{ location.name ?? null }}',
            'gender' => '{{ gender ?? null }}',
            'facebook' => '{{ link ?? null }}',
        ],
    ],

];
