<?php

use DoctrineDataFixtureModule\Service\FixtureFactory;

return [
    'factories' => [
        'doctrine.configuration.fixtures' => new FixtureFactory('fixtures_default'),
    ],
];
