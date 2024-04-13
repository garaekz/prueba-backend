<?php

namespace Garaekz\Bootstrap;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidFileException;
use Illuminate\Support\Env;

/**
 * Load environment variables from .env file.
 * 
 * Based on Laravel's LoadEnvironmentVariables class.
 */
class LoadEnvironmentVariables
{
    /**
     * LoadEnvironmentVariables constructor.
     *
     * @param  string  $filePath
     * @param  string|null  $fileName
     * @return void
     */
    public function __construct(protected $filePath, protected $fileName = null)
    {}

    /**
     * Setup the environment variables or fail silently.
     *
     * @return void
     */
    public function bootstrap()
    {
        try {
            $this->createDotenv()->safeLoad();
        } catch (InvalidFileException $e) {
            $this->writeErrorAndDie([
                'The environment file is invalid!',
                $e->getMessage(),
            ]);
        }
    }

    /**
     * Create a Dotenv instance.
     *
     * @return \Dotenv\Dotenv
     */
    protected function createDotenv()
    {
        return Dotenv::create(
            Env::getRepository(),
            $this->filePath,
            $this->fileName
        );
    }

    /**
     * Write the error information to the screen and exit.
     *
     * @param  string[]  $errors
     * @return void
     */
    /**
     * Write the error information to the screen and exit.
     *
     * @param  string[]  $errors
     * @return void
     */
    protected function writeErrorAndDie(array $errors)
    {
        foreach ($errors as $error) {
            echo $error . PHP_EOL;
        }

        exit(1);
    }
}