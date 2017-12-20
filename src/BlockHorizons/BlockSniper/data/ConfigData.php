<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\data;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\utils\TextFormat as TF;

class ConfigData {

	const OPTION_VERSION = -1;

	const OPTION_AUTO_UPDATE_CONFIG = 0;
	const OPTION_LANGUAGE = 1;
	const OPTION_BRUSH_ITEM = 2;
	const OPTION_MAX_SIZE = 3;
	const OPTION_MIN_ASYNC_SIZE = 4;
	const OPTION_MAX_REVERT_STORES = 5;
	const OPTION_RESET_DECREMENT_BRUSH = 6;
	const OPTION_SAVE_BRUSH_PROPERTIES = 7;
	const OPTION_DROP_LEAFBLOWER_PLANTS = 8;
	const OPTION_OPEN_GUI_AUTOMATICALLY = 9;
	const OPTION_MY_PLOT_SUPPORT = 10;
	const OPTION_SELECTION_ITEM = 11;

	/** @var array */
	private $settings = [];
	/** @var Loader */
	private $loader = null;
	/** @var array */
	private $conversion = [
		"Configuration-Version" => -1,
		"Auto-Configuration-Update" => 0,
		"Message-Language" => 1,
		"Brush-Item" => 2,
		"Maximum-Size" => 3,
		"Asynchronous-Operation-Size" => 4,
		"Maximum-SyncRevert-Stores" => 5,
		"Reset-Decrement-Brush" => 6,
		"Save-Brush-Properties" => 7,
		"Drop-Leafblower-Plants" => 8,
		"Open-Gui-Automatically" => 9,
		"MyPlot-Support" => 10,
		"Selection-Item" => 11
	];

	public function __construct(Loader $loader) {
		$this->loader = $loader;

		$this->collectSettings();
	}

	public function collectSettings(): void {
		$cfg = yaml_parse_file($this->getLoader()->getDataFolder() . "settings.yml");
		$this->settings = @[
			-1 => $cfg["Configuration-Version"],
			0 => $cfg["Auto-Configuration-Update"] ?? true,
			1 => $cfg["Message-Language"] ?? "",
			2 => $cfg["Brush-Item"] ?? 396,
			3 => $cfg["Maximum-Size"] ?? 15,
			4 => $cfg["Asynchronous-Operation-Size"] ?? 15,
			5 => $cfg["Maximum-SyncRevert-Stores"] ?? 15,
			6 => $cfg["Reset-Decrement-Brush"] ?? true,
			7 => $cfg["Save-Brush-Properties"] ?? true,
			8 => $cfg["Drop-Leafblower-Plants"] ?? true,
			9 => $cfg["Open-Gui-Automatically"] ?? true,
			10 => $cfg["MyPlot-Support"] ?? false,
			11 => $cfg["Selection-Item"] ?? 348
		];
		if($cfg["Configuration-Version"] !== Loader::CONFIGURATION_VERSION) {
			$autoUpdate = $cfg["Auto-Configuration-Update"];
			$this->getLoader()->getLogger()->info(TF::AQUA . "[BlockSniper] A new Configuration version was found. " . ($autoUpdate ? "Updating Configuration file..." : null));
			if($autoUpdate) {
				$this->updateConfig();
			}
		} else {
			$this->getLoader()->getLogger()->info(TF::AQUA . "[BlockSniper] No new Configuration version found, Configuration is up to date.");
		}
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	public function updateConfig(): void {
		unlink($this->getLoader()->getDataFolder() . "settings.yml");
		$this->settings[self::OPTION_VERSION] = Loader::CONFIGURATION_VERSION;
		yaml_emit_file($this->getLoader()->getDataFolder() . "settings.yml", $this->settings);
	}

	/**
	 * @return bool
	 */
	public function openGuiAutomatically(): bool {
		return $this->settings[self::OPTION_OPEN_GUI_AUTOMATICALLY];
	}

	/**
	 * @return string
	 */
	public function getLanguage(): string {
		return (string) $this->settings[self::OPTION_LANGUAGE];
	}

	/**
	 * @return int
	 */
	public function getBrushItem(): int {
		return (int) $this->settings[self::OPTION_BRUSH_ITEM];
	}

	/**
	 * @return int
	 */
	public function getMaxRadius(): int {
		return (int) $this->settings[self::OPTION_MAX_SIZE];
	}

	/**
	 * @return int
	 */
	public function getMaxUndoStores(): int {
		return (int) $this->settings[self::OPTION_MAX_REVERT_STORES];
	}

	/**
	 * @return bool
	 */
	public function resetDecrementBrush(): bool {
		return (bool) $this->settings[self::OPTION_RESET_DECREMENT_BRUSH];
	}

	/**
	 * @return bool
	 */
	public function saveBrushProperties(): bool {
		return (bool) $this->settings[self::OPTION_SAVE_BRUSH_PROPERTIES];
	}

	/**
	 * @return bool
	 */
	public function dropLeafblowerPlants(): bool {
		return (bool) $this->settings[self::OPTION_DROP_LEAFBLOWER_PLANTS];
	}

	/**
	 * @param string $key
	 *
	 * @return mixed|null
	 */
	public function get(string $key) {
		if(isset($this->settings[$key])) {
			return $this->settings[$key];
		}
		return null;
	}

	/**
	 * @param int   $key
	 * @param mixed $value
	 */
	public function set(int $key, $value): void {
		$this->settings[$key] = $value;
	}

	/**
	 * @return int
	 */
	public function getMinimumAsynchronousSize(): int {
		return (int) $this->settings[self::OPTION_MIN_ASYNC_SIZE];
	}

	/**
	 * @return bool
	 */
	public function updatesAutomatically(): bool {
		return (bool) $this->settings[self::OPTION_AUTO_UPDATE_CONFIG];
	}

	/**
	 * @return bool
	 */
	public function hasMyPlotSupport(): bool {
		return (bool) $this->settings[self::OPTION_MY_PLOT_SUPPORT];
	}

	/**
	 * @return int
	 */
	public function getSelectionItem(): int {
		return (int) $this->settings[self::OPTION_SELECTION_ITEM];
	}

	public function save(): void {
		yaml_emit_file($this->getLoader()->getDataFolder() . "settings.yml", $this->getStoredSettingsArray());
	}

	/**
	 * @return array
	 */
	public function getStoredSettingsArray(): array {
		$settings = [];
		foreach($this->settings as $key => $setting) {
			$key = array_search($key, $this->conversion, true);
			$settings[$key] = $setting;
		}
		return $settings;
	}
}