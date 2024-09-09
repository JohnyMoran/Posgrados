<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Docente;
use \App\Models\Programa;
use Illuminate\Support\Facades\DB;

class DocenteController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Docente';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Docente());


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
        //$grid->column('Fotografía', __('Fotografía'))->display(function ($foto) {
        //    return $foto ? "<img src='/storage/uploads/estudiantes/$foto' style='max-width:100px; max-height:100px;'>" : 'No cargado';
        //});
        $grid->column('Fotografía')->image();
        $grid->column('Dirección', __('Dirección'));
        $grid->column('Teléfono', __('Teléfono'));
        $grid->column('Correo', __('Correo'));
        $grid->column('Género', __('Género'));
        $grid->column('Fecha_de_nacimiento', __('Fecha de nacimiento'));
        $grid->column('Formación_académica', __('Formación académica'));
        $grid->column('Áreas_de_conocimiento', __('Áreas de conocimiento'));
        $grid->column('programas', __('Programas'))->display(function () {
            // Verificar si el docente tiene programas asociados y devolver los nombres
            if ($this->programa) {
                return $this->programa->pluck('Nombre_del_programa')->implode(', ');
            } else {
                return 'No asignado';
            }
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
        $show = new Show(Docente::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('Nombre', __('Nombre'));
        $show->field('Identificación', __('Identificación'));
        $show->field('Fotografía', __('Fotografía'));
        $show->field('Dirección', __('Dirección'));
        $show->field('Teléfono', __('Teléfono'));
        $show->field('Correo', __('Correo'));
        $show->field('Género', __('Género'));
        $show->field('Fecha_de_nacimiento', __('Fecha de nacimiento'));
        $show->field('Formación_académica', __('Formación académica'));
        $show->field('Áreas_de_conocimiento', __('Áreas de conocimiento'));
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
        $form = new Form(new Docente());

        $form->text('Nombre', __('Nombre'));
        $form->text('Identificación', __('Identificación'));
        $form->image('Fotografía', __('Fotografía'))->move('uploads/estudiantes')->uniqueName();
        $form->text('Dirección', __('Dirección'));
        $form->text('Teléfono', __('Teléfono'));
        $form->text('Correo', __('Correo'));
        $form->select('Género', __('Género'))->options([
            'Masculino' => 'Masculino',
            'Femenino' => 'Femenino',
            'Otro' => 'Otro',
        ]);
        $form->date('Fecha_de_nacimiento', __('Fecha de nacimiento'))->default(date('Y-m-d'));
        $form->select('Formación_académica', __('Formación académica'))->options([
            'Pregrado' => 'Pregrado',
            'Posgrado' => 'Posgrado',
        ]);
        $form->select('Áreas_de_conocimiento', __('Áreas de conocimiento'))->options([
            'Ingeniería de software' => 'Ingeniería de software',
            'Telecomunicaciones' => 'Telecomunicaciones',
            'Bases de datos' => 'Bases de datos',
        ]);
         $form->multipleSelect('programa', 'Programa')
            ->options(Programa::all()->pluck('Nombre_del_programa', 'id'));

        // Usar el método saving para guardar la relación muchos a muchos en la tabla pivot
        $form->saving(function (Form $form) {
            // Guardar la relación con los programas seleccionados en la tabla pivot
            $form->model()->programa()->sync($form->programas);
        });
        return $form;
    }
    private function getUserRole($user)
    {
        // Ajusta esto según tu sistema de roles
        return $user->role; // O el método adecuado para obtener el rol
    }
}
