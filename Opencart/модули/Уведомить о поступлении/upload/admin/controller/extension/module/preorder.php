<?php
class ControllerExtensionModulePreOrder extends Controller {
	private $error = array(); 
	
	public function index() {  
		$this->load->language('extension/module/preorder');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_preorder', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			if ($this->request->post['module_preorder_apply']) {
				$this->response->redirect($this->url->link('extension/module/preorder', 'user_token=' . $this->session->data['user_token'], true));
			}

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}
		
		$data = array();
		
		$data = $this->getList();
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['button_catalog'])) {
			$data['error_button_catalog'] = $this->error['button_catalog'];
		} else {
			$data['error_button_catalog'] = '';
		}
		
		if (isset($this->error['button_product'])) {
			$data['error_button_product'] = $this->error['button_product'];
		} else {
			$data['error_button_product'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
				
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/preorder', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/preorder', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		// General
		if (isset($this->request->post['module_preorder_status'])) {
			$data['module_preorder_status'] = $this->request->post['module_preorder_status'];
		} else {
			$data['module_preorder_status'] = $this->config->get('module_preorder_status');
		}
		
		if (isset($this->request->post['module_preorder_mail_alert'])) {
			$data['module_preorder_mail_alert'] = $this->request->post['module_preorder_mail_alert'];
		} else {
			$data['module_preorder_mail_alert'] = $this->config->get('module_preorder_mail_alert');
		}
		
		if (isset($this->request->post['module_preorder_product'])) {
			$data['module_preorder_product'] = $this->request->post['module_preorder_product'];
		} else {
			$data['module_preorder_product'] = $this->config->get('module_preorder_product');
		}
		
		$this->load->model('localisation/stock_status');

		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		if (isset($this->request->post['module_preorder_stock_status_id'])) {
			$data['module_preorder_stock_status_id'] = $this->request->post['module_preorder_stock_status_id'];
		} elseif ($this->config->get('module_preorder_stock_status_id')) {
			$data['module_preorder_stock_status_id'] = $this->config->get('module_preorder_stock_status_id');
		} else {
			$data['module_preorder_stock_status_id'] = 0;
		}
		
		if (isset($this->request->post['module_preorder_captcha'])) {
			$data['module_preorder_captcha'] = $this->request->post['module_preorder_captcha'];
		} else {
			$data['module_preorder_captcha'] = $this->config->get('module_preorder_captcha');
		}
		
		// Form
		if (isset($this->request->post['module_preorder_email'])) {
			$data['module_preorder_email'] = $this->request->post['module_preorder_email'];
		} else {
			$data['module_preorder_email'] =  $this->config->get('module_preorder_email');
		}
		
		if (isset($this->request->post['module_preorder_phone'])) {
			$data['module_preorder_phone'] = $this->request->post['module_preorder_phone'];
		} else {
			$data['module_preorder_phone'] =  $this->config->get('module_preorder_phone');
		}
		
		// Button
		if (isset($this->request->post['module_preorder_button_catalog'])) {
			$data['module_preorder_button_catalog'] = $this->request->post['module_preorder_button_catalog'];
		} elseif ($this->config->get('module_preorder_button_catalog')) {
			$data['module_preorder_button_catalog'] = $this->config->get('module_preorder_button_catalog');
		} else {
			$data['module_preorder_button_catalog'] =  'button-preorder-default';
		}
		
		if (isset($this->request->post['module_preorder_button_type_catalog'])) {
			$data['module_preorder_button_type_catalog'] = $this->request->post['module_preorder_button_type_catalog'];
		} else {
			$data['module_preorder_button_type_catalog'] = $this->config->get('module_preorder_button_type_catalog');
		}
		
		if (isset($this->request->post['module_preorder_button_product'])) {
			$data['module_preorder_button_product'] = $this->request->post['module_preorder_button_product'];
		} elseif ($this->config->get('module_preorder_button_product')) {
			$data['module_preorder_button_product'] = $this->config->get('module_preorder_button_product');
		} else {
			$data['module_preorder_button_product'] =  'btn btn-danger btn-lg btn-block';
		}
		
		if (isset($this->request->post['module_preorder_button_type_product'])) {
			$data['module_preorder_button_type_product'] = $this->request->post['module_preorder_button_type_product'];
		} else {
			$data['module_preorder_button_type_product'] = $this->config->get('module_preorder_button_type_product');
		}
		
		// SMS
		if (isset($this->request->post['module_preorder_api_key'])) {
			$data['module_preorder_api_key'] = $this->request->post['module_preorder_api_key'];
		} else {
			$data['module_preorder_api_key'] = $this->config->get('module_preorder_api_key');
		}
		
		if (isset($this->request->post['module_preorder_sender'])) {
			$data['module_preorder_sender'] = $this->request->post['module_preorder_sender'];
		} else {
			$data['module_preorder_sender'] = $this->config->get('module_preorder_sender');;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/preorder', $data));
	}
	
	public function delete() {
		$this->load->language('extension/module/preorder');
		$this->load->model('extension/module/preorder');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $preorder_id) {
				$this->model_extension_module_preorder->deletePreOrder($preorder_id);
			}
		
			$this->session->data['success'] = $this->language->get('text_success');
		}
		
		$url = '';
			
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		$this->response->redirect($this->url->link('extension/module/preorder', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}
	
	public function getList() {
		$this->load->model('extension/module/preorder');
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pa.date_added';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['delete'] = $this->url->link('extension/module/preorder/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['preorders'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		
		$preorder_total = $this->model_extension_module_preorder->getTotalPreOrders();
		$data['module_preorder_total'] = $preorder_total;
		
		$results = $this->model_extension_module_preorder->getPreOrders($filter_data);
		
		$this->load->model('catalog/product');
		$this->load->model('catalog/option');

    	foreach ($results as $result) {
			$product_options = array();
			
			foreach ($this->model_catalog_product->getProductOptions($result['product_id']) as $product_option) {
				$product_option_value_data = array();
				
				foreach ($product_option['product_option_value'] as $product_option_value) {
					$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);
					
					$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'name'                    => $option_value_info['name'],
						'quantity' 				  => $product_option_value['quantity']
					);
				}
				
				$product_options[] = array(
				    'product_option_id'    => $product_option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'name'                 => $product_option['name']
				);
			}
			
			$product_option = unserialize($result['product_option']);
			
			if ($product_option) {
				foreach ($product_option as $key => $option) {
					if (strpbrk($option, ',')) {
						$product_option[$key] = explode(',', $option);
					}
				}
			}
	
			$data['preorders'][] = array(
				'preorder_id'		=> $result['preorder_id'],
				'product_id'		=> $result['product_id'],
				'name'			 	=> $result['name'],
				'product_options'	=> $product_options,
				'product_option'	=> $product_option,
				'email'          	=> $result['email'],
				'phone'          	=> $result['phone'],
				'status'			=> $result['status'],
				'quantity'			=> $result['quantity'],
				'date_added' 	 	=> $result['date_added']
			);
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['sort_name'] = $this->url->link('extension/module/preorder', 'user_token=' . $this->session->data['user_token'] . '&sort=product_name' . $url, true);
		$data['sort_email'] = $this->url->link('extension/module/preorder', 'user_token=' . $this->session->data['user_token'] . '&sort=email' . $url, true);
		$data['sort_phone'] = $this->url->link('extension/module/preorder', 'user_token=' . $this->session->data['user_token'] . '&sort=phone' . $url, true);
		$data['sort_date_added'] = $this->url->link('extension/module/preorder', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);
		$data['sort_status'] = $this->url->link('extension/module/preorder', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		$data['user_token'] = $this->session->data['user_token'];

		$pagination = new Pagination();
		$pagination->total = $preorder_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/preorder', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		
		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($preorder_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($preorder_total - $this->config->get('config_limit_admin'))) ? $preorder_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $preorder_total, ceil($preorder_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		return $data;
	}
	
	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/module/preorder')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function editPreOrder() {
		if (isset($this->request->get['preorder_id'])) {
			$this->load->language('extension/module/preorder');
			$this->load->model('extension/module/preorder');
			$this->model_extension_module_preorder->editPreOrder($this->request->get['preorder_id']);
			$this->session->data['success'] = $this->language->get('text_success');
		}
	}
	
	public function editPreOrders() {
		$this->load->language('extension/module/preorder');
		$this->load->model('extension/module/preorder');
		$this->model_extension_module_preorder->editPreOrders();
		$this->session->data['success'] = $this->language->get('text_success');
	}
	
	public function editPreOrderProduct() {
		$json = array();
		
		$this->load->language('extension/module/preorder');
		
		if (!(int)$this->request->get['quantity']) {
			$json['error'] = $this->language->get('error_quantity');
		} else {
			$this->load->model('extension/module/preorder');
			$this->model_extension_module_preorder->editPreOrderProduct($this->request->get['product_id']);
			$json['success'] = $this->language->get('text_success');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/preorder')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
	
	public function install () {
		$this->load->model('extension/module/preorder');
		$this->model_extension_module_preorder->createDatabaseTables();
	}
}