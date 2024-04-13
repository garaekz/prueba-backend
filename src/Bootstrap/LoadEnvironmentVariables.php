<?php

namespace Garaekz\Bootstrap;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

class LoadEnvironmentVariables
{
    protected $filePath;
    protected $fileName;

    public function __construct($filePath, $fileName = '.env')
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
    }

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

    protected function writeErrorAndDie(array $errors)
    {
        foreach ($errors as $error) {
            echo $error . PHP_EOL;
        }
        exit(1);
    }
}
