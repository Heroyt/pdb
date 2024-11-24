<?php

/**
 * @file    load.php
 * @brief   Main bootstrap
 * @details File which is responsible for loading all necessary components of the app
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @date    2021-09-22
 * @version 1.0
 * @since   1.0
 */

use App\Core\Loader;
use Dibi\Bridges\Tracy\Panel;
use Latte\Bridges\Tracy\BlueScreenPanel;
use Lsr\Core\App;
use Lsr\Core\DB;
use Lsr\Helpers\Tools\Timer;
use Lsr\Helpers\Tracy\CacheTracyPanel;
use Lsr\Helpers\Tracy\DbTracyPanel;
use Lsr\Helpers\Tracy\RoutingTracyPanel;
use Lsr\Helpers\Tracy\TimerTracyPanel;
use Lsr\Helpers\Tracy\TranslationTracyPanel;
use Nette\Bridges\DITracy\ContainerPanel;
use Tracy\Debugger;
use Tracy\NativeSession;

date_default_timezone_set('Europe/Prague');

// Autoload libraries
require_once ROOT . 'vendor/autoload.php';

// Load all globals and constants
require_once ROOT . 'include/config.php';

Timer::start('core.init');

if (!is_dir(LOG_DIR) && !mkdir(LOG_DIR) && (!file_exists(LOG_DIR) || !is_dir(LOG_DIR))) {
    throw new RuntimeException(sprintf('Directory "%s" was not created', LOG_DIR));
}
if (!is_dir(UPLOAD_DIR) && !mkdir(UPLOAD_DIR) && (!file_exists(UPLOAD_DIR) || !is_dir(UPLOAD_DIR))) {
    throw new RuntimeException(sprintf('Directory "%s" was not created', UPLOAD_DIR));
}

// Enable tracy
Debugger::$editor = 'phpstorm://open?file=%file&line=%line';
Debugger::$dumpTheme = 'dark';
Debugger::setSessionStorage(new NativeSession());

// Register custom tracy panels
Debugger::getBar()
    ->addPanel(new TimerTracyPanel())
    ->addPanel(new CacheTracyPanel())
    ->addPanel(new DbTracyPanel())
    ->addPanel(new TranslationTracyPanel())
    ->addPanel(new RoutingTracyPanel());

Loader::init();

if (defined('INDEX') && PHP_SAPI !== 'cli') {
    // Register library tracy panels
    if (!isset($_ENV['noDb'])) {
        (new Panel())->register(DB::getConnection());
    }
    if (!PRODUCTION) {
        Debugger::getBar()
                ->addPanel(new ContainerPanel(App::getContainer()));
    }
}

BlueScreenPanel::initialize();

Timer::stop('core.init');
