<?php

namespace OC\Files\Storage;

class Kronos extends \OC\Files\Storage\Local {

	protected $group;

	public function __construct($arguments) {
		$this->group = $arguments['group'];
		$datadir = $arguments['datadir'];
		parent::__construct(array('datadir' => $datadir));
	}

	public function getId() {
		if($this->group) {
			return 'kronos::' . $this->group;
		} else {
			return 'kronos::all';
		}
	}

	public static function setup($options) {
		if (\OCP\User::isLoggedIn() || $options['user']) {
			$user_dir = $options['user_dir'];
			$commissies = \OC_Group::getUserGroups(\OCP\User::getUser());

			if(!\OCP\User::isLoggedIn()) {
				$commissies = \OC_Group::getGroups();
			}

			$dataroot = \OC::$SERVERROOT."/data/";

			\OC\Files\Filesystem::mount('\OC\Files\Storage\Kronos',
				array('datadir' => $dataroot.'Iedereen', 'group' => null), $user_dir.'/Iedereen/');

			foreach($commissies as $commissie) {
				$map = iconv("utf-8", "ascii//TRANSLIT", $commissie);
				\OC\Files\Filesystem::mount('\OC\Files\Storage\Kronos',
					array('datadir' => $dataroot.$map, 'group' => $commissie),
					$user_dir . '/'.$commissie.' Schijf/');
			}
		}
	}

	public function getCache($path = '', $storage = null) {
		return new \OC\Files\Cache\KronosCache($this);
	}

	public function getOwner($path) {
		return 'webmaster@kronos.nl';
	}
}
