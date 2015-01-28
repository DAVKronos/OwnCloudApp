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

require_once('bcrypt.php');

/**
 * dummy user backend, does not keep state, only for testing use
 */
class OC_User_Kronos extends OC_User_Backend {

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
	public function checkPassword($uid, $password) {
		$this->loadUsers();

		$crypt = new bcrypt();

		if($crypt->verify($password, $this->users[$uid]['password'])) {
			return $uid;
		}
		return false;
	}
	
	private $users;
	private $init;
	private $pdo;

	public function __construct($password) {
		$this->pdo = new PDO("pgsql:host='localhost';dbname='kronos_production'", 'kronos', $password);
		$this->users = array();
		$this->init = false;
	}

	private function loadUsers() {
		if(!$this->init) {
			$this->init = true;
			$stm = $this->pdo->prepare('SELECT email, name, encrypted_password FROM users');
			$stm->execute();
			$results = $stm->fetchAll();
			
			$this->users = array();
			foreach($results as $result) {
				$this->users[$result['email']] = array('uid' => $result['email'], 'name' => $result['name'], 'password' => $result['encrypted_password']);
			}
		}
	}

	public function getDisplayName($uid) {
		$this->loadUsers();	
	
		return $this->users[$uid]['name'];
	}

	public function getDisplayNames($search = '', $limit = null, $offset = null) {
		$this->loadUsers();

		$retval = array();

		foreach($this->users as $user) {
			if($search == '') {
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
		$this->loadUsers();

		return isset($this->users[$uid]);
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
		$this->loadUsers();

		return count($this->users);
	}
}
