<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\PermissionGroup;
use App\Permission;

class ModelPermissionGroupTest extends TestCase
{
	use DatabaseTransactions; 
	
    /**
	 * Тестирование модели PermissionGroup
	 * deletePermissionTest - отвязывание права доступа
	 * deleteTest - удвление группы
	 * 
     */
    public function testExample()
    {
		$arr = Array(
			'faker' => Faker\Factory::create(),
		);
		
        $this->addTest($arr);
        $this->assignPermissionTest($arr);
    }
	
	
	
	/**
	 * addTest - добавление группы
	 */
	public function addTest($arr){	
		//Success
		$groupString = PermissionGroup::add(str_random(10));
		$groupArrayMin = PermissionGroup::add(array('name' => str_random(10)));
		$groupArrayFull = PermissionGroup::add(array(
			'name' => str_random(10),
            'display_name' => str_random(10),
            'description' => str_random(10),
            'sort_order' => $arr['faker']->randomNumber,
		));
		
		$this->assertFalse(is_null($groupString));
		$this->assertFalse(is_null($groupArrayMin));
		$this->assertFalse(is_null($groupArrayFull));
		
		$this->seeInDatabase('permission_groups', ['id' => $groupString->id, 'name' => $groupString->name]);
		$this->seeInDatabase('permission_groups', ['id' => $groupArrayMin->id, 'name' => $groupArrayMin->name]);
		$this->seeInDatabase('permission_groups', [
			'id' => $groupArrayFull->id,
			'name' => $groupArrayFull->name,
			'display_name' => $groupArrayFull->display_name,
			'description' => $groupArrayFull->description,
			'sort_order' => $groupArrayFull->sort_order
		]);
		
		
		
		//Error
		$groupNull = PermissionGroup::add();
		$groupInt = PermissionGroup::add($arr['faker']->randomNumber);
		$groupErrArray = PermissionGroup::add(array('display_name' => str_random(10)));
		
		$this->assertTrue(is_null($groupNull));
		$this->assertTrue(is_null($groupInt));
		$this->assertTrue(is_null($groupErrArray));
	}
	
	
	
	/**
	 * assignPermissionTest - привязывание права доступа к группе
	 */
	public function assignPermissionTest($arr){
		//Success
		$group = PermissionGroup::add(str_random(10));
		$permissionOne = Permission::add(str_random(10));
		$permissionTwo = Permission::add(str_random(10));
		$permissionThree = Permission::add(str_random(10));
		
		$group
			->assignPermission($permissionOne)
			->assignPermission($permissionTwo->name)
			->assignPermission($permissionThree->id);
			
		$this->seeInDatabase('permissions', ['id' => $permissionOne->id, 'group_id' => $group->id]);
		$this->seeInDatabase('permissions', ['id' => $permissionTwo->id, 'group_id' => $group->id]);
		$this->seeInDatabase('permissions', ['id' => $permissionThree->id, 'group_id' => $group->id]);
		
		$this->assertTrue(count($group->permissions()->get()) == 3);
		
		
		
		//Error
		$groupErr = PermissionGroup::add(str_random(10));
		$groupErr
			->assignPermission(str_random(12))
			->assignPermission();
		
		$this->assertTrue(count($groupErr->permissions()->get()) == 0);
	}
}







