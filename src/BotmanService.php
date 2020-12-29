<?php


namespace App;


use App\Conversation\OnboardingConversation;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\DoctrineCache;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Storages\Drivers\FileStorage;
use Doctrine\Common\Cache\FilesystemCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Flex\Cache;

class BotmanService
{
    /** @var BotMan */
    private $botman;
    /**
     * @var Cache
     */
    private $appCache;

    public function __construct()
    {
    }

    public function initialize(Request $request)
    {
        $config = [
            // Your driver-specific configuration
            // "telegram" => [
            //    "token" => "TOKEN"
            // ]
        ];

        // Load the driver(s) you want to use
        DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
        

        // Create an instance
        $storagePath = realpath(__DIR__ . '/../var') . '/botman';
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0777, true);
        }
        $cache  = new FilesystemCache($storagePath);
        
        $this->botman = BotManFactory::create(
            $config, 
            new DoctrineCache($cache),
            $request, 
            new FileStorage($storagePath)
        );

        // Give the bot something to listen for.
        $this->botman->hears('hello', function (BotMan $bot) {
            $user = $bot->getUser();
            $bot->reply('Hello '.$user->getId());
            $bot->startConversation(new OnboardingConversation(), $user->getId(), get_class($bot->getDriver()));
        });        
    }

    public function handleRequest(Request $request)
    {
        $this->initialize($request);
        $this->botman->listen();
    }
}