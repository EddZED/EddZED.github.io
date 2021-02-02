<?php  
class ControllerExtensionModulePreOrder extends Controller {
	public function index() {
		$this->load->language('extension/module/preorder');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			
			$data = array();
			
			if (isset($this->request->post['preorder_product_id'])) {
				$data['product_id'] = (int)$this->request->post['preorder_product_id'];			
			} else {
				$data['product_id'] = 0;
			}
			
			if (isset($this->request->post['preorder_email'])) {
				$data['email'] = $this->request->post['preorder_email'];			
			} else {
				$data['email'] = '';
			}
			
			if (isset($this->request->post['preorder_phone'])) {
				$data['phone'] = $this->request->post['preorder_phone'];			
			} else {
				$data['phone'] = '';
			}
			
			if (isset($this->request->post['preorder_option'])) {
				$data['option'] = serialize(array_filter($this->request->post['preorder_option']));			
			} else {
				$data['option'] = '';
			}
			
			if ($this->config->get('module_preorder_email') == 2) {
		
				if (utf8_strlen($this->request->post['preorder_email']) > 96 || !filter_var($this->request->post['preorder_email'], FILTER_VALIDATE_EMAIL)) {
					$json['error'] = $this->language->get('error_email');
				}
			}
			
			if ($this->config->get('module_preorder_phone') == 2) {
		
				if ((utf8_strlen($this->request->post['preorder_phone']) < 3) || (utf8_strlen($this->request->post['preorder_phone']) > 32)) {
					$json['error'] = $this->language->get('error_phone');
				}
			}

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && $this->config->get('module_preorder_captcha')) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error'] = $captcha;
				}
			}
			
			if (!isset($json['error'])) {
				$this->load->model('extension/module/preorder');

				$this->model_extension_module_preorder->addPreOrder($data);
				
				$json['success'] = $this->language->get('text_success');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function add() {
		$json = array();

		if (isset($this->request->post['product_id'])) {
			$product_id = (int)$this->request->post['product_id'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}
		
		if (isset($this->request->post['module_id'])) {
			if ((int)$this->request->post['module_id']) {
				$module_id = (int)$this->request->post['module_id'];
			} else {
				$module_id = 'catalog';
			}
		} else {
			$module_id = '';
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);
		
		if ($product_info) {
			
			if (isset($this->request->post['option' . $module_id])) {
				$option = array_filter($this->request->post['option' . $module_id]);	
			} else {
				$option = array();
			}
			
			$json['name'] = $product_info['name'];
			$json['option'] = $option;
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function option() {
		$json = array();
		$json['success'] = true;
		
		if (isset($this->request->post['module_id'])) {
			if ((int)$this->request->post['module_id']) {
				$module_id = (int)$this->request->post['module_id'];
			} else {
				$module_id = 'catalog';
			}
		} else {
			$module_id = '';
		}
		
		if (isset($this->request->post['option' . $module_id])) {
			$option = array_filter($this->request->post['option' . $module_id]);	
		} else {
			$option = array();
		}
		
		if (isset($this->request->post['product_id'])) {
			$product_id = (int)$this->request->post['product_id'];
		} else {
			$product_id = (int)$this->request->get['product_id'];
		}
		
		$this->load->model('catalog/product');
		
		$product_options = $this->model_catalog_product->getProductOptions($product_id);
		
		foreach ($product_options as $product_option) {
			if (!empty($option[$product_option['product_option_id']])) {
				foreach ($product_option['product_option_value'] as $option_value) {
					if ($option_value['subtract'] && ($option_value['quantity'] <= 0)) {
						if (is_array($option[$product_option['product_option_id']])) {
							foreach ($option[$product_option['product_option_id']] as $product_option_value_id) {
								if ($option_value['product_option_value_id'] == $product_option_value_id) {
									$json['success'] = false;
								}
							}
						} else {
							if ($option_value['product_option_value_id'] == $option[$product_option['product_option_id']]) {
								$json['success'] = false;
							}
						}
					}
				}
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function form() {
		$json = array();
		
		$json['preorder_status'] = $this->config->get('module_preorder_status');
					
		if ($this->config->get('module_preorder_status')) {
			$this->load->language('extension/module/preorder');
			
			$json['text_title'] = $this->language->get('text_title');
			$json['entry_name'] = $this->language->get('entry_name');
			$json['entry_email'] = $this->language->get('entry_email');
			$json['entry_phone'] = $this->language->get('entry_phone');
			$json['button_submit'] = $this->language->get('button_submit');
			
			$json['preorder_email'] = $this->config->get('module_preorder_email');
			$json['preorder_phone'] = $this->config->get('module_preorder_phone');
			
			if ($this->customer->isLogged()) {
				$json['email'] = $this->customer->getEmail();
			} else {
				$json['email'] = '';
			}
			
			if ($this->customer->isLogged()) {
				$json['phone'] = $this->customer->getTelephone();
			} else {
				$json['phone'] = '';
			}
			
			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && $this->config->get('module_preorder_captcha')) {
				$json['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$json['captcha'] = '';
			}
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}
}