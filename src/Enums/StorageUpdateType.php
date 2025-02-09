<?php
declare(strict_types=1);

namespace App\Enums;

enum StorageUpdateType
{

    case PRODUCTION;
    case CONSUMPTION;
    case SELL;
    case LOADING;
    case UNLOADING;
    case OTHER;

}
