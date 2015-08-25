<?php

namespace zibo\core\di\io;

use zibo\core\di\DependencyCallArgument;

use zibo\core\di\DependencyContainer;
use zibo\core\Zibo;

use zibo\library\filesystem\File;

/**
 * Cache decorator for another DependencyIO
 */
class CachedDependencyIO implements DependencyIO {

    /**
     * Path to the generated container
     * @var string
     */
    const FILE = 'application/data/cache/container.php';

    /**
     * DependencyIO which is cached by this DependencyIO
     * @var zibo\core\di\io\DependencyIO
     */
    private $io;

    /**
     * Constructs a new cached DependencyIO
     * @param DependencyIO $io the DependencyIO which needs a cache
     * @return null
     */
    public function __construct(DependencyIO $io) {
        $this->io = $io;
    }

    /**
     * Gets the dependency container
     * @param zibo\core\Zibo $zibo Instance of zibo
     * @return zibo\core\di\DependencyContainer
     */
    public function getContainer(Zibo $zibo) {
        $file = new File(self::FILE);
        if ($file->exists()) {
            require($file);
        }

    	if (isset($container)) {
    		return $container;
    	}

    	$container = $this->io->getContainer($zibo);

    	$parent = $file->getParent();
    	$parent->create();

    	$php = $this->generatePhp($container, $file);

    	$file->write($php);

    	return $container;
    }

    /**
	 * Generates a PHP source file for the provided dependency container
	 * @param zibo\core\di\DependencyContainer $container
	 * @return string
     */
    public static function generatePhp(DependencyContainer $container) {
        $output = "<?php\n\n";
        $output .= "/*\n";
        $output .= " * This file is generated by zibo\core\di\io\CachedDependencyIO.\n";
        $output .= " */\n";
        $output .= "\n";
        $output .= "use zibo\\core\\di\\Dependency;\n";
        $output .= "use zibo\\core\\di\\DependencyCall;\n";
        $output .= "use zibo\\core\\di\\DependencyCallArgument;\n";
        $output .= "use zibo\\core\\di\\DependencyContainer;\n";
        $output .= "\n";
        $output .= '$container' . " = new DependencyContainer();\n";
        $output .= "\n";

        $dependencies = $container->getDependencies();
        foreach ($dependencies as $interface => $interfaceDependencies) {
            foreach ($interfaceDependencies as $dependency) {
                $callIndex = 1;

                $calls = $dependency->getCalls();
                if ($calls) {
                    foreach ($calls as $call) {
                        $argumentIndex = 1;

                        $arguments = $call->getArguments();
                        if ($arguments) {
                            foreach ($arguments as $argument) {
                                $extra = null;

                                $type = $argument->getType();
                                switch ($type) {
                                    case DependencyCallArgument::TYPE_DEPENDENCY:
                                        $extra = $argument->getDependencyId();
                                        break;
                                    case DependencyCallArgument::TYPE_CONFIG:
                                        $extra = $argument->getDefaultValue();
                                        break;
                                }

                                $output .= '$a' . $argumentIndex . ' = new DependencyCallArgument(';
                                $output .= self::getArgumentValue($type) . ', ';
                                $output .= self::getArgumentValue($argument->getValue()) . ', ';
                                $output .= self::getArgumentValue($extra) . ");\n";
                                $argumentIndex++;
                            }
                        }

                        $output .= '$c' . $callIndex . ' = new DependencyCall(';
                        $output .= self::getArgumentValue($call->getMethodName()) . ");\n";

                        for ($i = 1; $i < $argumentIndex; $i++) {
                            $output .= '$c' . $callIndex . '->addArgument($a' . $i . ");\n";
                        }

                        $callIndex++;
                    }
                }

                $constructorArguments = $dependency->getConstructorArguments();
                if ($constructorArguments) {
                    $argumentIndex = 1;

                    foreach ($constructorArguments as $argument) {
                        $output .= '$a' . $argumentIndex . ' = new DependencyCallArgument(';
                        $output .= self::getArgumentValue($argument->getType()) . ', ';
                        $output .= self::getArgumentValue($argument->getValue()) . ', ';
                        $output .= self::getArgumentValue($argument->getDependencyId()) . ");\n";
                        $argumentIndex++;
                    }

                    $output .= '$c' . $callIndex . " = new DependencyCall('__construct');\n";

                    for ($i = 1; $i < $argumentIndex; $i++) {
                        $output .= '$c' . $callIndex . '->addArgument($a' . $i . ");\n";
                    }

                    $callIndex++;
                }

                $output .= '$d = new Dependency(';
                $output .= self::getArgumentValue($dependency->getClassName()) . ', ';
                $output .= self::getArgumentValue($dependency->getId()) . ");\n";

                for ($i = 1; $i < $callIndex; $i++) {
                    $output .= '$d->addCall($c' . $i . ");\n";
                }

                $output .= '$container->addDependency(';
                $output .= self::getArgumentValue($interface) . ', ';
                $output .= '$d);';
                $output .= "\n\n";
            }
        }

        return $output;
    }

    /**
     * Gets the PHP syntax of the provided value
     * @param mixed $value
     * @return string
     */
    private static function getArgumentValue($value) {
        if ($value === null) {
            return 'null';
        }

        return "'" . $value . "'";
    }

}