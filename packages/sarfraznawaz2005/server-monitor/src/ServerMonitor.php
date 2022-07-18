<?php
/**
 * Created by PhpStorm.
 * User: Sarfraz
 * Date: 7/11/2019
 * Time: 1:38 PM
 */

namespace Sarfraznawaz2005\ServerMonitor;

use Carbon\Carbon;

class ServerMonitor
{
    public $cacheFile;

    public function __construct()
    {
        $this->cacheFile = storage_path('servermonitor.cache');
    }

    /**
     * Checks if current application environment is production.
     *
     * @return bool
     */
    protected function isProduction(): bool
    {
        $envs = config('server-monitor.production_environments');

        return in_array(config('app.env'), $envs, true);
    }

    /**
     * Returns all checks that need to be run.
     *
     * @return array
     */
    public function getCheckClasses(): array
    {
        $key = 'application.' . config('app.env');
        $env = $this->isProduction() ? 'production' : 'development';

        $serverChecks['server'] = config('server-monitor.checks.server');
        $commonChecks['application.common'] = config('server-monitor.checks.application.common');
        $envChecks[$key] = config("server-monitor.checks.application.$env");

        return array_filter(array_merge($serverChecks, $commonChecks, $envChecks));
    }

    /**
     * Runs all checks and returns results.
     *
     * @return array
     * @throws \ReflectionException
     */
    public function runChecks(): array
    {
        $results = [];

        $checksAll = $this->getCheckClasses();

        $isConsole = app()->runningInConsole();
        $via = $isConsole ? 'Console' : 'Web Interface';

        $totalChecksCount = 0;
        $passedChecksCount = 0;
        foreach ($checksAll as $type => $checks) {
            if ($checks) {
                foreach ($checks as $check => $config) {

                    if (!is_array($config)) {
                        $check = $config;
                        $config = [];
                    }

                    if ($isConsole && isset($config['web_only'])) {
                        continue;
                    }

                    $totalChecksCount++;

                    $sTime = microtime(true);
                    $object = app()->make($check);

                    try {
                        $status = $object->check($config);
                        $error = $object->message();
                    } catch (\Exception $e) {
                        $status = false;
                        $error = $object->message();
                    }

                    $eTime = number_format((microtime(true) - $sTime) * 1000, 2);

                    if ($status) {
                        $passedChecksCount++;
                    } else {
                        Notifier::notify($object, $config);
                    }

                    $results[] = [
                        'type' => $type,
                        'checker' => getCheckerClassName($check),
                        'name' => getCheckerName($check, $config),
                        'status' => $status,
                        'error' => $error,
                        'time' => sprintf('%dms', $eTime),
                    ];
                }
            }
        }

        $results = collect($results)->groupBy('type')->toArray();

        $results['counts'] = [
            'total_checks_count' => $totalChecksCount,
            'passed_checks_count' => $passedChecksCount,
            'failed_checks_count' => $totalChecksCount - $passedChecksCount,
        ];

        $results['via'] = $via;

        @file_put_contents($this->cacheFile, serialize($results));

        return $results;
    }

    /**
     * Runs given single check and returns result.
     *
     * @param $checkClass
     * @return array
     * @throws \ReflectionException
     */
    public function runCheck($checkClass): array
    {
        $checksAll = $this->getCheckClasses();

        foreach ($checksAll as $type => $checks) {
            if ($checks) {
                foreach ($checks as $check => $config) {

                    if (!is_array($config)) {
                        $check = $config;
                        $config = [];
                    }

                    if ($checkClass === getCheckerClassName($check)) {
                        $sTime = microtime(true);
                        $object = app()->make($check);
                        $status = $object->check($config);
                        $error = $object->message();
                        $eTime = number_format((microtime(true) - $sTime) * 1000, 2);

                        return [
                            'type' => $type,
                            'checker' => $check,
                            'name' => getCheckerName($check, $config),
                            'status' => $status,
                            'error' => $error,
                            'time' => sprintf('%dms', $eTime),
                        ];
                    }
                }
            }
        }

        throw new \InvalidArgumentException("$checkClass not found!");
    }

    /**
     * Returns check results from cache file or optionally run and return new check results
     *
     * @return array
     */
    public function getChecks(): array
    {
        if (!file_exists($this->cacheFile)) {
            return [];
        }

        return unserialize(file_get_contents($this->cacheFile));
    }

    /**
     * Returns last check-run time
     *
     * @return string
     */
    public function getLastCheckedTime(): string
    {
        if (!file_exists($this->cacheFile)) {
            return 'N/A';
        }

        return Carbon::parse(date('F d Y H:i:s.', filemtime($this->cacheFile)))->diffForHumans();
    }
}
