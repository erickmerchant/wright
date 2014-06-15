<?php namespace Wright\Model;

use Aura\Sql\ExtendedPdo;

$container->bind(SchemaInterface::class, Schema::class, [
    'connection' => $container->resolvable(function () {
        return new ExtendedPdo('sqlite::memory:');
    })
]);
