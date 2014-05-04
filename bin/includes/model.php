<?php namespace Wright\Model;

$container->bind(Schema::class, null, [
    'connection' => $container->resolvable(function () {
        return new \PDO('sqlite::memory:');
    })
]);
