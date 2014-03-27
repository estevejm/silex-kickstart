<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

/**
 * Bootstrap
 *
 * @author estevejuliamelis <estevejuliamelis@gmail.com>
 */
class Bootstrap {

    private static $app;

    public static function execute() {

        self::$app = new Silex\Application();

        self::loadUtils();
        self::loadConfig();
        self::loadServiceProviders();
        //self::connectDB();

        return self::$app;
    }

    private static function loadConfig() {
        $config = Yaml::parse(__DIR__ . "/../app/config/config.yml");

        // setup APP_ENV in server dashboard if we want this running
        self::$app['env']    = $_SERVER["APP_ENV"] ?: "dev";
        self::$app['config'] = $config[self::$app['env']];
        self::$app['debug']  = self::$app['config']["debug"];
    }

    private static function loadServiceProviders() {
        self::$app->register(
            new Silex\Provider\TwigServiceProvider(),
            array(
                'twig.path' => __DIR__ . '/../app/views'
            )
        );

        self::$app->register(new Silex\Provider\SessionServiceProvider());
    }

    private static function connectDB() {
        try {
            self::$app['db'] = new PDO(
                "mysql:host=" . self::$app['config']["db"]["host"]
                . ";dbname=" . self::$app['config']["db"]["dbname"],
                self::$app['config']["db"]["username"],
                self::$app['config']["db"]["password"]
            );
        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        self::$app['db']->exec("set names utf8");

        self::$app['db']->setAttribute(
            PDO::ATTR_DEFAULT_FETCH_MODE,
            PDO::FETCH_ASSOC
        );
    }

    private static function loadUtils() {
        function d($var, $die = true) {
            echo "<pre>";
            var_dump($var);
            echo "</pre>";

            if ($die) die;
        }
    }
}