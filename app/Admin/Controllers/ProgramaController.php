<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Programa;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\Coordinador;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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
                // Deshabilitar la creación de nuevos registros para el asistente
                $grid->disableCreateButton();
                $grid->actions(function ($actions) {
                    // Deshabilitar edición y eliminación para el asistente
                    $actions->disableEdit();
                    $actions->disableDelete();
                });
            }
        }
        

        $grid->column('id', __('Id'));
        $grid->column('Código_SNIES', __('Código SNIES'));
        $grid->column('Nombre_del_programa', __('Nombre del programa'));
        $grid->column('Descripción', __('Descripción'));
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
            return $pdf ? "<a href='/uploads/$pdf' target='_blank'>Ver PDF</a>" : 'No cargado';
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
        
        $form->text('Código_SNIES', __('Código SNIES'))
            ->creationRules('required|max:10|unique:programas,Código_SNIES')
            ->updateRules('required|max:10|unique:programas,Código_SNIES,{{id}}');
        $form->text('Nombre_del_programa', __('Nombre del programa'))
                ->rules('required|max:255');
        $form->textarea('Descripción', __('Descripción'))
                ->rules('required');
        $form->image('Logo', __('Logo'))
            ->move('programas/logos')->uniqueName()
            ->creationRules('required|image|mimes:jpeg,png,jpg,gif|max:5000')
            ->updateRules('nullable|image|mimes:jpeg,png,jpg,gif|max:5000');
        $form->text('Correo', __('Correo'))
                ->rules('required|email');
        $form->text('Teléfono', __('Teléfono'))
                ->rules('required|max:10');
        $form->select('Lineas_de_trabajo', __('Líneas de trabajo'))->options([
            'Ingeniería de Software' => 'Ingeniería de Software',
            'Inteligencia Artificial' => 'Inteligencia Artificial',
            'Ciencia de Datos' => 'Ciencia de Datos',
            'IoT y Tecnologías 4.0' => 'IoT y Tecnologías 4.0',
        ])->rules('required');
        $form->select('Coordinador_asignado', __('Coordinador asignado'))
                ->options(\App\Models\Coordinador::pluck('Nombre', 'id')->toArray())
                ->rules('required');
        $form->multipleSelect('docentes', 'Docentes')
                ->options(Docente::all()->pluck('Nombre', 'id'))
                ->rules('required');
        $form->date('Fecha_generación_del_registro_calificado', __('Fecha generación del registro calificado'))
                ->default(date('Y-m-d'))
                ->rules('required|date');
        $form->text('Número_de_resolución', __('Número de resolución'))
                ->rules('required|max:50');
        $form->file('Resolución_de_registro_calificado', __('Resolución de registro calificado'))
            ->move('programas/resoluciones')->uniqueName()
            ->creationRules('required|mimes:pdf|max:10000') 
            ->updateRules('nullable|mimes:pdf|max:10000');


        return $form;
    }

    // Método para obtener el rol del usuario
    private function getUserRole($user)
    {
        // Ajusta esto según tu sistema de roles
        return $user->role; // O el método adecuado para obtener el rol
    }
    
}
