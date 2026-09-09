<?php

return [

    'organization' => [

        'name' =>
            env(
                'NACPDEAN_ORGANIZATION_NAME',
                'NATIONAL ASSOCIATION OF CHARCOAL PRODUCERS, DEALERS, EXPORTERS AND AFFORESTATION OF NIGERIA'
            ),

        'short_name' =>
            env(
                'NACPDEAN_ORGANIZATION_SHORT_NAME',
                'NACPDEAN'
            ),

        'address' =>
            env(
                'NACPDEAN_ORGANIZATION_ADDRESS',
                ''
            ),

        'phone' =>
            env(
                'NACPDEAN_ORGANIZATION_PHONE',
                ''
            ),

        'email' =>
            env(
                'NACPDEAN_ORGANIZATION_EMAIL',
                ''
            ),

        'website' =>
            env(
                'NACPDEAN_ORGANIZATION_WEBSITE',
                ''
            ),
    ],

    'signatories' => [

        'national_president' => [

            'name' =>
                env(
                    'NACPDEAN_NATIONAL_PRESIDENT_NAME',
                    ''
                ),

            'title' =>
                env(
                    'NACPDEAN_NATIONAL_PRESIDENT_TITLE',
                    'National President'
                ),
        ],

        'national_secretary_general' => [

            'name' =>
                env(
                    'NACPDEAN_NATIONAL_SECRETARY_GENERAL_NAME',
                    ''
                ),

            'title' =>
                env(
                    'NACPDEAN_NATIONAL_SECRETARY_GENERAL_TITLE',
                    'National Secretary-General'
                ),
        ],
    ],

];
