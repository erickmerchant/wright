<?php namespace Wright\Model;

use Wright\Data\DataInterface;
use Wright\Settings\SettingsInterface;
use Aura\Sql\ExtendedPdoInterface;

interface SchemaInterface
{
    public function getConnection();
}
