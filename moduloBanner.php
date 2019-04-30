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



if (!defined('_PS_VERSION_')) {
    exit;
}

require_once('classes/Banner.php');

class ModuloBanner extends Module
{
    public function __construct()
    {
        $this->name = 'moduloBanner';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Javier';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
		$this->bootstrap = true;

		parent::__construct();

        $this->displayName = $this->l('Banners');
        $this->description = $this->l('mi modulo del banner');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
		
        $this->fieldImageSettings = array(
            'name' => 'image',
            'dir' => 'object',
        );
    	if (!Configuration::get('moduloBanner')) {
            $this->warning = $this->l('No name provided');
		}
	}
	
    public function install()
    {
        include(dirname(__FILE__).'\sql\install.php');
        return parent::install() &&
        $this->registerHook('displayLeftColumn') &&
        $this->registerHook('displayRightColumn') &&
        $this->registerHook('displayTopColumn') &&
        $this->registerHook('displayFooter');
	}
	

	public function getContent()
	{
		$id_banner = (int)Tools::getValue('id_banner');
		$this->html = "";
		if (Tools::isSubmit('savemoduloBanner')) {
			if ($this->processSave()) {
				return $this->html . $this->renderList();
			}
			else {
				return $this->html . $this->renderForm();
			}
		} 
		elseif (Tools::isSubmit('updatemoduloBanner') || Tools::isSubmit('addmoduloBanner')) {
			$this->html .= $this->renderForm();
			return $this->html;
		} 
		elseif (Tools::isSubmit('deletemoduloBanner')) {
			$banner = new Banner((int)$id_banner);
			$banner->delete();
			$this->_clearCache('category.tpl');
			Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.
			Tools::getAdminTokenLite('AdminModules'));
		}
		else {
			$this->html .= $this->renderList();
			return $this->html;
		}
	}
    
	protected function renderForm()
	{
		$image_size = "";
		$image_url = "";
		$fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Este es mi formulario del banner'),
			),
			'input' => array(
					'id_banner' => array(
					'type' => 'hidden',
					'name' => 'id_banner'
				),
				array(
					'type' => 'select',
					'label' => $this->l('Category'),
					'name' => 'id_category',
					'options' => array(
						'query' => Category::getAllCategoriesName(true),
						'id' => 'id_category',
						'name' => 'name'
					)
				),
				'imagen' => array(
					'type' => 'file',
					'label' => $this->l('Picture'),
					'lang' => true,
					'name' => 'imagen',
					'display_image' => true,
					'size' => $image_size,
					'image' => $image_url ? $image_url : false,
					'cols' => 40,
					'rows' => 10,
				),
				array(

					'type' => 'select',
					'lang' => true,
					'label' => $this->l('Posicion'),
					'name' => 'hook',
					'desc' => $this->l('Please Eneter Web Site URL Address.'),
					'options' => array(
						'query' => $this->arrayHooks(), // el true es que solo los que estan activos
						'id' => 'hooks',
						'name' => 'firstname',
					)
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
			),
			'buttons' => array(
				array(
					'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.
					Tools::getAdminTokenLite('AdminModules'),
					'title' => $this->l('Back to list'),
					'icon' => 'process-icon-back'
				)
			)
		);


			$helper = new HelperForm();
			$helper->module = $this;
			$helper->identifier = $this->identifier;
			$helper->token = Tools::getAdminTokenLite('AdminModules');
			$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
			$helper->toolbar_scroll = true;
			$helper->title = $this->displayName;
			$helper->submit_action = 'savemoduloBanner';
			$helper->fields_value = $this->getFormValues();

			return $helper->generateForm(array(array('form' => $fields_form)));
	}


	protected function renderList()
	{
			$this->fields_list = array();
			$this->fields_list['id_banner'] = array(
				'title' => $this->l('Id banner'),
				'type' => 'text',
				'search' => false,
				'orderby' => false,
			);
			$this->fields_list['id_category'] = array(
				'title' => $this->l('Category'),
				'type' => 'text',
				'search' => false,
				'orderby' => false,
			);

			$this->fields_list['hook'] = array(
				'title' => $this->l('Enlace'),
				'type' => 'text',
				'search' => false,
				'orderby' => false,
			);

			$helper = new HelperList();
			$helper->shopLinkType = '';
			$helper->simple_header = false;
			$helper->identifier = 'id_banner';
			$helper->actions = array('edit', 'delete');
			$helper->show_toolbar = true;
			$helper->imageType = 'jpg';
			$helper->toolbar_btn['new'] = array(
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&add'.$this->name.'&token='.
				Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Add new')
			);
			$helper->title = $this->displayName;
			$helper->table = $this->name;
			$helper->token = Tools::getAdminTokenLite('AdminModules');
			$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
			$content = $this->getListContent($this->context->language->id);
			return $helper->generateList($content, $this->fields_list);
	}

	public function processSave()
	{
			$saved = false;
			if ($id_banner = Tools::getValue('id_banner')) {
				$banner = new Banner((int)$id_banner);
				if (isset($_REQUEST['savemoduloBanner'])) {
					$banner->id_banner = Tools::getValue('id_banner');
					$banner->id_category = Tools::getValue('id_category');
					$banner->hook = Tools::getValue('hook');
					$path = dirname(__FILE__).'/img/';
					ddd($path);
					$newname = $_FILES['imagen']['name'];
					$banner->imagen = $newname;
					$target = $path.$newname;
					move_uploaded_file($_FILES['imagen']['tmp_name'], $target);
					$miBanner = $this->posiciones($banner->hook ,$banner->id_category);
						if (empty($miBanner)) {
							$saved = $banner->save();
						}
				}
		}
		else{
			$banner = new Banner((int)$id_banner);
			if (isset($_REQUEST['savemoduloBanner'])) {
				$banner->id_banner = Tools::getValue('id_banner');
				$banner->id_category = Tools::getValue('id_category');
				$banner->hook = Tools::getValue('hook');
				$path = dirname(__FILE__).'/img/';
				$newname = $_FILES['imagen']['name'];
				$banner->imagen = $newname;
				$target = $path.$newname;
				move_uploaded_file($_FILES['imagen']['tmp_name'], $target);
				$miBanner = $this->posiciones($banner->hook ,$banner->id_category);
				if (empty($miBanner)) {
					$saved = $banner->save();
				}
			}
		}
		return $saved;
	}

	
	protected function getListContent()
	{
		$banners = $this->getBanners();
		for($i=0 ; $i < count($banners); $i++) {
			$banners[$i]['id_category'] = $banners[$i]['name'];
		}
		return $banners;
	}



	
	public function getFormValues()
	{
		$fields_value = array();
		$id_banner = (int)Tools::getValue('id_banner');
		if ($id_banner) {
			$banner = new Banner((int)$id_banner);
			$fields_value['id_category'] = $banner->id_category;
			$fields_value['hook'] = $banner->hook;
			$fields_value['imagen'] = $banner->imagen;
		}
		else{
			$fields_value['id_category'] = "";
			$fields_value['hook'] = "";
			$fields_value['imagen'] = "";
		}
		$fields_value['id_banner'] = $id_banner;
		

		return $fields_value;
	}

	public function arrayHooks()
	{
	
		$hook = array();
		
		$hook[0]['hooks'] = 'arriba';
		$hook[0]['firstname'] = 'arriba';
		$hook[1]['hooks'] = 'derecha';
		$hook[1]['firstname'] = 'derecha';
		$hook[2]['hooks'] = 'abajo';
		$hook[2]['firstname'] = 'abajo';
		$hook[3]['hooks'] = 'izquierda';
		$hook[3]['firstname'] = 'izquierda';
		
		return $hook;
	}

	public function getBanners()
	{
		$sql = 'SELECT ban.`id_banner`, ban.`id_category`,ban.`hook`,ca.`name`
			FROM `'._DB_PREFIX_.'banners` ban
			LEFT JOIN `'._DB_PREFIX_.'category_lang` ca ON (ca.`id_category` = ban.`id_category`)
			WHERE `id_lang` = '.$this->context->language->id;


			return Db::getInstance()->ExecuteS($sql);
	}

	public function hookDisplayLeftColumn($params)
	{
		if (Tools::getValue('controller') == 'category') {
			$id_categoria = Tools::getValue('id_category');
			$valor = $this->posiciones('izquierda',$id_categoria);
			if (!empty($valor)) {
				$image = $valor['imagen'];
				$path = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/' .$this->name . '/views/img/'. $image;
				$this->smarty->assign(array(
					'image' => $image,
					'path' => $path
				));
				return $this->display(__FILE__,'views/templates/moduloBanner.tpl');
			}
		}
	}


	public function hookDisplayRightColumn($params)
	{
		if (Tools::getValue('controller') == 'category') {
			$id_categoria = Tools::getValue('id_category');
			$valor = $this->posiciones('derecha',$id_categoria);
			if (!empty($valor)) {
				$image = $valor['imagen'];
				$path = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/' .$this->name . '/views/img/'. $image;
				$this->smarty->assign(array(
					'image' => $image,
					'path' => $path
				));
				return $this->display(__FILE__,'views/templates/moduloBanner.tpl');
			}
		}
	}

	public function hookDisplayTopColumn($params)
	{
		if (Tools::getValue('controller') == 'category') {
			$id_categoria = Tools::getValue('id_category');
			$valor = $this->posiciones('arriba',$id_categoria);
			if (!empty($valor)) {
				$image = $valor['imagen'];
				$path = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/' .$this->name . '/views/img/'. $image;
				$this->smarty->assign(array(
					'image' => $image,
					'path' => $path
				));
				return $this->display(__FILE__,'views/templates/moduloBanner.tpl');
			}
		}
	}

	public function hookDisplayFooter($params)
	{
		if (Tools::getValue('controller') == 'category') {
			$id_categoria = Tools::getValue('id_category');
			$valor = $this->posiciones('abajo',$id_categoria);
			if (!empty($valor)) {
				$image = $valor['imagen'];
				$path = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/' .$this->name . '/views/img/'. $image;
				$this->smarty->assign(array(
					'image' => $image,
					'path' => $path
				));
				return $this->display(__FILE__,'views/templates/moduloBanner.tpl');
			}
		}
	}
	
	public function posiciones($enlace, $id_categoria)
	{
		$sql = 'SELECT `imagen`
		FROM `'._DB_PREFIX_.'banners`
		WHERE `id_category` = '.(int)$id_categoria.' AND  `hook` = "'.$enlace.'"';
		return Db::getInstance()->getRow($sql);
	}
}



