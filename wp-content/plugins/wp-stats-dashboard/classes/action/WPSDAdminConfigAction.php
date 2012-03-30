<?php
/**
 * WPSDAdminConfigAction
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 0.3
 * @package wp-stats-dashboard
 */
class WPSDAdminConfigAction extends WPSDWPPlugin{

	/**
	 * @var WPSDAdminConfigForm
	 */
	var $adminConfigForm = null;
	
	/**
	 * @var WPSDAdminConfigMetricsForm
	 */
	var $adminConfigMetricsForm = null;
	
	/**
	 * __construct()
	 */
	function WPSDAdminConfigAction($plugin_name, $plugin_base){
		$this->plugin_name = $plugin_name;
		$this->plugin_base = $plugin_base;
		$this->adminConfigForm = new WPSDAdminConfigForm();
		$this->adminConfigMetricsForm = new WPSDAdminConfigMetricsForm();
	}

	/**
	 * Render form.
	 */
	function render(){
		$this->render_admin('admin_config', array(
				'form' => $this->adminConfigForm,
				'form2' => $this->adminConfigMetricsForm,
				'plugin_base_url' => $this->url(),
				'plugin_name' => $this->plugin_name
			)
		);
	}
}