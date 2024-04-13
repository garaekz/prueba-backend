<?php

namespace Garaekz\Bootstrap;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

/**
 * Class LoadEnvironmentVariables
 * 
 * This class is responsible for loading environment variables from a file.
 */
class LoadEnvironmentVariables
{
    /**
     * The path to the environment file.
     *
     * @var string
     */
    protected $filePath;

    /**
     * The name of the environment file.
     *
     * @var string
     */
    protected $fileName;

    /**
     * LoadEnvironmentVariables constructor.
     *
     * @param string $filePath The path to the environment file.
     * @param string $fileName The name of the environment file. Default is '.env'.
     */
    public function __construct($filePath, $fileName = '.env')
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
    }

    /**
     * Bootstrap the environment variables.
     *
     * This method loads the environment variables from the specified file.
     * If the file is missing, it writes an error message and terminates the script.
     */
    public function bootstrap()
    {
        try {
            $dotenv = Dotenv::createImmutable($this->filePath, $this->fileName);
            $dotenv->load();
        } catch (InvalidPathException $e) {
            $this->writeErrorAndDie([
                "The environment file is missing!",
                $e->getMessage(),
            ]);
        }
    }

    /**
     * Write an error message and terminate the script.
     *
     * @param array $errors An array of error messages to be displayed.
     */
    protected function writeErrorAndDie(array $errors)
    {
        foreach ($errors as $error) {
            echo $error . PHP_EOL;
        }
        exit(1);
    }
}
