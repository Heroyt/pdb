<?php

/**
 * @author Tomáš Vojík <xvojik00@stud.fit.vutbr.cz>, <vojik@wboy.cz>
 */

namespace App\Install;

use Dibi\Exception;

/**
 * Class that initially seeds the database
 */
class Seeder implements InstallInterface
{
    /**
     * @inheritDoc
     */
    public static function install(bool $fresh = false): bool {
        try {
            /** @phpstan-ignore catch.neverThrown */
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL . $e->getSql() . PHP_EOL;
            return false;
        }
        return true;
    }
}
