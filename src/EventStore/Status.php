<?php

declare(strict_types=1);

namespace App\EventStore;

class Status
{
    public const int CODE_OK = 0;

    /** @var array<string,string> */
    public array $metadata = [];
    public int $code = 0;
    public string $details = '';

    public static function fromObject(object $status): Status {
        $obj = new self();
        foreach (get_object_vars($status) as $property => $value) {
            if (property_exists($obj, $property)) {
                $obj->$property = $value;
            }
        }
        return $obj;
    }
}
