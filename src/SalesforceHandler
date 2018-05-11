<?php
namespace CanadaDrives\Integration;

use CanadaDrives\Salesforce\SalesForceMiniLib;

class SalesforceHandler
{
    public static function getSalesforceHandler($logger)
    {
        try {
            $sfEnvironment = null;
            if (getenv('APP_ENV') != "production" && !empty($_GET['salesforceUrl']) && !empty($_GET['salesforceUser'])) {
                $sfEnvironment = array(
                    'salesforceUrl' => $_GET['salesforceUrl'],
                    'salesforceUser' => $_GET['salesforceUser'],
                );
            }
            $logger->info("Salesforce Auth", !is_null($sfEnvironment) ? $sfEnvironment: array(
                'APP_ENV' => getenv('APP_ENV'),
            ));
            return new SalesForceMiniLib($sfEnvironment);
        } catch (Exception $e) {
            $logger->error($e->getMessage(), array(
                'APP_ENV' => getenv('APP_ENV'),
                'salesforceUrl' => isset($_GET['salesforceUrl']) ? $_GET['salesforceUrl'] : null,
                'salesforceUser' => isset($_GET['salesforceUser']) ? $_GET['salesforceUser'] : null,
            ));
            $logger->debug('ENVIRONMENT', array_merge($_SERVER, $_GET));
        }
    }
}