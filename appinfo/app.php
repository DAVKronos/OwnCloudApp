<?php

//OC_APP::registerAdmin('kronos','settings');

//OC_User::registerBackend('KRONOS');
//OC_User::useBackend('KRONOS');

OC::$CLASSPATH['OC\User\Kronos'] = 'kronos/lib/kronosuser.php';
OC::$CLASSPATH['OC\Group\Kronos'] = 'kronos/lib/kronosgroup.php';

require_once('apps/kronos/lib/kronosuser.php');
require_once('apps/kronos/lib/kronosgroup.php');

OC_User::useBackend(new OC_User_Kronos(OC_Config::getValue("kronos_user"), OC_Config::getValue("kronos_password"), OC_Config::getValue("kronos_database")));
OC_Group::useBackend(new OC_Group_Kronos(OC_Config::getValue("kronos_user"), OC_Config::getValue("kronos_password"), OC_Config::getValue("kronos_database")));

OC::$CLASSPATH['OC\Files\Cache\KronosCache'] = 'kronos/lib/kronoscache.php';
OC::$CLASSPATH['OC\Files\Storage\Kronos'] = 'kronos/lib/kronosstorage.php';

\OCP\Util::connectHook('OC_Filesystem', 'setup', '\OC\Files\Storage\Kronos', 'setup');

\OCP\Util::connectHook('OC_User', 'post_login', '\OC_User_Kronos', 'sync');

/*$entry = array(
	'id' => "kronos_settings",
	'order' => 1,
	'href' => OC_Helper::linkTo('kronos', 'settings.php'),
	'name' => 'KRONOS'
);
?>*/
