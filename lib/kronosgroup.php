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
 * dummy group backend, does not keep state, only for testing use
 */
class OC_Group_Kronos extends OC_Group_Backend {
	private $pdo;
	private $groups;
	private $init;

	public function __construct($user, $password) {
		if(!is_null($password)) {
			$this->pdo = new PDO("pgsql:host='localhost';dbname='kronos_production'", $user, $password);
			$this->groups = array();
			$this->init = false;
		}
	}

	private function loadGroups() {
		if(!$this->init) {
			$this->init = true;
			$stm = $this->pdo->prepare('	SELECT com.name, com.role, use.id, use.email
							FROM commissions AS com
							JOIN commission_memberships AS mem ON com.id = mem.commission_id
							JOIN users AS use ON mem.user_id = use.id
							');
			$stm->execute();
			$results = $stm->fetchAll();

			foreach($results as $result) {
				$this->groups[$result['name']]['role'] = $result['role'];
				$this->groups[$result['name']]['users'][] = 'kronos.'.$result['id'];
				if($result['role'] == "ADMIN") {
					$this->groups['admin']['role'] = $result['role'];
					$this->groups['admin']['users'][] = 'kronos.'.$result['id'];
				}
			}
		}
	}

	/**
	 * @brief Try to create a new group
	 * @param $gid The name of the group to create
	 * @returns true/false
	 *
	 * Trys to create a new group. If the group name already exists, false will
	 * be returned.
	 */
	public function createGroup($gid) {
		return false;
	}

	/**
	 * @brief delete a group
	 * @param $gid gid of the group to delete
	 * @returns true/false
	 *
	 * Deletes a group and removes it from the group_user-table
	 */
	public function deleteGroup($gid) {
		return false;
	}

	/**
	 * @brief is user in group?
	 * @param $uid uid of the user
	 * @param $gid gid of the group
	 * @returns true/false
	 *
	 * Checks whether the user is member of a group or not.
	 */
	public function inGroup($uid, $gid) {
		$this->loadGroups();

		return in_array($uid, $this->groups[$gid]['users']);
	}

	/**
	 * @brief Add a user to a group
	 * @param $uid Name of the user to add to group
	 * @param $gid Name of the group in which add the user
	 * @returns true/false
	 *
	 * Adds a user to a group.
	 */
	public function addToGroup($uid, $gid) {
		return false;
	}

	/**
	 * @brief Removes a user from a group
	 * @param $uid NameUSER of the user to remove from group
	 * @param $gid Name of the group from which remove the user
	 * @returns true/false
	 *
	 * removes the user from a group.
	 */
	public function removeFromGroup($uid, $gid) {
		return false;	
	}

	/**
	 * @brief Get all groups a user belongs to
	 * @param $uid Name of the user
	 * @returns array with group names
	 *
	 * This function fetches all groups a user belongs to. It does not check
	 * if the user exists at all.
	 */
	public function getUserGroups($uid) {
		$this->loadGroups();

		$retarr = array();
		foreach($this->groups as $gid => $group) {
			if(in_array($uid, $group['users'])) {
				$retarr[] = $gid;
			}
		}
		return $retarr;
	}

	/**
	 * @brief get a list of all groups
	 * @returns array with group names
	 *
	 * Returns a list with all groups
	 */
	public function getGroups($search = '', $limit = -1, $offset = 0) {
		$this->loadGroups();
	
		return array_keys($this->groups);
	}

	/**
	 * @brief get a list of all users in a group
	 * @returns array with user ids
	 */
	public function usersInGroup($gid, $search = '', $limit = -1, $offset = 0) {
		$this->loadGroups();

		return $this->groups[$gid]['users'];
	}

}
