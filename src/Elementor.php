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

		$it = new \Nette\PhpGenerator\ClassType($name);
		$it->addConstant('VERSION', $this->version);
		$it->addConstant('MINIMUM_ELEMENTOR_VERSION', $this->elementor_min);
		$it->addConstant('MINIMUM_PHP_VERSION', $this->php_min);
		$it->addProperty('_instance', null)
		   ->setPrivate()
		   ->setStatic();
		$its .= $it;

		return $it;
	}
}