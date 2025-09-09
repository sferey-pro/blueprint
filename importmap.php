<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app-react' => [
        'path' => './assets/react/app-react.js',
        'entrypoint' => true,
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@symfony/ux-react' => [
        'path' => './vendor/symfony/ux-react/assets/dist/loader.js',
    ],
    '@symfony/ux-live-component' => [
        'path' => './vendor/symfony/ux-live-component/assets/dist/live_controller.js',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    'react' => [
        'version' => '19.1.1',
    ],
    'react-dom/client' => [
        'version' => '19.1.1',
    ],
    'react-dom' => [
        'version' => '19.1.1',
    ],
    'scheduler' => [
        'version' => '0.26.0',
    ],
    'axios' => [
        'version' => '1.11.0',
    ],
    'react-data-table-component' => [
        'version' => '7.7.0',
    ],
    'styled-components' => [
        'version' => '6.1.19',
    ],
    'tslib' => [
        'version' => '2.8.1',
    ],
    '@emotion/is-prop-valid' => [
        'version' => '1.4.0',
    ],
    'shallowequal' => [
        'version' => '1.1.0',
    ],
    'stylis' => [
        'version' => '4.3.6',
    ],
    '@emotion/unitless' => [
        'version' => '0.10.0',
    ],
    '@emotion/memoize' => [
        'version' => '0.9.0',
    ],
];
