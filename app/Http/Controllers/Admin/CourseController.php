<?php

namespace App\Http\Controllers\Admin;

use App\Course;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // ACL
        abort_if(Gate::denies('course-view'), 403);

        $goToSection = 'index';
        // $items = Course::all(); // Todos
        // $items = Course::paginate(3); // limit de 3; Em blade: {{ $items->links() }}
        $items = Course::all();

        // view() -> 'admin' é um diretório >>> views/admin/courses.blade.php
        return view('admin.courses', compact('items', 'goToSection'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // ACL
        abort_if(Gate::denies('course-create'), 403);

        $goToSection = 'create';

        return view('admin.courses', compact('goToSection'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // ACL
        abort_if(Gate::denies('course-create'), 403);

        // dd($request->all());
        // dd($request['name'])

        $data = $request->all();
        $dir = 'img/courses/';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $microTime = preg_replace('/[[:punct:].[:space:]]/', '', microTime()); // remove espaços pontuações
            $ext = $image->guessClientExtension(); //retorna a extensão do arquivo
            $imageName = 'img_' . $microTime . '.' . $ext; // Deve-se criar uma tabela especifica de imagem e add o id da imagem no nome
            $image->move($dir, $imageName);
            $data['image'] = $dir . $imageName;
        } else {
            $data['image'] = $dir . 'default.png';
        }

        if (isset($data['status'])) {
            $data['status'] = 'y';
        } else {
            $data['status'] = 'n';
        }

        // dd($data);
        Course::create($data);

        return redirect()->route('admin.courses'); // !Não precisa das vars $item e $goToSection, a rota é chamada
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Course $course
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course, $id)
    {
        // ACL
        abort_if(Gate::denies('course-view'), 403);

        // Se a rota tiver nome diferente do Controller
        if (!isset($course->id)) {
            $course = Course::find($id);
        }

        $goToSection = 'show';
        $record = Course::find($course->id);

        // view() -> 'admin' é um diretório >>> views/admin/courses.blade.php
        return view('admin.courses', compact('goToSection', 'record'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Course $course
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course, $id)
    {
        // ACL
        abort_if(Gate::denies('course-update'), 403);

        // Se a rota tiver nome diferente do Controller
        if (!isset($course->id)) {
            $course = Course::find($id);
        }

        // dd($course); // Nome do Controller
        $goToSection = 'edit';
        $record = Course::find($course->id);

        return view('admin.courses', compact('goToSection', 'record'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Course $course
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course, $id)
    {
        // ACL
        abort_if(Gate::denies('course-update'), 403);

        $data = $request->all();

        // Se a rota tiver nome diferente do Controller
        if (!isset($course->id)) {
            $course = Course::find($id);
        }

        $dir = 'img/courses/';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $microTime = preg_replace('/[[:punct:].[:space:]]/', '', microTime()); // remove espaços pontuações
            $ext = $image->guessClientExtension(); //retorna a extensão do arquivo
            $imageName = 'img_' . $microTime . '.' . $ext; // Deve-se criar uma tabela especifica de imagem e add o id da imagem no nome
            $image->move($dir, $imageName);
            $data['image'] = $dir . $imageName;
        }

        if (isset($data['status'])) {
            $data['status'] = 'y';
        } else {
            $data['status'] = 'n';
        }

        // dd($data);
        // dd($course->id); // Por segurança buscar pelo id originário($course) e não o enviado($request)
        $course->update($data);

        // return redirect()->back();
        return redirect()->route('admin.courses'); // !Não precisa das vars $item e $goToSection, a rota é chamada
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Course $course
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course, $id)
    {
        // ACL
        abort_if(Gate::denies('course-delete'), 403);

        // Se a rota tiver nome diferente do Controller
        if (!isset($course->id)) {
            $course = Course::find($id);
        }

        try {
            $course->delete();
        } catch (\Exception $e) {
            abort(403, 'Não foi possível deletar o registro!');
        }

        return redirect()->route('admin.courses');
    }
}
