<?php
/**
 * Created by PhpStorm.
 * User: Sarfraz
 * Date: 7/10/2019
 * Time: 2:39 PM
 */

namespace Sarfraznawaz2005\ServerMonitor\Checks\Application;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Sarfraznawaz2005\ServerMonitor\Checks\Check;

class UnwantedPhpExtensionsAreDisabled implements Check
{
    private $extensions;

    /**
     * Perform the actual verification of this check.
     *
     * @param array $config
     * @return bool
     */
    public function check(array $config): bool
    {
        $this->extensions = Collection::make(Arr::get($config, 'extensions', []));

        $this->extensions = $this->extensions->reject(static function ($ext) {
            return extension_loaded($ext) === false;
        });

        return $this->extensions->isEmpty();
    }

    /**
     * The error message to display in case the check does not pass.
     *
     * @return string
     */
    public function message(): string
    {
        $NL = app()->runningInConsole() ? "\n" : '<br>';

        return "The following extensions are not disabled:$NL" . $this->extensions->implode($NL);
    }
}
