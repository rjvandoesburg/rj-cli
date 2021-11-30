<?php

declare(strict_types=1);

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

abstract class BaseCommand extends Command
{
    /**
     * The array of trait initializers that will be called on each new instance.
     *
     * @var array
     */
    protected static $traitInitializers = [];

    public function __construct()
    {
        static::bootTraits();

        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    public function configure()
    {
        parent::configure();

        $this->initializeTraits();
    }

    /**
     * Boot all of the bootable traits on the model.
     *
     * @return void
     */
    protected static function bootTraits(): void
    {
        $class = static::class;

        $booted = [];

        static::$traitInitializers[$class] = [];

        foreach (class_uses_recursive($class) as $trait) {
            $method = 'boot' . class_basename($trait);

            if (method_exists($class, $method) && ! in_array($method, $booted)) {
                forward_static_call([$class, $method]);

                $booted[] = $method;
            }

            if (method_exists($class, $method = 'initialize' . class_basename($trait))) {
                static::$traitInitializers[$class][] = $method;

                static::$traitInitializers[$class] = array_unique(
                    static::$traitInitializers[$class]
                );
            }
        }
    }

    /**
     * Initialize any initializable traits on the model.
     *
     * @return void
     */
    protected function initializeTraits(): void
    {
        foreach (static::$traitInitializers[static::class] as $method) {
            $this->{$method}();
        }
    }
}
