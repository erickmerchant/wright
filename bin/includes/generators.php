<?php namespace Wright\Generators;

$container->bind(GeneratorCollection::class, GeneratorCollection::class);

$container->alias(GeneratorCollection::class, 'generators');
