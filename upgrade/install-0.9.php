<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_0_9($object)
{
	Configuration::updateValue('MODULOBANNER_NB_CACHES', 20);
	return ($object->registerHook('displayLeftColumn') && $object->registerHook('displayRightColumn') && $object->registerHook('displayTopColumn') && $object->registerHook('displayFooter'));
}
