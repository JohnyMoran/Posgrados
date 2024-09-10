<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Coordinador;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CoordinadorController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Coordinador';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Coordinador());

        $grid->column('id', __('Id'));
        $grid->column('Nombre', __('Nombre'));
        $grid->column('Identificación', __('Identificación'));
        $grid->column('Dirección', __('Dirección'));
        $grid->column('Teléfono', __('Teléfono'));
        $grid->column('Correo', __('Correo'));
        $grid->column('Género', __('Género'));
        $grid->column('Fecha_de_nacimiento', __('Fecha de nacimiento'));
        $grid->column('Fecha_de_vinculación', __('Fecha de vinculación'));
        $grid->column('Acuerdo_de_nombramiento_pdf', __('Acuerdo de nombramiento PDF'))->display(function ($pdf) {
            return $pdf ? "<a href='/uploads/$pdf' target='_blank'>Ver PDF</a>" : 'No cargado';
        });
        $grid->column('asistente_name', 'Asistente')->display(function () {
            // Obtener el nombre del asistente desde la tabla admin_users
            $asistente = DB::table('admin_users')
                ->where('id', $this->asistente_id)
                ->value('name');
            return $asistente ? $asistente : 'No asignado';
        });
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Coordinador::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('Nombre', __('Nombre'));
        $show->field('Identificación', __('Identificación'));
        $show->field('Dirección', __('Dirección'));
        $show->field('Teléfono', __('Teléfono'));
        $show->field('Correo', __('Correo'));
        $show->field('Género', __('Género'));
        $show->field('Fecha_de_nacimiento', __('Fecha de nacimiento'));
        $show->field('Fecha_de_vinculación', __('Fecha de vinculación'));
        $show->field('Acuerdo_de_nombramiento_pdf', __('Acuerdo de nombramiento pdf'));
        $show->field('asistente_id', __('asistente id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Coordinador());

        $form->text('Nombre', __('Nombre'))
            ->rules('required|max:255');
        $form->text('Identificación', __('Identificación'))
            ->creationRules('required|max:10|unique:coordinadores,Identificación')
            ->updateRules('required|max:10|unique:coordinadores,Identificación,{{id}}');
        $form->text('Dirección', __('Dirección'))
            ->rules('required|max:255');
        $form->text('Teléfono', __('Teléfono'))
            ->rules('required|max:10');
        $form->text('Correo', __('Correo'))
            ->rules('required|email');
        $form->select('Género', __('Género'))->options([
            'Masculino' => 'Masculino',
            'Femenino' => 'Femenino',
            'Otro' => 'Otro',
        ]);
        $form->date('Fecha_de_nacimiento', __('Fecha de nacimiento'))->default(date('Y-m-d'))
            ->rules('required|date|before_or_equal:today');
        $form->date('Fecha_de_vinculación', __('Fecha de vinculación'))->default(date('Y-m-d'))
            ->updateRules('required|date|after_or_equal:Fecha_de_nacimiento');
        $form->file('Acuerdo_de_nombramiento_pdf', __('Acuerdo de nombramiento PDF'))
            ->move('coordinadores/acuerdos')->uniqueName()
            ->creationRules('required|mimes:pdf|max:10000')
            ->updateRules('nullable|mimes:pdf|max:10000'); 
        
        // Relación con Asistente
        $form->select('asistente_id', 'Asistente_de_coordinador')->options(function () {
            return DB::table('admin_users')
                ->join('admin_role_users', 'admin_users.id', '=', 'admin_role_users.user_id')
                ->join('admin_roles', 'admin_role_users.role_id', '=', 'admin_roles.id')
                ->where('admin_roles.slug', 'asistente') // Ajusta el slug según el rol de asistente
                ->pluck('admin_users.name', 'admin_users.id');
            })
            ->creationRules('nullable|exists:admin_users,id')
            ->updateRules('nullable|exists:admin_users,id');    
        
        return $form;
    }
}
