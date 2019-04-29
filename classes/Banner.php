<?php
/**
* 2007-2016 Javier
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author Javier SA <jvictor@webimpacto.es>
*  @copyright  2007-2016 Javier SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Banner extends ObjectModel
{
    public $id_category;
    public $hook;
    public $id_banner;
    public $imagen;
    public $imageType;

    public static $definition = array
    (
        'table' => 'banners',
        'primary' => 'id_banner',
        'fields' => array(
            'id_category' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'hook' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 255),
            'imagen' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 255)
        ),
    );
}
