<?php namespace Syscover\Core\GraphQL\Scalars;

use GraphQL\Type\Definition\ScalarType;

class JsonScalar extends ScalarType
{
    public $name = "Json";
    public $description = "Object scalar type, type that encapsulates for any object";

    public function serialize($value)
    {
        if (is_string($value))
        {
            $value = json_decode($value, true);
        }

        return $value;
    }

    public function parseValue($value)
    {
        return $value;
    }

    public function parseLiteral($valueNode, array $variables = null)
    {
        return $valueNode->value;
    }
}