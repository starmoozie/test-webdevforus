<?php

namespace App\Http\Controllers\Admin;

use Starmoozie\CRUD\app\Http\Controllers\CrudController;
use Starmoozie\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class BaseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Starmoozie\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BaseCrudController extends CrudController
{
    use \Starmoozie\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Starmoozie\CRUD\app\Http\Controllers\Operations\CreateOperation { store as tStore; }
    use \Starmoozie\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as tUpdate; }
    use \Starmoozie\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Starmoozie\CRUD\app\Http\Controllers\Operations\ShowOperation;

    use \Starmoozie\LaravelMenuPermission\app\Traits\CheckPermission;

    protected $model;
    protected $request;
    protected $clause = [];

    /**
     * Configure the CrudPanel object. Apply settings to all operations from child class.
     * 
     * @return void
     */
    public function setup()
    {
        $path    = request()->segment(2);
        $heading = str_replace('-', ' ', $path);

        CRUD::setModel($this->model);
        CRUD::setRoute(config('starmoozie.base.route_prefix') . "/$path");
        CRUD::setEntityNameStrings(__("starmoozie::title.$heading"), __("starmoozie::title.$heading"));

        foreach ($this->clause as $key => $value) {
            CRUD::addClause($value);
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @return void
     */
    protected function setupListOperation()
    {
        $this->checkPermission();

        $this->setColumns();
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->checkPermission();

        $this->crud->setValidation($this->request);

        $this->setFields();
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    /**
     * Define what happens when the Show operation is loaded.
     * 
     * @return void
     */
    protected function setupShowOperation()
    {
        $this->checkPermission();

        $this->setShows();
    }
}
