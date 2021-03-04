<?php

namespace WPH\CLI;

class Elementor {

	protected $version = '1.0.0';

	protected $elementor_min = '2.0.0';

	protected $php_min = '7.0';

	protected $dir = 'includes';

	public function __construct() {

		$this->version = $this->getVersion();
		$this->elementor_min = $this->getElementorVersion();
		$this->php_min = $this->getPhpVersion();
		$this->dir = $this->getDir();
	}

	public function getVersion() {
		return $this->version;
	}

	public function setVersion($version) {
		$this->version = $version;
	}

	public function getElementorVersion() {
		return $this->elementor_min;
	}

	public function setElementorVersion($version) {
		$this->elementor_min = $version;
	}

	public function getPhpVersion() {
		return $this->php_min;
	}

	public function setPhpVersion($version) {
		$this->php_min = $version;
	}

	public function getDir() {
		return $this->dir;
	}

	public function setDir($directory) {
		$this->dir = $directory;
	}

	public function plugin($name) {

		if (!is_dir('./' . $this->getDir())) {
			mkdir('./' . $this->getDir(), 0755, true);
		}

		$its = '<?php' . PHP_EOL;
		$its .= PHP_EOL . 'namespace ' . ucfirst($name) . ';' . PHP_EOL;

		$it = new \Nette\PhpGenerator\ClassType($name);
		$it->addConstant('VERSION', $this->version);
		$it->addConstant('MINIMUM_ELEMENTOR_VERSION', $this->elementor_min);
		$it->addConstant('MINIMUM_PHP_VERSION', $this->php_min);
		$it->addProperty('_instance', null)
		   ->setPrivate()
		   ->setStatic();

		$it_instance = $it->addMethod('instance');
		$it_instance->setStatic();
		$it_instance->setBody("if(is_null(self::\$_instance)) {\n\tself::\$_instance = new self();\n}\n\nreturn self::\$_instance;");

		$it_construct = $it->addMethod('__construct');
		$it_construct->setBody("add_action('plugins_loaded', [\$this, 'on_plugins_loaded']);");

		$it_i18n = $it->addMethod('i18n');
		$it_i18n->setBody("load_plugin_textdomain('plugin-" . strtolower($name) . "');");

		$it_onload = $it->addMethod('on_plugins_loaded');
		$it_onload->setBody("if(\$this->is_compatible()) {\n\tadd_action('elementor/init', [\$this, 'init']);\n}");

		$it_compatible = $it->addMethod('is_compatible');
		$it_compatible->setBody("if(!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {\n\tadd_action('admin_notices', [\$this, 'minimum_elementor_version']);\nreturn false;\n}\nif(!did_action('elementor/loaded')) {\n\tadd_action('admin_notices', [\$this, 'missing_main_plugin']);\n\treturn false;\n}\n\nreturn true;");

		$it_missing = $it->addMethod('missing_main_plugin');
		$it_missing->setBody("if(isset(\$_GET['activate'])) unset(\$_GET['activate']);\n\n\$message = sprintf(\n\t/* translators: 1: Plugin name 2: Elementor */\nesc_html__( '\"%1\$s\" requires \"%2\$s\" to be installed and activated.', 'plugin-" . strtolower($name) . "'),\n'<strong>' . esc_html__( 'Elementor Test Extension', 'plugin-" . strtolower($name) . "') . '</strong>',\n'<strong>' . esc_html__( 'Elementor', 'plugin-" . strtolower($name) . "') . '</strong>'\n);\n\nprintf('<div class=\"notice notice-warning is-dismissible\"><p>%1\$s</p></div>', \$message );");

		$it_init = $it->addMethod('init');
		$it_init->setBody("add_action('elementor/widgets/widgets_registered', [\$this, 'init_widgets']);\nadd_action('elementor/controls/controls_registered', [\$this, 'init_controls']);");

		$its .= PHP_EOL . $it;

		return $its;
	}
}