<?php

namespace Hennig\Common;

use Hennig\Builder\Jsonable;

class Rpc
{
    /** @var IAuth */
    static $authClass;

    /**
     * Init the Rpc, must get in an Auth object
     *
     * @param IAuth $authClass
     */
    static public function init(IAuth $authClass)
    {
        static::$authClass = $authClass;

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Mon, 10 Aug 1979 05:00:00 GMT");
        header("Content-Type: text/javascript; charset=utf-8", true);
        ob_implicit_flush(true);
        if (Config::get('app_gzip') && strpos($_SERVER['HTTP_ACCEPT_ENCODING'] ?? '', 'gzip') !== false) {
            header("Content-Encoding: gzip");
            ob_start("ob_gzhandler");
        }
    }

    /**
     * Send this method in stream way
     *
     * @param string $name
     * @param array $params
     */
    static public function method($name, $params = [])
    {
        // Need to close before any output
        if (!headers_sent()) session_write_close();
        echo \json_encode(['method' => $name, 'params' => $params]);
        ob_flush();
        usleep(100000);
    }

    /**
     * Handle the request
     */
    static public function handle()
    {
        list($inputClass, $inputMethod) = explode('@', $_SERVER['QUERY_STRING'] ?? '');
        $class = $inputClass;
        if (!class_exists($class)) {
            $class = "\\App\\Controller\\{$inputClass}Controller";

            if (!class_exists($class)) {
                $class = "\\App\\{$inputClass}";

                if (!class_exists($class)) {
                    $object = new class($inputClass)
                    {
                        private $class;

                        public function __construct($class)
                        {
                            $this->class = $class;
                        }

                        public function __call($name, $arguments)
                        {
                            throw new \Exception("$this->class not found.");
                        }
                    };
                }
            }
        }

        if (empty($object)) $object = new $class();
        $request = @json_decode(file_get_contents('php://input'), true);
        if (empty($request)) {
            $request = @json_decode(file_get_contents('php://stdin'), true);
        }

        try {
            $params = $request['params'] ?? [];
            static::checkPermission($object);
            $result = call_user_func_array([$object, $request['method'] ?? $inputMethod], $params);

            if ($result instanceof Jsonable) {
                $result = $result->toJson();
            }

            if ($result instanceof \Iterator) {
                $result = iterator_to_array($result);
            }

            $response = [
                'result' => $result,
                'error' => null
            ];
        } catch (\Exception $exception) {
            $response = ErrorHandling::output($exception, ErrorHandling::ARRAY);

            try {
                if (!($exception instanceof ESimple)) {
                    Common::slack($exception, 'bugs');
                }
            } catch (\Exception $e) {
                //@todo
            }
        }

        if (!headers_sent()) session_write_close();
        echo \json_encode($response);
        ob_flush();
    }

    /**
     * @param $object
     * @throws \Exception
     */
    static public function checkPermission($object)
    {
        $uses = class_uses($object);
        if (in_array(\Hennig\Common\AllowGuest::class, $uses)) {
            return;
        }

        if (static::$authClass::check()) {
            Session::set('user_id', static::$authClass::id());
        } else {
            Session::set('user_id', '');
        }

        if (!Session::get('user_id')) {
            throw new ESimple('User not logged.');
        }

        if (in_array(\Hennig\Common\AllowUser::class, $uses)) {
            return;
        }

        if (!Session::get('user_level')) {
            throw new ESimple('Privilegies required.');
        }
    }
}