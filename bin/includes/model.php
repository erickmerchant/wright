<?php namespace Wright\Model;

use Aura\Sql\ExtendedPdo;

$container->bind(Schema::class, null, [
    'connection' => $container->resolvable(function () {
        return new ExtendedPdo('sqlite::memory:');
    })
]);
