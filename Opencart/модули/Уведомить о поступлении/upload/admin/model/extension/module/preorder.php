<?php
class ModelExtensionModulePreOrder extends Model {
	
	public function deletePreOrder($preorder_id) {	
		$this->db->query("DELETE FROM " . DB_PREFIX . "preorder WHERE preorder_id = '" . (int)$preorder_id . "'");
	}
		
	public function getPreOrders($data = array()) {
		$sql = "SELECT pa.preorder_id, pa.product_id, pa.email, pa.phone, pa.date_added, pa.status, pa.product_option, pd.name, p.quantity FROM " . DB_PREFIX . "preorder pa LEFT JOIN " . DB_PREFIX . "product p ON (pa.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (pa.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
			$sort_data = array(			
				'pd.name',
				'pa.email',
				'pa.phone',
				'pa.status',
				'pa.date_added'
			);		
		
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY pa.date_added";	
			}
	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
		
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}		

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}	
			
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}	
			
			$query = $this->db->query($sql);
			
			return $query->rows;
	}
	
	public function getTotalPreOrders() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "preorder");
		
		return $query->row['total'];
	}

	public function createDatabaseTables() {
		$sql  = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "preorder` ( ";
		$sql .= "`preorder_id` int(11) NOT NULL AUTO_INCREMENT, ";
		$sql .= "`product_id` int(11) NOT NULL, ";
		$sql .= "`product_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`product_option` TEXT COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`store_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";	
		$sql .= "`store_url` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`email` varchar(96) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`status` tinyint(1) NOT NULL, ";
		$sql .= "`date_added` date NOT NULL DEFAULT '0000-00-00', ";
		$sql .= "PRIMARY KEY (`preorder_id`) ";
		$sql .= ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
		$this->db->query($sql);
		
		// phone
		$query = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "preorder");

		$phone = false;

		if ($query->rows) {
			
			foreach ($query->rows as $row) {
				if ($row['Field'] == 'phone') {
					$phone = true;
				}
			}

			if (!$phone) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "preorder`  ADD `phone`  varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '';");
			}
		}
	}
	
	public function getPreOrder($preorder_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "preorder WHERE preorder_id = '" . (int)$preorder_id . "'");
		
		if ($query->num_rows) {
			return array(
				'preorder_id' => $query->row['preorder_id'],
				'product_id'       => $query->row['product_id'],
				'product_name'     => $query->row['product_name'],
				'product_option'   => unserialize($query->row['product_option']),
				'email'      	   => $query->row['email'],
				'phone'      	   => $query->row['phone'],
				'status'      	   => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'store_name'       => $query->row['store_name'],
				'store_url'        => $query->row['store_url']
			);
		} else {
			return false;
		}
	}
	
	public function editPreOrder($preorder_id) {
		$preorder = $this->getPreOrder($preorder_id);

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($preorder['product_id']);
		
		if ($preorder && $product_info && $product_info['quantity']) {
			$this->load->model('catalog/option');
			$this->load->language('mail/preorder');
			
			$option_quantity = true;
			$product_options = array();
			
			if ($preorder['product_option']) {
				
				foreach ($this->model_catalog_product->getProductOptions($preorder['product_id']) as $product_option) {
					
					$product_option_value_data = array();
					
					foreach ($product_option['product_option_value'] as $product_option_value) {
						
						$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);
						
						$product_option_value_data[] = array(
							'product_option_value_id' => $product_option_value['product_option_value_id'],
							'quantity' 				  => $product_option_value['quantity'],
							'name'                    => $option_value_info['name'],
						);
					}
					
					$product_options[] = array(
						'product_option_id'    => $product_option['product_option_id'],
						'product_option_value' => $product_option_value_data,
						'name'                 => $product_option['name']
					);
				}
			
				foreach ($preorder['product_option'] as $key => $option) {
					if (strpbrk($option, ',')) {
						$preorder['product_option'][$key] = explode(',', $option);
					}
				}
						
				foreach ($product_options as $product_option) {
					if (isset($preorder['product_option'][$product_option['product_option_id']])) {
						
						foreach ($product_option['product_option_value'] as $product_option_value) {
							if (!is_array($preorder['product_option'][$product_option['product_option_id']])) {
								if ($preorder['product_option'][$product_option['product_option_id']] == $product_option_value['product_option_value_id']) {
									if (!$product_option_value['quantity']) {
										$option_quantity = false;
										break 2;
									}
								}
							} else {
								foreach ($preorder['product_option'][$product_option['product_option_id']] as $option_value) {
									if ($option_value == $product_option_value['product_option_value_id']) {
										if (!$product_option_value['quantity']) {
											$option_quantity = false;
											break 3;
										}
									}
								}					
							}
						}
					}
				}
			}
			
			if ($option_quantity) {
				
				$this->db->query("UPDATE " . DB_PREFIX . "preorder SET status = '1' WHERE preorder_id = '" . (int)$preorder_id . "' AND status = '0'");
				
				if ($preorder['email'] && $this->config->get('module_preorder_email')) {
					$subject = sprintf($this->language->get('text_subject'), html_entity_decode($preorder['store_name'], ENT_QUOTES, 'UTF-8'));
					
					$query = $this->db->query("SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$preorder['product_id'] . "'");
					
					if ($query->row) {
						$url_alias = $query->row['keyword'];
					} else {
						$url_alias = "index.php?route=product/product&product_id=" . $preorder['product_id'];
					}
					
					// HTML Mail
					$data = array();
	
					$data['title'] = sprintf($this->language->get('text_subject'), html_entity_decode($preorder['store_name'], ENT_QUOTES, 'UTF-8'));
					
					$data['logo'] = HTTP_CATALOG . 'image/' . $this->config->get('config_logo');
					$data['store_name'] = $preorder['store_name'];
					$data['store_url'] = $preorder['store_url'];
	
					$data['text_header'] = sprintf($this->language->get('text_header'), $preorder['product_name']);
					$data['text_option'] = $this->language->get('text_option');
					$data['product_options'] = $product_options;
					$data['preorder_product_option'] = $preorder['product_option'];
					$data['text_link'] = $this->language->get('text_link');
					$data['link'] = $preorder['store_url'] . $url_alias;
					
					
					
					// Text Mail
					$text  = sprintf($this->language->get('text_header'), $preorder['product_name']) . "\n\n";
					
					if ($preorder['product_option']) {
						$text .= $this->language->get('text_option') . "\n";
						
						foreach ($product_options as $product_option) {
							
							if (isset($preorder['product_option'][$product_option['product_option_id']])) {
								
								$text .= "\n" . $product_option['name'] . ': ';
								
								$i = 0;
								
								foreach ($product_option['product_option_value'] as $product_option_value) {
									if (!is_array($preorder['product_option'][$product_option['product_option_id']])) {
										if ($preorder['product_option'][$product_option['product_option_id']] == $product_option_value['product_option_value_id']) {
											$text .= ($i ? ', ' : '') . $product_option_value['name'];
										}
									} else {
										foreach ($preorder['product_option'][$product_option['product_option_id']] as $option_value) {
											if ($option_value == $product_option_value['product_option_value_id']) {
												$text .= ($i ? ', ' : '') . $product_option_value['name'];
											}
										}					
									}
								}
							}
						}
						
						$text .= "\n\n";
					}
					
					$text .= $this->language->get('text_link') . " " . $preorder['store_url'] . $url_alias . "\n\n";
					
					$mail = new Mail();
					$mail->protocol = $this->config->get('config_mail_protocol');
					$mail->parameter = $this->config->get('config_mail_parameter');
					$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
					$mail->smtp_username = $this->config->get('config_mail_smtp_username');
					$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
					$mail->smtp_port = $this->config->get('config_mail_smtp_port');
					$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

					$mail->setTo($preorder['email']);
					$mail->setFrom($this->config->get('config_email'));
					$mail->setSender(html_entity_decode($preorder['store_name'], ENT_QUOTES, 'UTF-8'));
					$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
					$mail->setText($text);
					$mail->setHtml($this->load->view('mail/preorder', $data));
					$mail->send();
				}
				
				if ($preorder['phone'] && $this->config->get('module_preorder_sender') && $this->config->get('module_preorder_api_key') && $this->config->get('module_preorder_phone')) {
					
					$text  = sprintf($this->language->get('text_subject'), $preorder['product_name']) . "\n";
					$text .= $this->language->get('text_link') . " " . $preorder['store_url'] . "index.php?route=product/product&product_id=" . $preorder['product_id'] . "\n\n";
					
					$param = array(
						"api_id"	 =>	$this->config->get('module_preorder_api_key'),
						"to"		 =>	$preorder['phone'],
						"text"		 =>	$text,
						"from"		 =>	$this->config->get('module_preorder_sender')
					);
					
					$ch = curl_init("http://sms.ru/sms/send");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
					$result = curl_exec($ch);
					curl_close($ch);
				}
			}
		}
	}
	
	public function editPreOrders() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "preorder pa LEFT JOIN " . DB_PREFIX . "product p ON (pa.product_id = p.product_id) WHERE p.quantity > '0' AND pa.status = '0'");
		
		foreach ($query->rows as $result) {
			$this->editPreOrder($result['preorder_id']);
		}
	}
	
	public function editPreOrderProduct($product_id) {
		$query = $this->db->query("SELECT preorder_id FROM " . DB_PREFIX . "preorder WHERE product_id = " . (int)$product_id . " AND status = '0'");
		
		foreach ($query->rows as $result) {
			$this->editPreOrder($result['preorder_id']);
		}
	}
}