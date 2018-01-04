<?php

namespace autoupdater;

use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class AutoUpdater
{
    public static function searchForUpdates(Plugin $plugin, $url, $currentVersion)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);

        $array = explode("\n", $data);

        $lastVersion = "";

        foreach ($array as $line) {
            if (strpos($line, "lastVersion:") !== false) {
                $lastVersion = substr($line, strlen("lastVersion: "), strlen($line));
                break;
            }
        }

        if ($lastVersion !== $currentVersion) {
            $updates = "Missed updates:" . PHP_EOL;

            $isHistory = false;

            foreach ($array as $line) {
                if ($isHistory) {
                    $updates .= str_replace('"', "", $line) . PHP_EOL;
                }

                if (strpos($line, "history:") !== false) {
                    $isHistory = true;
                }
            }

            $plugin->getLogger()->alert("You have missed some updates of this plugin. Your version: " . $currentVersion);
            $plugin->getLogger()->alert($updates);
            $plugin->getLogger()->alert("You can download these updates on " . $plugin->getDescription()->getWebsite() . ".");
        } else {
            $plugin->getLogger()->info(TextFormat::RED . "Plugin is up to date.");
        }
    }
}