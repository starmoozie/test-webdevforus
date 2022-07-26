<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RoleRequest as Request;
use App\Models\Role as Model;

/**
 * Class RoleCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Starmoozie\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RoleCrudController extends BaseCrudController
{
    /**
     * Configure the parent class BaseCrudController object. Apply settings to all operations.
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->model   = Model::class;
        $this->request = Request::class;
        $this->clause  = [];
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @return void
     */
    protected function setupListOperation()
    {
        parent::setupListOperation();

        if (!is_me(starmoozie_user()->email)) {
            $this->crud->denyAccess(['create', 'update', 'delete']);
        }
    }

    public function store()
    {
        return $this->tStore();
    }

    public function update()
    {
        $this->crud->field('options')->type('hidden');
        $request = $this->crud->getRequest();
        $request->request->add(['options' => $request->menuPermission]);
        $request->request->remove('menuPermission');

        return $this->tUpdate();
    }

    /**
     * Define list columns.
     * 
     * @return void
     */
    protected function setColumns()
    {
        $this->crud->column('id')
        ->label(__('label.groupid'))
        ->type('text');

        $this->crud->column('name')
        ->label(__('label.namagroup'));

        $this->crud->column('city')
        ->label(__('label.city'));
    }

    protected function setFields()
    {
        $this->crud->field('name')
        ->label(__('label.namagroup'))
        ->size(6);

        $this->crud->field('city')
        ->label(__('label.city'))
        ->fake(true)
        ->store_in('details')
        ->size(6);

        $this->crud->field('menuPermission')
        ->label(__('starmoozie::menu_permission.menu_permission'))
        ->type('menu_permission')
        ->model('Starmoozie\LaravelMenuPermission\app\Models\MenuPermission')
        ->entity('menuPermission')
        ->attribute('child')
        ->pivot(true)
        ->view_namespace('menu_permission_view::fields')
        ->options(fn($query) => $query->joinMenuPermission()->get([
            'p.name as child',
            'menu_permission.id',
            'm.name as parent'
        ]));
    }
}