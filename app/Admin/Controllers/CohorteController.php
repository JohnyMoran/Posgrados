<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Cohorte;
use \App\Models\Programa;
use App\Models\Estudiante;
use Illuminate\Support\Facades\DB;

class CohorteController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Cohorte';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Cohorte());

        $user = \OpenAdmin\Admin\Facades\Admin::user(); // Obtener el usuario autenticado

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
        $grid->column('Código', __('Código'));
        $grid->column('Nombre', __('Nombre'));
        $grid->column('Fecha_de_inicio', __('Fecha de inicio'));
        $grid->column('Fecha_de_finalización', __('Fecha de finalización'));
        $grid->column('numero_estudiantes_matriculados', __('Número de estudiantes_matriculados'))->display(function () {
            return $this->estudiantes->count();
        });
        $grid->column('Programa_id', __('Programa'))->display(function ($programa_id) {
            return \App\Models\Programa::find($programa_id)->Nombre_del_programa ?? 'No asignado';
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
        $show = new Show(Cohorte::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('Código', __('Código'));
        $show->field('Nombre', __('Nombre'));
        $show->field('Fecha_de_inicio', __('Fecha de inicio'));
        $show->field('Fecha_de_finalización', __('Fecha de finalización'));
        $show->field('Número_de_estudiantes_matriculados', __('Número de estudiantes matriculados'));
        $show->field('Programa_id', __('Programa id'));
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
        $form = new Form(new Cohorte());

        $form->text('Código', __('Código'));
        $form->text('Nombre', __('Nombre'));
        $form->date('Fecha_de_inicio', __('Fecha de inicio'))->default(date('Y-m-d'));
        $form->date('Fecha_de_finalización', __('Fecha de finalización'))->default(date('Y-m-d'));
        $form->text('Número_de_estudiantes_matriculados', __('Número de estudiantes matriculados'));
        $form->select('programa_id', __('Programa'))->options(Programa::pluck('Nombre_del_programa', 'id'));

        return $form;
    }

    // Método para obtener el rol del usuario
    private function getUserRole($user)
    {
        // Ajusta esto según tu sistema de roles
        return $user->role; // O el método adecuado para obtener el rol
    }

    public function testModel()
    {
        $estudiante = new \App\Models\Estudiante();
        dd($estudiante);
    }
}
