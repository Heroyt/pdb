<?php

/**
 * @file      config.php
 * @brief     App configuration
 * @details   Contains all constants and settings
 * @author    Tomáš Vojík <vojik@wboy.cz>
 */

use Lsr\Core\Config;

require_once ROOT . 'include/constants.php';

/** If in production */
define('PRODUCTION', !(Config::getInstance()->getConfig('General')['DEBUG'] ?? false));
