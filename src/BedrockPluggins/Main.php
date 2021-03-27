declare(strict_types=1);

namespace solopaw\OreTracker;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase {

    /** @var SkyBlock */
    private static $instance;

    public static function getInstance(): SkyBlock {
        return self::$instance;
    }

    public function onLoad(): void {
        self::$instance = $this;
        if(!is_dir($dataFolder = $this->getDataFolder())) {
            mkdir($dataFolder);
        }
        $this->saveResource("messages.json");
        $this->saveResource("settings.yml");
    }

    public function onEnable(): void {
        $this->settings = new SkyBlockSettings($this);
        $this->provider = new JSONProvider($this);
        $this->sessionManager = new SessionManager($this);
        $this->islandManager = new IslandManager($this);
        $this->generatorManager = new IslandGeneratorManager($this);
        $this->messageManager = new MessageManager($this);
        $this->commandMap = new IslandCommandMap($this);
        $this->commandMap->registerDefaultCommands();
        $this->checkSpawnProtection();
    }

    public function onDisable(): void {
        foreach($this->islandManager->getIslands() as $island) {
            $island->save();
        }
    }

    public function getSettings(): SkyBlockSettings {
        return $this->settings;
    }

    public function getProvider(): Provider {
        return $this->provider;
    }

    public function getSessionManager(): SessionManager {
        return $this->sessionManager;
    }

    public function getIslandManager(): IslandManager {
        return $this->islandManager;
    }

    public function getGeneratorManager(): IslandGeneratorManager {
        return $this->generatorManager;
    }

    public function getMessageManager(): MessageManager {
        return $this->messageManager;
    }

    public function getCommandMap(): IslandCommandMap {
        return $this->commandMap;
    }

    private function checkSpawnProtection(): void {
        $server = $this->getServer();
        if($server->getSpawnRadius() > 0) {
            $this->getLogger()->warning("Disable the spawn protection on your server.properties, otherwise SkyBlock won't work");
            $server->getPluginManager()->disablePlugin($this);
        }
    }

}
