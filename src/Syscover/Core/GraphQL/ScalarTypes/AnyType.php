<?php namespace Syscover\Core\GraphQL\ScalarTypes;

use GraphQL\Type\Definition\ScalarType;
use GraphQL\Language\AST\BooleanValueNode;
use GraphQL\Language\AST\FloatValueNode;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Language\AST\IntValueNode;

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

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input
     *
     * @param \GraphQL\Language\AST\Node $valueNode
     * @return mixed
     */
    public function parseLiteral($ast)
    {
        if ($ast instanceof StringValueNode)
        {
            return (string) $ast->value;
        }

        if ($ast instanceof IntValueNode)
        {
            return (int) $ast->value;
        }

        if ($ast instanceof BooleanValueNode)
        {
            return (boolean) $ast->value;
        }

        if ($ast instanceof FloatValueNode)
        {
            return (float) $ast->value;
        }

        return null;
    }
}