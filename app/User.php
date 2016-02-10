<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Hash;


/**
 * 
 * @method assignRole(int|string|object $role)
 * @method deleteRole(int|string|object $role)
 * @method access(int|string|object $perm)
 * @method hasRole(int|string|object $role)
 * 
 * @static @method add(array $arrData)
 * @static @method del(int|string|object $user)
 * @static @method getModel(int|string|object $user)
 * @static @method checkArrayUser(array $arrData) 
 * 
 */
class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
	
	
	public function roles()
    {
       return $this->belongsToMany('App\Role', 'user_has_roles');
    }
	
	
	
	/**
	 * Assign role to the user
	 * 
	 * @param {int|string|object}
	 * 	@param {int} id role
	 * 	@param {string} name role
	 * 	@param {object} object role
	 *
	 * @returns {object}
	 */
	public function assignRole($role = '')
	{
		$role = Role::getModel($role);
		
		if(!is_null($role)){
			$findRole = $this->roles()->where('id', $role->id)->get();
			if(count($findRole) === 0){
				$this->roles()->save($role);
			}
		}

		return $this;
	}
	
	
	
	/**
	 * Delete role to the user
	 * 
	 * @param {int|string|object}
	 * 	@param {int} id role
	 * 	@param {string} name role
	 * 	@param {object} object role
	 *
	 * @returns {object}
	 */
	public function deleteRole($role = '')
	{
		$role = Role::getModel($role);
		
		if(!is_null($role)){
			$this->roles()->detach($role);
		}
		
		return $this;
	}
	
	
	/**
	 * Check access user
	 * 
	 * @param {int|string|object}
	 * 	@param {int} id perm
	 * 	@param {string} name perm
	 * 	@param {object} object perm
	 *
	 * @returns {true|false}
	 */
	public function access($perm = '')
	{
		$roles = $this->roles()->get();
		$permission = Permission::getModel($perm);
		
		if(is_null($roles) || is_null($permission)){
			return false;
		}
		
		foreach($roles as $key => $item){
			$findPerm = $permission->roles()->find($item->id);
			if(count($findPerm) > 0){
				return true;
			}
		}

		return false;
	}
	
	
	
	/**
	 * Has role
	 * 
	 * @param {int|string|object}
	 * 	@param {int} id role
	 * 	@param {string} name role
	 * 	@param {object} object role
	 *
	 * @returns {true|false}
	 */
	public function hasRole($role = '')
	{
		$roles = Role::getModel($role);
		
		if(is_null($roles)){
			return false;
		}
		
		$findRole = $this->roles()->find($roles->id);
		if(count($findRole) > 0){
			return true;
		}

		return false;
	}
	
	
	
	/**
	 * Save user
	 *
	 * @param {array} $arrData array with data to be added
	 * 	@param {string} $arrData['name'] name user
	 * 	@param {string} $arrData['email'] email user
	 * 	@param {string} $arrData['password'] password user
	 *
	 * @returns {object|null}
	 *
	 */
	public static function add($arrData = array())
	{
		if(self::checkArrayUser($arrData) === false){
			return null;
		}
		
		$arrDefault = array(
			'name' => '',
			'email' => '',
			'password' => '',
		);
		$res = array_merge($arrDefault, $arrData);
		
		$user = new User;
		$user->name = $res['name'];
		$user->email = $res['email'];
		if($res['password'] != ''){
			$user->password = Hash::make($res['password']);
		}
		$user->save();
		
		return $user;
	}
	
	
	
	/**
	 * Delete user
	 *
	 * @param {int|string|object}
	 * 	@param {int} id user
	 * 	@param {string} email user
	 * 	@param {object} object user
	 *
	 * @returns {true}
	 */
	public static function del($user = '')
	{
		$user = self::getModel($user);
		if(!is_null($user)){
			$user->delete();
		}
		
		return true;
	}
	
	
	
	
	/**
	 *  Getting the user by id or email or object
	 * 
	 * @param {int|string|object}
	 * 	@param {int} id user
	 * 	@param {string} email user
	 * 	@param {object} object user
	 *
	 * @returns {object|null}
	 */
	public static function getModel($user = '')
	{
		$userModel = null;
		if(is_string($user) === true){
			$userModel = self::where('email', $user);
		}
		if(is_numeric($user) === true){
			$userModel = self::find($user);
		}
		if(is_object($user) === true){
			$userModel = self::find($user->id);
		}
		
		return $userModel;
	}
	
	
	
	
	/**
	 * Checking to add to the array user
	 * 
	 * @returns {true|false}
	 */
	static function checkArrayUser($arrData = array())
	{
		if(
			is_array($arrData) === true 
			&& array_key_exists('name', $arrData) === true
			&& array_key_exists('email', $arrData) === true
		){
			return true;
		}
		
		return false;
	}	
}
