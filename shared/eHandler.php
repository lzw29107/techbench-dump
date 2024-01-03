<?php
/*
TechBench dump
Copyright (C) 2024 TechBench dump website authors and contributors

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

function argProcess($arg) {
    if(is_string($arg)) {
        if(strlen($arg) > 15) {
            $argl = substr($arg, 0, 15);
            $arg = "'$argl...'";
        } else $arg = "'$arg'";
    } else if(is_array($arg)) {
        $arg = 'Array';
    } else if(!is_int($arg)) {
        if(is_object($arg)) {
            $class = get_class($arg);
            $arg = "Object($class)";
        } else if(is_bool($arg)) {
            $arg = $arg ? 'true' : 'false';
        } else if(is_null($arg)) {
            $arg = 'NULL';
        } else $arg = gettype($arg);
    }
    return $arg;
}

//inspired from @pinkgothic on https://www.php.net/manual/function.set-exception-handler.php#98201
function exceptionHandler($exception) {

    if(is_file('dump.xml.lock')) {
        $lock = array();
        $lock['status'] = 'Exception';
        file_put_contents('dump.xml.lock', json_encode($lock));
        sleep(1);
        unlink('dump.xml.lock');
    }

    // these are our templates
    $traceline = "#%s %s(%s): %s(%s)";
    $msg = "\nFatal error: Uncaught Error: %s in %s:%s Stack trace:%s  thrown in %s on line %s";
    if(php_sapi_name() != 'cli') $msg = "<br><b>Fatal error</b>: Uncaught Error: %s in %s:%s Stack trace:%s  thrown in <b>%s</b> on line <b>%s</b>";

    // alter your trace as you please, here
    $trace = $exception->getTrace();
    foreach ($trace as $key => $stackPoint) {
        // I'm converting arguments to their type
        // (prevents passwords from ever getting logged as anything other than 'string')
        if(isset($trace[$key]['args'])) {
    $trace[$key]['args'] = array_map('argProcess', $trace[$key]['args']);
  } else $trace[$key]['args'] = array();
    }

    // build your tracelines
    $result = array();
    foreach ($trace as $key => $stackPoint) {
        $result[] = sprintf(
            $traceline,
            $key,
            $stackPoint['file'],
            $stackPoint['line'],
            $stackPoint['function'],
            implode(', ', $stackPoint['args'])
        );
    }
    // trace always ends with {main}
    if(isset($key))$result[] = '#' . ++$key . ' {main}';

    // write tracelines into main template
    $msg = sprintf(
        $msg,
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        implode("\n", $result),
        $exception->getFile(),
        $exception->getLine()
    );

    // log or echo as you please
    echo $msg;
    exit();
}

set_exception_handler('exceptionHandler');

/*
function ErrorHandler($errno, $errstr, $errfile, $errline)
{
    if(is_file('dump.xml.lock')) file_put_contents('dump.xml.lock', 'Error');
    return false;
}

set_error_handler('ErrorHandler', E_RECOVERABLE_ERROR);
*/
?>
