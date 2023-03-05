<?php

declare(strict_types=1);

namespace PlayerBounty;

use PlayerBounty\Events\Events;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use PlayerBounty\Commands\PlayerBountyCommand;
use pocketmine\utils\Config;
use function array_shift;

class Loader extends PluginBase {

    $economy = EconomyAPI::getInstance();
    public Config $config;
    public array $bounty = [];

	public function onEnable() : void{
	$this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        $this->saveResource("messages.yml");
        $this->config = new Config($this->getDataFolder(). "/messages.yml", Config::YAML);
	$this->getServer()->getPluginManager()->registerEvents(new Events($this), $this);
        $this->getServer()->getCommandMap()->register("playerbounty", new PlayerBountyCommand($this));
	}

    public function getMoney(Player $player) : float {
        $money = $this->economy->myMoney($player->getName());
        assert(is_float($money));
        return $money;
    }

    public function addMoney(Player $player, float $money) : void {
        $this->economy->addMoney($player->getName(), $money);
    }

    public function removeMoney(Player $player, float $money) : void {
        $this->economy->reduceMoney($player->getName(), $money);
    }

    public function getConfigMessage(string $path) : string {
        $config = new Config($this->getDataFolder(). "/messages.yml", Config::YAML);
        return $config->get("$path");
    }

    public function replaceVars(string $str, array $vars): string {
        foreach ($vars as $key => $value) {
            $str = str_replace("{" . $key . "}", $value, $str);
        }
        return $str;
    }

    public function isBountyList(string $name) : bool {
        return array_key_exists($name, $this->bounty);
    }

    public function addBountyList(string $name, int $amount) {
        $this->bounty[$name] = $amount;
    }

    public function deleteBountylist(string $name) {
        unset($this->bounty[$name]);
    }

}
