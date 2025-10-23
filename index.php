<?php

use Sequtiyhusky\Fania\FaniaCore;

include __DIR__ . DIRECTORY_SEPARATOR . "vendor/autoload.php";

$core = (new FaniaCore());

while($core->getDataManager("Config")->get("online")){
    $core->settingLoader();
}
