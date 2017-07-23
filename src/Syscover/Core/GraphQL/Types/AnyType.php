<?php namespace Syscover\Core\GraphQL\Types;

use GraphQL\Type\Definition\ScalarType;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Language\AST\IntValueNode;

class AnyType extends ScalarType
{

    public $name = "Any";
    public $description = "Any type of application, can to be a Int or String";
    const MAX_INT = 2147483647;
    const MIN_INT = -2147483648;

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

    public function parseLiteral($ast)
    {
        if ($ast instanceof StringValueNode)
        {
            return $ast->value;
        }

        if ($ast instanceof IntValueNode)
        {
            $val = (int) $ast->value;
            if ($ast->value === (string) $val && self::MIN_INT <= $val && $val <= self::MAX_INT)
            {
                return $val;
            }
        }

        return null;
    }
}