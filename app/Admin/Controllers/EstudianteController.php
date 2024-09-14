<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Estudiante;
use \App\Models\Cohorte;
use \App\Models\Programa;
use Illuminate\Support\Facades\DB;

class EstudianteController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Estudiante';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Estudiante());


        // Obtener el usuario autenticado
        $user = auth()->user();

        // Verificar si el usuario es asistente
        $isAsistente = DB::table('admin_role_users')
            ->join('admin_roles', 'admin_role_users.role_id', '=', 'admin_roles.id')
            ->where('admin_role_users.user_id', $user->id)
            ->where('admin_roles.slug', 'asistente') // Ajusta el slug según el rol de asistente
            ->exists();

        // Verificar si el usuario es coordinador
        $isCoordinador = DB::table('admin_role_users')
            ->join('admin_roles', 'admin_role_users.role_id', '=', 'admin_roles.id')
            ->where('admin_role_users.user_id', $user->id)
            ->where('admin_roles.slug', 'coordinador') // Ajusta el slug según el rol de coordinador
            ->exists();

        if ($isAsistente) {
            // Obtener el coordinador al que el asistente está asignado
            $coordinador = DB::table('coordinadores')
                ->where('asistente_id', $user->id)
                ->first();

            if ($coordinador) {
                // Filtrar para que solo vea la información relacionada con el coordinador
                $grid->model()->whereHas('programa', function ($query) use ($coordinador) {
                    $query->where('Coordinador_asignado', $coordinador->id);
                });

                // Deshabilitar la creación de nuevos registros para el asistente
                $grid->disableCreateButton();
                $grid->actions(function ($actions) {
                    // Deshabilitar edición y eliminación para el asistente
                    $actions->disableEdit();
                    $actions->disableDelete();
                });
            }
        } elseif ($isCoordinador) {
            // Filtrar para que el coordinador vea solo la información relacionada con él mismo
            $grid->model()->whereHas('programa', function ($query) use ($user) {
                $query->whereHas('coordinador', function ($query) use ($user) {
                    $query->where('Nombre', $user->name); // Asume que el nombre del usuario es el nombre del coordinador
                });
            });

            // Deshabilitar la creación de nuevos registros para el coordinador
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                // Deshabilitar edición y eliminación para el coordinador
                $actions->disableEdit();
                $actions->disableDelete();
            });
        }

        $grid->column('id', __('Id'));
        $grid->column('Nombre', __('Nombre'));
        $grid->column('Identificación', __('Identificación'));
        $grid->column('Código_estudiantil', __('Código estudiantil'));
        $grid->column('Fotografía')->image();
        $grid->column('Dirección_de_residencia', __('Dirección de residencia'));
        $grid->column('Teléfono', __('Teléfono'));
        $grid->column('Correo', __('Correo'));
        $grid->column('Género', __('Género'));
        $grid->column('Fecha_de_nacimiento', __('Fecha de nacimiento'));
        $grid->column('Estado_civil', __('Estado civil'));
        $grid->column('Semestre', __('Semestre'));
        $grid->column('Fecha_de_ingreso', __('Fecha de ingreso'));
        $grid->column('Fecha_de_egreso', __('Fecha de egreso'));
        $grid->column('cohorte_id', __('Cohorte'))->display(function ($cohorteId) {
            return Cohorte::find($cohorteId)->Nombre ?? 'No asignada';
        });
        $grid->column('programa_id', __('Programa'))->display(function ($programaId) {
            return Programa::find($programaId)->Nombre_del_programa ?? 'No asignado';
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
        $show = new Show(Estudiante::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('Nombre', __('Nombre'));
        $show->field('Identificación', __('Identificación'));
        $show->field('Código_estudiantil', __('Código estudiantil'));
        $show->field('Fotografía', __('Fotografía'));
        $show->field('Dirección_de_residencia', __('Dirección de residencia'));
        $show->field('Teléfono', __('Teléfono'));
        $show->field('Correo', __('Correo'));
        $show->field('Género', __('Género'));
        $show->field('Fecha_de_nacimiento', __('Fecha de nacimiento'));
        $show->field('Estado_civil', __('Estado civil'));
        $show->field('Semestre', __('Semestre'));
        $show->field('Fecha_de_ingreso', __('Fecha de ingreso'));
        $show->field('Fecha_de_egreso', __('Fecha de egreso'));
        $show->field('cohorte_id', __('Cohorte id'));
        $show->field('programa_id', __('Programa id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        // Obtener el usuario autenticado
        $user = auth()->user();

        $userRole = $this->getUserRole($user); // Método para obtener el rol del usuario

        // Obtener el coordinador asignado por el nombre del usuario
        $coordinador = \App\Models\Coordinador::where('Nombre', $user->name)->first();

        // Si el usuario tiene el rol de coordinador y existe el coordinador asignado
        if ($coordinador) {
            // Filtrar para que solo vea su programa
            $show->model()->where('Coordinador_asignado', $coordinador->id);

            $show->panel()->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });

        }

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Estudiante());

        $form->text('Nombre', __('Nombre'))
            ->rules('required|max:255');
        $form->text('Identificación', __('Identificación'))
            ->creationRules('required|max:20|unique:estudiantes,Identificación')
            ->updateRules('required|max:20|unique:estudiantes,Identificación,{{id}}');
        $form->text('Código_estudiantil', __('Código estudiantil'))
            ->creationRules('required|max:20|unique:estudiantes,Código_estudiantil')
            ->updateRules('required|max:20|unique:estudiantes,Código_estudiantil,{{id}}');
        $form->image('Fotografía', __('Fotografía'))->move('/estudiantes')->uniqueName()
            ->creationRules('required|image|mimes:jpeg,png,jpg,gif|max:5000')
            ->updateRules('nullable|image|mimes:jpeg,png,jpg,gif|max:5000');
        $form->text('Dirección_de_residencia', __('Dirección de residencia'))
            ->rules('nullable|max:255');
        $form->text('Teléfono', __('Teléfono'))
            ->rules('nullable|max:10');
        $form->text('Correo', __('Correo'))
            ->rules('required|email');
        $form->select('Género', __('Género'))->options([
            'Masculino' => 'Masculino',
            'Femenino' => 'Femenino',
            'Otro' => 'Otro',
        ])
        ->rules('required|in:Masculino,Femenino,Otro');
        $form->date('Fecha_de_nacimiento', __('Fecha de nacimiento'))->default(date('Y-m-d'))
            ->rules('required|date|before_or_equal:today');
        $form->select('Estado_civil', __('Estado civil'))->options([
            'Soltero' => 'Soltero',
            'Casado' => 'Casado',
            'Divorciado' => 'Divorciado',
            'Viudo' => 'Viudo',
        ])
            ->rules('required');
        $form->text('Semestre', __('Semestre'))
            ->rules('required|numeric|min:1');
        $form->date('Fecha_de_ingreso', __('Fecha de ingreso'))->default(date('Y-m-d'))
            ->rules('required|date|before_or_equal:today');
        $form->date('Fecha_de_egreso', __('Fecha de egreso'))->default(date('Y-m-d'))
            ->rules('required|date|after_or_equal:Fecha_de_ingreso');
        $form->select('cohorte_id', __('Cohorte'))->options(Cohorte::pluck('Nombre', 'id'))
            ->creationRules('required|exists:cohortes,id')
            ->updateRules('nullable|exists:cohortes,id');
        $form->select('programa_id', __('Programa'))->options(Programa::pluck('Nombre_del_programa', 'id'))
            ->creationRules('required|exists:programas,id')
            ->updateRules('nullable|exists:programas,id');

        return $form;
    }
    // Método para obtener el rol del usuario
    private function getUserRole($user)
    {
        // Ajusta esto según tu sistema de roles
        return $user->role; // O el método adecuado para obtener el rol
    }
    
}
