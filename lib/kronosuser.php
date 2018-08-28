<?php

/**
 * ownCloud
 *
 * @author Frank Karlitschek
 * @copyright 2012 Frank Karlitschek frank@owncloud.org
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * dummy user backend, does not keep state, only for testing use
 */
class OC_User_Kronos extends OC_User_Backend {

	static public $ser;

	static public function sync($uid) {
		$users = self::loadUsers();
		if(!is_array($users)) {
			return;
		}
		$uid = $uid['uid'];
		$user = $users[$uid];
		\OC::$server->getConfig()->setUserValue($uid, 'settings', 'email', $user['email']);

//		$image = new \OCP\Image();
//		$image->loadFromFile('/opt/railsapps/Kronos-Website/public/avatars/medium/missing.png');
		
//		$avatar = \OC::$server->getAvatarManager()->getAvatar($uid);
//		$avatar->set($image);
	}

	/**
	 * @brief Create a new user
	 * @param string $uid The username of the user to create
	 * @param string $password The password of the new user
	 * @return bool
	 *
	 * Creates a new user. Basic checking of username is done in OC_User
	 * itself, not in its subclasses.
	 */
	public function createUser($uid, $password) {
		return false;
	}

	/**
	 * @brief delete a user
	 * @param string $uid The username of the user to delete
	 * @return bool
	 *
	 * Deletes a user
	 */
	public function deleteUser($uid) {
		return false;
	}

	public function canChangeAvatar($uid) {
		return false;
	}

	/**
	 * @brief Set password
	 * @param string $uid The username
	 * @param string $password The new password
	 * @return bool
	 *
	 * Change the password of a user
	 */
	public function setPassword($uid, $password) {
		return false;
	}

	/**
	 * @brief Check if the password is correct
	 * @param string $uid The username
	 * @param string $password The password
	 * @return string
	 *
	 * Check if the password is correct without logging in the user
	 * returns the user id or false
	 */
	public function checkPassword($email, $password) {
		$users = self::loadUsers();
		$uid = null;

		foreach($users as $key => $val) {
			if($val['email'] === $email) {
				$uid = 'kronos.'.$val['id'];
				break;
			}
		}
		if($uid === null) {
			return false;
		}

    if(password_verify($password, $users[$uid]['password'])) {
      return $uid;
    }

    return false;
	}
	
	public function __construct($user, $password, $database, $hostname) {
		self::$users = array();
		self::$pdo = new PDO("pgsql:host='".$hostname."';dbname='".$database."'", $user, $password);
	}

	static private $pdo = null;
	static private $users = array();
	static private $init = false;

	static private function loadUsers() {
		$us = array();
		if(!self::$init) {
			self::$init = true;

			$stm = self::$pdo->prepare('SELECT users.id, users.email, users.name, users.encrypted_password, users.avatar_file_name FROM users INNER JOIN user_types ON users.user_type_id = user_types.id WHERE user_types.name != \'Oudlid\' AND user_types.name != \'Proeflid\'');
			$stm->execute();
			$results = $stm->fetchAll();
			
			foreach($results as $result) {
				$us['kronos.'.$result['id']] = array('email' => $result['email'], 'uid' => 'kronos.'.$result['id'], 'id' => $result['id'], 'name' => $result['name'], 'password' => $result['encrypted_password'], 'avatar_filename' => $result['avatar_file_name']);
			}
			self::$users = $us;
		} else {
			$us = self::$users;
		}
		return $us;
	}

	public function getDisplayName($uid) {
		$users = self::loadUsers();	
	
		return $users[$uid]['name'];
	}

	public function getDisplayNames($search = '', $limit = null, $offset = null) {
		$users = self::loadUsers();

		$retval = array();

		foreach($users as $user) {
			if($search === '') {
				$retval[$user['uid']] = $user['name'];
			} else {
				if(strpos($user['name'], $search) !== false) {
					$retval[$user['uid']] = $user['name'];
				}
			}
		}

		return $retval;
	}

	/**
	 * @brief Get a list of all users
	 * @param string $search
	 * @param int $limit
	 * @param int $offset
	 * @return array with all uids
	 *
	 * Get a list of all users.
	 */
	public function getUsers($search = '', $limit = null, $offset = null) {
		$dn = $this->getDisplayNames($search, $limit, $offset);

		return array_keys($dn);
	}

	/**
	 * @brief check if a user exists
	 * @param string $uid the username
	 * @return boolean
	 */
	public function userExists($uid) {
		$users = self::loadUsers();

		return isset($users[$uid]);
	}

	/**
	 * @return bool
	 */
	public function hasUserListings() {
		return true;
	}

	/**
	 * counts the users in the database
	 *
	 * @return int | bool
	 */
	public function countUsers() {
		$users = self::loadUsers();

		return count($users);
	}
}
