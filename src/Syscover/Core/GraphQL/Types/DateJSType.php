<?php namespace Syscover\Core\GraphQL\Types;

use Carbon\Carbon;
use GraphQL\Type\Definition\ScalarType;

use GraphQL\Error\Error;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Utils;

class DateJSType extends ScalarType
{

    public $name = "DateJS";
    public $description = "JavaScript Date object from Timestamp SQL";

    /**
     * Serializes an internal value to include in a response.
     *
     * @param string $value
     * @return string
     */
    public function serialize($value)
    {
        var_dump($value);
        exit;
        //$value = 'Fri Jul 07 2017 15:17:11 GMT+0200 (CEST)';
        //$value = '12/02/2001';


        // Assuming internal representation of email is always correct:
        return $value;

        // If it might be incorrect and you want to make sure that only correct values are included in response -
        // use following line instead:
        // return $this->parseValue($value);
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * @param mixed $value
     * @return mixed
     */
    public function parseValue($value)
    {
        var_dump($value);
        exit;
        return $value;
    }

    public function parseLiteral($valueNode)
    {
        // Note: throwing GraphQL\Error\Error vs \UnexpectedValueException to benefit from GraphQL
        // error location in query:
        if (!$valueNode instanceof StringValueNode) {
            throw new Error('Query error: Can only parse strings got: ' . $valueNode->kind, [$valueNode]);
        }
        if (!filter_var($valueNode->value, FILTER_VALIDATE_EMAIL)) {
            throw new Error("Not a valid email", [$valueNode]);
        }
        return $valueNode->value;
    }


}