<?php namespace Syscover\Core\Services;

use Illuminate\Support\Facades\DB;

class MySQLService
{
    public static function renameColumn(
        $tableName,
        $columnFrom,
        $columnTo,
        $type,
        $length,
        $unsigned = false,
        $nullable = false,
        $default = null
    )
    {
        // set table with prefix
        $tableName = config('database.connections.mysql.prefix') . $tableName;

        $sql = self::getSqlStatement($tableName, $columnFrom, $columnTo, $type, $length, $unsigned, $nullable, $default);
        DB::select(DB::raw($sql));
    }

    private static function getSqlStatement(
        $tableName,
        $columnFrom,
        $columnTo,
        $type,
        $length,
        $unsigned,
        $nullable,
        $default = null
    )
    {
        $sql = null;
        switch ($type) {
            case 'TINYINT':
                $sql = 'ALTER TABLE ' . $tableName . ' CHANGE ' . $columnFrom . ' '. $columnTo .' TINYINT(' . $length . ') ' . ($unsigned? 'UNSIGNED ' : null) . ($nullable? 'NULL' : 'NOT NULL') . (empty($default)? null : ' DEFAULT \'' . $default . '\'');
                break;

            case 'SMALLINT':
                $sql = 'ALTER TABLE ' . $tableName . ' CHANGE ' . $columnFrom . ' '. $columnTo .' SMALLINT(' . $length . ') ' . ($unsigned? 'UNSIGNED ' : null) . ($nullable ? 'NULL' : 'NOT NULL') . (empty($default)? null : ' DEFAULT \'' . $default . '\'');
                break;

            case 'INT':
                $sql = 'ALTER TABLE ' . $tableName . ' CHANGE ' . $columnFrom . ' '. $columnTo .' INT(' . $length . ') ' . ($unsigned? 'UNSIGNED ' : null) . ($nullable ? 'NULL' : 'NOT NULL') . (empty($default)? null : ' DEFAULT \'' . $default . '\'');
                break;

            case 'BIGINT':
                $sql = 'ALTER TABLE ' . $tableName . ' CHANGE ' . $columnFrom . ' '. $columnTo .' BIGINT(' . $length . ') ' . ($unsigned? 'UNSIGNED ' : null) . ($nullable ? 'NULL' : 'NOT NULL') . (empty($default)? null : ' DEFAULT \'' . $default . '\'');
                break;

            case 'DECIMAL':
                $sql = 'ALTER TABLE ' . $tableName . ' CHANGE ' . $columnFrom . ' '. $columnTo .' DECIMAL(' . $length . ') ' . ($unsigned? 'UNSIGNED ' : null) . ($nullable ? 'NULL' : 'NOT NULL') . (empty($default)? null : ' DEFAULT \'' . $default . '\'');
                break;

            case 'VARCHAR':
                $sql = 'ALTER TABLE ' . $tableName . ' CHANGE ' . $columnFrom . ' '. $columnTo .' VARCHAR(' . $length . ') CHARACTER SET utf8 COLLATE utf8_unicode_ci ' . ($nullable ? 'NULL' : 'NOT NULL') . (empty($default)? null : ' DEFAULT \'' . $default . '\'');
                break;

            case 'TEXT':
                $sql = 'ALTER TABLE ' . $tableName . ' CHANGE ' . $columnFrom . ' '. $columnTo .' TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci ' . ($nullable ? 'NULL' : 'NOT NULL') . (empty($default)? null : ' DEFAULT \'' . $default . '\'');
                break;

            case 'JSON':
                $sql = 'ALTER TABLE ' . $tableName . ' CHANGE ' . $columnFrom . ' '. $columnTo .' JSON ' . ($nullable ? 'NULL' : 'NOT NULL') . (empty($default) ? null : ' DEFAULT \'' . $default . '\'');
                break;
        }

        return $sql;
    }
}