<?php
/**
 * A base controller that provides clever model 
 * loading, view loading and layout support.
 *
 * @package CodeIgniter
 * @subpackage MY_Controller
 * @link http://github.com/jamierumbelow/codeigniter-base-controller
 * @version 1.1.1
 * @author Jamie Rumbelow <http://jamierumbelow.net>
 * @copyright Copyright (c) 2009, Jamie Rumbelow <http://jamierumbelow.net>
 */

class MY_Controller extends CI_Controller {
	
	/**
	 * The view to load, only set if you want
	 * to bypass the autoload magic.
	 *
	 * @var string
	 */
	protected $view;
	
	/**
	 * The data to pass to the view, where
	 * the keys are the names of the variables
	 * and the values are the values.
	 *
	 * @var array
	 */
	protected $data = array();
	
	/**
	 * The layout to load the view into. Only
	 * set if you want to bypass the magic.
	 *
	 * @var string
	 */
	protected $layout;
	
	/**
	 * An array of asides. The key is the name
	 * to reference by and the value is the file.
	 * The class will loop through these, parse them 
	 * and push them via a variable to the layout. 
	 * 
	 * This allows any number of asides like sidebars,
	 * footers etc. 
	 *
	 * @var array
	 * @since 1.1.0
	 */
	protected $asides = array();
	
	/**
	 * The directory to store partials in.
	 *
	 * @var string
	 */
	protected $partial = 'partials';
	
	/**
	 * The models to load into the controller.
	 *
	 * @var array
	 */
	protected $models = array();
	
	/**
	 * The model name formatting string. Use the
	 * % symbol, which will be replaced with the model
	 * name. This allows you to use model names like
	 * m_model, model_m or model_model_m. Do whatever
	 * suits you.
	 *
	 * @since 1.2.0
	 * @var string
	 */
	protected $model_string = '%_model';
	
	/**
	 * The prerendered data for output buffering
	 * and the render() method. Generally left blank.
	 *
	 * @since 1.1.1
	 * @var string
	 */
	protected $prerendered_data = '';
	
	/**
	 * The class constructor, loads the models
	 * from the $this->models array.
	 *
	 * Can't extend the default controller as it
	 * can't load the default libraries due to __get()
	 *
	 * @author Jamie Rumbelow
	 */
	public function __construct() {
		parent::__construct();
		$this->_load_models();
	}
	
	/**
	 * Called by CodeIgniter instead of the action
	 * directly, automatically loads the views.
	 *
	 * @param string $method The method to call
	 * @return void
	 * @author Jamie Rumbelow
	 */
	public function _remap($method) {
		if (method_exists($this, $method)) {
			call_user_func_array(array($this, $method), array_slice($this->uri->rsegments, 2));
		} else {
			if (method_exists($this, '_404')) {
				call_user_func_array(array($this, '_404'), array($method));	
			} else {
				show_404(strtolower(get_class($this)).'/'.$method);
			}
		}
		
		$this->_load_view();
	}
	
	/**
	 * Loads the view by figuring out the
	 * controller, action and conventional routing.
	 * Also takes into account $this->view, $this->layout
	 * and $this->sidebar.
	 *
	 * @return void
	 * @access private
	 * @author Jamie Rumbelow
	 */
	private function _load_view() {
		if ($this->view !== FALSE) {
			$view = ($this->view !== null) ? $this->view . '.php' : $this->router->directory . $this->router->class . '/' . $this->router->method . '.php';
	
			$data['yield'] =  $this->prerendered_data;
			$data['yield'] .= $this->load->view($view, $this->data, TRUE);
			
			if (!empty($this->asides)) {
				foreach ($this->asides as $name => $file) {
					$data['yield_'.$name] = $this->load->view($file, $this->data, TRUE);
				}
			}
			
			$data = array_merge($this->data, $data);
			
			if (!isset($this->layout)) {
				if (file_exists(APPPATH . 'views/layouts/' . $this->router->class . '.php')) {
					$this->load->view('layouts/' . $this->router->class . '.php', $data);
				} else {
				  $this->load->view('layouts/application.php', $data);
				}
			} else if ($this->layout !== FALSE) {
				$this->load->view('layouts/' . $this->layout . '.php', $data);
			} else {
				$this->output->set_output($data['yield']);
			}
		}
	}
	
	/**
	 * Loads the models from the $this->model array.
	 *
	 * @return void
	 * @author Jamie Rumbelow
	 */
	private function _load_models() {
	  foreach ($this->models as $model) {
	    $this->load->model($this->_model_name($model), $model, TRUE);
	  }
	}
	
	/**
	 * Returns the correct model name to load with, by
	 * replacing the % symbol in $this->model_string.
	 *
	 * @param string $model The name of the model
	 * @return string
	 * @since 1.2.0
	 * @author Jamie Rumbelow
	 */
	protected function _model_name($model) {
		return str_replace('%', $model, $this->model_string);
	}
	
	/**
	 * A helper method for controller actions to stop
	 * from loading any views.
	 *
	 * @return void
	 * @author Jamie Rumbelow
	 */
	protected function _pass() {
		$this->view = FALSE;
	}
	
	/**
	 * A helper method to check if a request has been
	 * made through XMLHttpRequest (AJAX) or not 
	 *
	 * @return bool
	 * @author Jamie Rumbelow
	 */
	protected function is_ajax() {
		return ($this->input->server('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest') ? TRUE : FALSE;
	}
	
	/**
	 * Renders the current view and adds it to the 
	 * output buffer. Useful for rendering more than one
	 * view at once.
	 *
	 * @return void
	 * @since 1.0.5
	 * @author Jamie Rumbelow
	 */
	protected function render() {
	  $this->prerendered_data .= $this->load->view($this->view, $this->data, TRUE);
	}
	
	/**
	 * Partial rendering method, generally called via the helper.
	 * renders partials and returns the result. Pass it an optional 
	 * data array and an optional loop boolean to loop through a collection.
	 *
	 * @param string $name The partial name
	 * @param array $data The data or collection to pass through
	 * @param boolean $loop Whether or not to loop through a collection
	 * @return string
	 * @since 1.1.0
	 * @author Jamie Rumbelow and Jeremy Gimbel
	 */
	public function partial($name, $data = null, $loop = TRUE) {
		$partial = '';
		$name = $this->partial . '/' . $name;
		
		if (!isset($data)) {
			$partial = $this->load->view($name, array(), TRUE);
		} else {
			if ($loop == TRUE) {
				foreach ($data as $row) {
					$partial.= $this->load->view($name, (array)$row, TRUE);
				}
			} else {
				$partial.= $this->load->view($name, $data, TRUE);
			}
		}
		
		return $partial;
	}
	
}

/**
 * Partial rendering helper method, renders partials
 * and returns the result. Pass it an optional data array
 * and an optional loop boolean to loop through a collection.  
 * 
 * NOTE FROM JEREMY: If you are a 'elitist bastard' feel free
 * 					 to chuck this in a helper, but we really
 *					 don't care, because Jamie's Chieftain.
 *
 * @param string $name The partial name
 * @param array $data The data or collection to pass through
 * @param boolean $loop Whether or not to loop through a collection
 * @return string
 * @since 1.1.0
 * @author Jamie Rumbelow and Jeremy Gimbel
 */
function partial($name, $data = null, $loop = TRUE) {
	$ci =& get_instance();
	return $ci->partial($name, $data, $loop);
}