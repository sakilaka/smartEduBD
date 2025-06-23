<?php

namespace App\Helpers;

use Auth;
use Illuminate\Database\Eloquent\Model;

class GlobalHelper extends Model
{

    public static function get_guard()
    {
        if (Auth::guard('admin')->check()) {
            return "admin";
        } else if (Auth::guard('web')->check()) {
            return "user";
        } else {
            return null;
        }
    }

    /**
     * Generate unique id from existing table
     *
     * @param Illuminate\Database\Eloquent\Model $model
     * @param string $column
     * @param $options = [
     *   'prefix' => '',
     *   'conditions' => [],
     *   'pad_length' => 4,
     *   'pad_string' => '0',
     *   'pad_type'   => 'STR_PAD_LEFT',
     * ]
     * @return string
     */
    public static function generate_id($model, $column, $options = [])
    {
        $modelObj = new $model;
        $query    = $modelObj->select($column);

        $prefix    = $options['prefix'] ?? '';
        $padLength = $options['pad_length'] ?? 4;
        $padString = $options['pad_string'] ?? '0';
        $padType   = $options['pad_type'] ?? '';
        $padType   = $padType == 'right' ? STR_PAD_RIGHT : STR_PAD_LEFT;

        if (isset($options['conditions']) && is_array($options['conditions'])) {
            $query->where($options['conditions']);
        }

        $lastRow = $query->latest('id')->latest($column)->first();
        $lastId  = $lastRow ? $lastRow->{$column} : 0;
        $lastId = $prefix ? substr($lastId, strlen($prefix)) : $lastId;

        $uniqId = self::generateUniqId($modelObj, $column, $lastId, $prefix);

        $padLength = $padLength < strlen($lastId) ? strlen($lastId) : $padLength;

        return $prefix . str_pad($uniqId, $padLength, $padString, $padType);
    }

    // create unique id
    private static function generateUniqId($modelObj, $column, $lastId = 0, $prefix = '')
    {
        $newId  = (int) $lastId + 1;

        if ($modelObj->where($column, $newId)->exists()) {
            return self::generateUniqId($modelObj, $column, $newId, $prefix);
        }

        return $newId;
    }

    // update, delete and insert
    public static function updelsert($foreignColumn, $foreignId, $model, $data)
    {
        $itemsId = $model::where($foreignColumn, $foreignId)
            ->pluck('id')
            ->toArray();

        foreach ($data as $item) {
            $item[$foreignColumn] = $foreignId;
            $item['id']           = $item['id'] ?? null;
            $model::updateOrCreate(['id' => $item['id']], $item);
            if (in_array($item['id'], $itemsId)) {
                $foundKey = array_search($item['id'], $itemsId);
                if ($foundKey !== false) {
                    unset($itemsId[$foundKey]);
                }
            }
        }

        $model::whereIn('id', $itemsId)->delete();
    }

    public static function getLastPart($delimiter, $string = null)
    {
        if ($string) {
            $parts = explode($delimiter, $string);
            return count($parts) > 0 ? end($parts) : null;
        }

        return null;
    }
}
