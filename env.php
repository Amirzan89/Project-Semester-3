<?php
// load_env.php

// Path to the .env file
$envFilePath = __DIR__ . '.env';

// Load and parse the .env file
if (file_exists($envFilePath)) {
    $envContent = file_get_contents($envFilePath);
    $envLines = explode(PHP_EOL, $envContent);

    foreach ($envLines as $line) {
        $line = trim($line);
        if ($line && strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Set the environment variable if it's not already defined
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
            }
        }
    }
}
?>