<?php

namespace CanadaDrives\Integration;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\ErrorHandler;
use Bramus\Monolog\Formatter\ColoredLineFormatter;


class LogHandler {

    public static function getLogger($log_file_path) {

        // initialized logger
        $logger = new Logger(getenv('APP_ENV'));
        ErrorHandler::register($logger);

        // set up file logging
        $file_handler = new StreamHandler($log_file_path, Logger::DEBUG);
        $file_formatter = new LineFormatter(null, null, false, true);
        $file_handler->setFormatter($file_formatter);
        $logger->pushHandler($file_handler);

        // set up papertrail logging
        if (getenv('PAPERTRAIL_LOG') == 'true') {
            $papertrail_formatter = new ColoredLineFormatter(null, "%channel%.%level_name%: %message% %context%");
            $papertrail_handler = new SyslogUdpHandler(
                getenv('PAPERTRAIL_URL'),
                getenv('PAPERTRAIL_PORT'),
                LOG_USER,
                Logger::INFO,
                true,
                getenv('PAPERTRAIL_LOG_CHANNEL')
            );
            $papertrail_handler->setFormatter($papertrail_formatter);
            $logger->pushHandler($papertrail_handler);
        }

        return $logger;

    }

    public static function handleException($logger, $e, $sfCustomerId) {
        if (isset($logger) && $logger instanceof Monolog\Logger) {
            $logger->error("Request: $sfCustomerId", [
                'ip' => isset($_SERVER['X_FORWARDED_FOR']) ? $_SERVER['X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'],
                'http_referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "",
                'error' => $e->getMessage(),
            ]);
            $logger->debug('ENVIRONMENT', array_merge($_SERVER, $_GET));
        }
    }

}
