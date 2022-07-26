<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest as Request;
use App\Models\User as Model;
use App\Models\Role;
use App\Imports\DataImport;
use Excel;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 */
class UserCrudController extends BaseCrudController
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
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        parent::setup();

        if (!is_me(starmoozie_user()->email)) {
            $this->crud->addClause('where', 'email', '!=', 'starmoozie@gmail.com');
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @return void
     */
    protected function setupListOperation()
    {
        parent::setupListOperation();
        $this->crud->addButtonFromView('top', 'import', 'import', 'end');
    }

    /**
     * Store a newly created resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation();

        return $this->tStore();
    }

    /**
     * Update the specified resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation();

        return $this->tUpdate();
    }

    /**
     * Handle password input fields.
     */
    protected function handlePasswordInput($request)
    {
        // Remove fields not present on the user.
        $request->request->remove('password_confirmation');

        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', \Hash::make($request->input('password')));
        } else {
            $request->request->remove('password');
        }

        return $request;
    }

    /**
     * Columns to show in list.
     */
    protected function setColumns()
    {
        $this->crud->column('name')
        ->label(__('starmoozie::base.name'));

        $this->crud->column('email')
        ->label(__('starmoozie::menu_permission.email'));

        $this->crud->column('role_id')
        ->label(__('starmoozie::menu_permission.role'));
    }

    /**
     * Fields are using inside create/edit.
     */
    protected function setFields()
    {
        $this->crud->field('name')
        ->label(__('starmoozie::base.name'))
        ->size('6')
        ->tab(__('tab.profile'));

        $this->crud->field('email')
        ->size('6')
        ->label(__('starmoozie::menu_permission.email'))
        ->tab(__('tab.profile'));

        $this->crud->field('mobile')
        ->size('6')
        ->label(__('starmoozie::menu_permission.mobile'))
        ->tab(__('tab.profile'));

        $this->crud->field('role')
        ->size('6')
        ->allows_null(false)
        ->label(__('starmoozie::menu_permission.role'))
        ->options(fn($q) => $q->when(
            !is_me(starmoozie_user()->email),
            fn($q) => $q->whereNotIn('name', ['developer'])
        ))
        ->tab(__('tab.profile'));

        $this->crud->field('address')
        ->label(__('label.address'))
        ->fake(true)
        ->store_in('details')
        ->type('textarea')
        ->tab(__('tab.profile'));

        $this->crud->field('photo')
        ->label(__('label.photo'))
        ->fake(true)
        ->store_in('details')
        ->type('image')
        ->crop(true)
        ->aspect_ratio(0)
        ->tab(__('tab.profile'));

        $this->crud->field('password')
        ->type('password')
        ->size('6')
        ->label(__('starmoozie::menu_permission.password'))
        ->tab(__('tab.password'));

        $this->crud->field('password_confirmation')
        ->type('password')
        ->size('6')
        ->label(__('starmoozie::menu_permission.password_confirm'))
        ->tab(__('tab.password'));
    }

    public function import()
    {
        ini_set('max_execution_time', 180);
        $data = Excel::toArray(new DataImport, request()->file('file'));
        $data = reset($data);

        foreach (array_chunk($data, 100) as $chunk) {
            foreach ($chunk as $key => $value) {
                $data_role = ['name' => $value[1]];
                $role      = Role::updateOrCreate($data_role, array_merge($data_role, ['options' => "[]"]));

                $unique      = filter_var(microtime(true), FILTER_SANITIZE_NUMBER_INT);
                $user_data[] = [
                    'id'      => substr($unique, 0, 14),
                    'name'    => $value[2],
                    'mobile'  => $value[4],
                    'email'   => $value[5],
                    'role_id' => Role::latest()->first()->id,
                    'details' => JSON_ENCODE([
                        'city'  => $value[3],
                        'photo' => NULL
                    ]),
                    'password' => \Hash::make($value[5])
                ];
            }

            $this->bulkInsertOrUpdate($user_data);
        }

        return 1;
    }

    private function bulkInsertOrUpdate(array $rows){
        $table = \DB::getTablePrefix().with(new Model)->getTable();

        $first = reset($rows);

        $columns = implode( ',',
            array_map( function( $value ) { return "$value"; } , array_keys($first) )
        );

        $values = implode( ',', array_map( function( $row ) {
                return '('.implode( ',',
                    array_map( function( $value ) { return \DB::connection()->getPdo()->quote($value); } , $row )
                ).')';
            } , $rows )
        );
        
        $updates = implode( ',',
            array_map( function( $value ) { return "$value = VALUES($value)"; } , array_keys($first) )
        );
        
        $sql = "INSERT INTO {$table}({$columns}) VALUES {$values} ON DUPLICATE KEY UPDATE {$updates}";
        
        return \DB::statement( $sql );
    }
}
