<?php

namespace BCLibraries\LibrarianRecommender;

class Environment
{
    const ENV_FILE = __DIR__ . '/../.env';
    const LOCAL_ENV_FILE = __DIR__ . '/../.env.local';

    /**
     * @throws \Exception
     */
    public static function load() {
        $base_env = parse_ini_file(self::ENV_FILE);
        $local_env = file_exists(self::LOCAL_ENV_FILE) ? parse_ini_file(self::LOCAL_ENV_FILE) : [];

        $env_vals = array_merge($base_env, $local_env, $_ENV);
        if ($env_vals === false) {
            throw new \Exception("Error building ENV values");
        }
        return $env_vals;
    }
}
