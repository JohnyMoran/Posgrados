<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Programa;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Model\Coordinador;
use Illuminate\Support\Facades\DB;
use OpenAdmin\Admin\Facades\Admin;

class ProgramaController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Programa';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Programa());

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
                ->where('asistente_id', $user->id) // Usa la columna 'asistente_id'
                ->first();

            if ($coordinador) {
                // Filtrar para que solo vea la información relacionada con el coordinador
                $grid->model()->where('Coordinador_asignado', $coordinador->id);

                // Deshabilitar la creación de nuevos registros para el asistente
                $grid->disableCreateButton();
                $grid->actions(function ($actions) {
                    // Deshabilitar edición y eliminación para el asistente
                    $actions->disableEdit();
                    $actions->disableDelete();
                });
            }
        } elseif ($isCoordinador) {
            // Obtener el coordinador asociado al usuario actual
            $coordinador = DB::table('coordinadores')
                ->where('Nombre', $user->name) // Usa el campo correcto para obtener el coordinador
                ->first();

            if ($coordinador) {
                // Filtrar para que el coordinador vea solo su propia información
                $grid->model()->where('Coordinador_asignado', $coordinador->id);
            }
        }
        

        $grid->column('id', __('Id'));
        $grid->column('Código_SNIES', __('Código SNIES'));
        $grid->column('Nombre_del_programa', __('Nombre del programa'));
        $grid->column('Descripción', __('Descripción'));
        //$grid->column('Logo', __('Logo'))->display(function ($logo) {
        //    return $logo ? "<img src='/storage/$logo' style='max-width:100px; max-height:100px;'>" : 'No cargado';
        //});
        $grid->column('Logo')->image();
        $grid->column('Correo', __('Correo'));
        $grid->column('Teléfono', __('Teléfono'));
        $grid->column('Lineas_de_trabajo', __('Líneas de trabajo'));
        $grid->column('Coordinador_asignado', __('Coordinador asignado'))->display(function () {
            return $this->coordinador ? $this->coordinador->Nombre : 'No asignado';
        });
        $grid->column('docentes', 'Docentes')->display(function () {
            return $this->docentes->pluck('Nombre')->implode(', ');
        });
        
        $grid->column('Fecha_generación_del_registro_calificado', __('Fecha generación del registro calificado'));
        $grid->column('Número_de_resolución', __('Número de resolución'));
        $grid->column('Resolución_de_registro_calificado', __('Resolución de registro calificado'))->display(function ($pdf) {
            return $pdf ? "<a href='/storage/$pdf' target='_blank'>Ver PDF</a>" : 'No cargado';
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
        $show = new Show(Programa::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('Código_SNIES', __('Código SNIES'));
        $show->field('Nombre_del_programa', __('Nombre del programa'));
        $show->field('Descripción', __('Descripción'));
        $show->field('Logo', __('Logo'));
        $show->field('Correo', __('Correo'));
        $show->field('Teléfono', __('Teléfono'));
        $show->field('Lineas_de_trabajo', __('Lineas de trabajo'));
        $show->field('Coordinador_asignado', __('Coordinador asignado'));
        $show->field('Fecha_generación_del_registro_calificado', __('Fecha generación del registro calificado'));
        $show->field('Número_de_resolución', __('Número de resolución'));
        $show->field('Resolución_de_registro_calificado', __('Resolución de registro calificado'));
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
        $form = new Form(new Programa());

        $form->text('Código_SNIES', __('Código SNIES'));
        $form->text('Nombre_del_programa', __('Nombre del programa'));
        $form->textarea('Descripción', __('Descripción'));
        $form->image('Logo', __('Logo'))->move('programas/logos')->uniqueName();
        $form->text('Correo', __('Correo'));
        $form->text('Teléfono', __('Teléfono'));
        $form->select('Lineas_de_trabajo', __('Lineas de trabajo'))->options([
            'Ingeniería de Software' => 'Ingeniería de Software',
            'Inteligencia Artificial' => 'Inteligencia Artificial',
            'Ciencia de Datos' => 'Ciencia de Datos',
            'IoT y Tecnologías 4.0' => 'IoT y Tecnologías 4.0',
        ]);
        $form->select('Coordinador_asignado', __('Coordinador asignado'))
        ->options(\App\Models\Coordinador::pluck('Nombre', 'id')->toArray());
        $form->multipleSelect('docentes', 'Docentes')->options(Docente::all()->pluck('Nombre', 'id'));
        $form->date('Fecha_generación_del_registro_calificado', __('Fecha generación del registro calificado'))->default(date('Y-m-d'));
        $form->text('Número_de_resolución', __('Número de resolución'));
        $form->file('Resolución_de_registro_calificado', __('Resolución de registro calificado'))->move('programas/resoluciones')->uniqueName();

        return $form;
    }

    // Método para obtener el rol del usuario
    private function getUserRole($user)
    {
        // Ajusta esto según tu sistema de roles
        return $user->role; // O el método adecuado para obtener el rol
    }
    
}
