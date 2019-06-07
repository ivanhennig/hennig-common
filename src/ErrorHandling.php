<?php

namespace Hennig\Common;

class ErrorHandling
{
    /**
     * Init error handling
     *
     */
    static public function init()
    {
        set_time_limit(0);
        ini_set('display_errors', 'On');
        $error_handler_level = E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_STRICT;
        error_reporting($error_handler_level);
        // Transform errors into Exceptions
        set_error_handler(function ($code, $message, $file, $line) {
            if ($code === E_DEPRECATED || $code === E_NOTICE || error_reporting() == 0) {
                return false;
            }

            throw new \ErrorException("$message", 0, $code, $file, $line);
        }, $error_handler_level);

        set_exception_handler(function ($exception) {
            $debug = Config::env('DEBUG', false);
            /** @var \Exception $exception */
            if (php_sapi_name() === 'cli') {
                $msg = "{$exception->getMessage()}";
                if ($exception->getCode()) {
                    $msg .= " ({$exception->getCode()})";
                }

                if ($debug) {
                    $msg .= ", {$exception->getFile()} ({$exception->getLine()})";
                    echo "Exception: $msg" . PHP_EOL;

                    foreach (static::trace($exception) as $trace) {
                        echo $trace . PHP_EOL;
                    }

                    return;
                }

                echo "Exception: $msg" . PHP_EOL;
                return;
            }

            $msg = array();
            $msg['message'] = html_entity_decode($exception->getMessage());
            if ($exception->getCode() > 0) {
                $msg['code'] = $exception->getCode();
            }

            if ($debug) {
                $msg['trace'] = iterator_to_array(static::trace($exception));
            }

            echo json_encode([
                'result' => null,
                'error' => $msg
            ]);
        });
    }

    /**
     * Helper function to show the trace
     *
     * @param \Exception|null $exception
     * @return \Generator
     */
    static public function trace($exception = null)
    {
        foreach (empty($exception) ? debug_backtrace(2) : $exception->getTrace() as $traceItem) {
            $file = $traceItem['file'] ?? $traceItem['class'];
            $line = empty($traceItem['line']) ? '' : ":{$traceItem['line']}";
            if (strpos($file, '/vendor/') !== false) {//Don't show vendor files
                continue;
            }

            $where = str_replace([BASE_DIR, '.php'], '', $file);
            yield "{$where}@{$traceItem['function']}{$line}";
        }
    }
}