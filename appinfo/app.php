<?php

OC::$CLASSPATH['OC\User\Kronos'] = 'kronos/lib/kronosuser.php';
OC::$CLASSPATH['OC\Group\Kronos'] = 'kronos/lib/kronosgroup.php';

require_once('custom/kronos/lib/kronosuser.php');
require_once('custom/kronos/lib/kronosgroup.php');

$oc_config = OC::$server->getConfig();

// register Kronos User Backend
OC::$server->getUserManager()->registerBackend(
  new OC_User_Kronos(
    $oc_config->getSystemValue("kronos_user"),
    $oc_config->getSystemValue("kronos_password"),
    $oc_config->getSystemValue("kronos_database"),
    $oc_config->getSystemValue("kronos_hostname")));

// register Kronos Group Backend
OC::$server->getGroupManager()->addBackend(
  new OC_Group_Kronos(
    $oc_config->getSystemValue("kronos_user"),
    $oc_config->getSystemValue("kronos_password"),
    $oc_config->getSystemValue("kronos_database"),
    $oc_config->getSystemValue("kronos_hostname")));

