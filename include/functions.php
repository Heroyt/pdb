<?php

/**
 * @file      functions.php
 * @brief     Main functions
 * @details   File containing all main functions for the app.
 * @author    Tomáš Vojík <vojik@wboy.cz>
 * @date      2021-09-22
 * @version   1.0
 * @since     1.0
 */

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Add a trailing slash to a string (file/directory path)
 *
 * @param string $string
 *
 * @return string
 */
function trailingUnSlashIt(string $string): string {
    if (substr($string, -1) === DIRECTORY_SEPARATOR) {
        $string = substr($string, 0, -1);
    }
    return $string;
}

function jsonSerialize(mixed $data): string {
    $normalizerContext = [
      AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, string $format, array $context) {
        if (property_exists($object, 'code')) {
            return $object->code;
        }
        if (property_exists($object, 'id')) {
            return $object->id;
        }
        if (property_exists($object, 'name')) {
            return $object->name;
        }
          return null;
      },
    ];
    $serializer = new Serializer(
        [
        new DateTimeNormalizer(),
        new BackedEnumNormalizer(),
        new JsonSerializableNormalizer(defaultContext: $normalizerContext),
        new ObjectNormalizer(defaultContext: $normalizerContext),
        ],
        [
        new JsonEncoder(
            defaultContext: [
                            JsonDecode::ASSOCIATIVE => true,
                            JsonEncode::OPTIONS     => JSON_UNESCAPED_UNICODE
                              | JSON_UNESCAPED_SLASHES
                              | JSON_PRESERVE_ZERO_FRACTION
                              | JSON_THROW_ON_ERROR,
                          ]
        ),
        ],
    );
    return $serializer->serialize($data, 'json');
}


/**
 * @param  ServerRequestInterface  $request
 * @return list<non-empty-lowercase-string>
 */
function getAcceptTypes(ServerRequestInterface $request): array {
    $types = [];
    foreach ($request->getHeader('Accept') as $value) {
        $str = strtolower(trim(explode(';', $value, 2)[0]));
        if ($str === '') {
            continue;
        }
        $types[] = $str;
    }
    return $types;
}

/**
 * @param  non-empty-string|null  $data
 * @return non-empty-string
 */
function guidV4(?string $data = null): string {
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) === 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
