<?php namespace Syscover\Core\GraphQL\Scalars;

use GraphQL\Type\Definition\ScalarType;

class ObjectScalar extends ScalarType
{
    public $name = "Object";
    public $description = "Object scalar type, type that encapsulates for any object";

    /**
     * Serializes an internal value to include in a response.
     *
     * @param string $value
     * @return string
     */
    public function serialize($value)
    {
        return json_decode(json_encode($value), true);
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * @param mixed $value
     * @return mixed
     */
    public function parseValue($value)
    {
        return json_decode(json_encode($value), false);
    }

    public function parseLiteral($valueNode, array $variables = null)
    {
        return $valueNode->value;
    }
}
