<?php 
/* ************************************************ */
// NOTE : Do not delete this file
/* ************************************************ */

ini_set("display_errors",true);
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);
$obj = $bootstrap->getObjectManager();
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$result = $obj->get('\FS\Services\Cron\AutoInvoice')->execute();
?>