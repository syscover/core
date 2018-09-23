<?php namespace Syscover\Core\GraphQL\Types\Scalars;

use GraphQL\Language\AST\BooleanValueNode;
use GraphQL\Language\AST\FloatValueNode;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;

class AnyType extends ScalarType
{
    public $name = "Any";
    public $description = "Any type of application, can to be a Int, String or Boolean";

    /**
     * Serializes an internal value to include in a response.
     *
     * @param string $value
     * @return string
     */
    public function serialize($value)
    {
        return $value;
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * @param mixed $value
     * @return mixed
     */
    public function parseValue($value)
    {
        return $value;
    }

    public function parseLiteral($valueNode, array $variables = null)
    {
        if ($valueNode instanceof StringValueNode)
        {
            return (string) $valueNode->value;
        }

        if ($valueNode instanceof IntValueNode)
        {
            return (int) $valueNode->value;
        }

        if ($valueNode instanceof BooleanValueNode)
        {
            return (boolean) $valueNode->value;
        }

        if ($valueNode instanceof FloatValueNode)
        {
            return (float) $valueNode->value;
        }

        return null;
    }
}