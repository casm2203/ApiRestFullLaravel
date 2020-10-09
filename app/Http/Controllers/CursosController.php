<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cursos;
use App\Models\Clientes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;



class CursosController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->header('Authorization');
        // echo "<pre>";
        // print_r($token);
        // echo "</pre>";
        $response = array();
        $clientes = Clientes::all();
        foreach ($clientes as $key => $value) {
            if ("Basic " . base64_encode($value["id_cliente"] . ":" . $value["llave_secreta"]) == $token) {

                //$cursos = Cursos::all();

                if (isset($_GET["page"])) {

                    $cursos = DB::table('cursos')
                        ->join('clientes', 'cursos.id_creador', '=', 'clientes.id')
                        ->select(
                            'cursos.id',
                            'cursos.titulo',
                            'cursos.descripcion',
                            'cursos.instructor',
                            'cursos.imagen',
                            'cursos.id_creador',
                            'clientes.nombre',
                            'clientes.apellido'
                        )
                        ->paginate(10);
                } else {
                    $cursos = DB::table('cursos')
                        ->join('clientes', 'cursos.id_creador', '=', 'clientes.id')
                        ->select(
                            'cursos.id',
                            'cursos.titulo',
                            'cursos.descripcion',
                            'cursos.instructor',
                            'cursos.imagen',
                            'cursos.id_creador',
                            'clientes.nombre',
                            'clientes.apellido'
                        )
                        ->get();
                }

                if (!empty($cursos)) {
                    $response = array(
                        "status" => 200,
                        "total_registros" => count($cursos),
                        "detalles" => $cursos

                    );
                    return json_encode($response, true);
                } else {
                    $response = array(
                        "status" => 200,
                        "detalle" => "ERROR: No hay registros de cursos"
                    );
                }
            } else {
                $response = array(
                    "status" => 404,
                    "detalle" => "ERROR: Token no registrado"
                );
            }
        }
        return json_encode($response, true);
    }


    //Crear registro

    public function store(Request $request)
    {
        $token = $request->header('Authorization');
        $response = array();
        $clientes = Clientes::all();

        foreach ($clientes as $key => $value) {
            if ("Basic " . base64_encode($value["id_cliente"] . ":" . $value["llave_secreta"]) == $token) {
                $datos = array(
                    "titulo" => $request->input("titulo"),
                    "descripcion" => $request->input("descripcion"),
                    "instructor" => $request->input("instructor"),
                    "imagen" => $request->input("imagen"),
                    "precio" => $request->input("precio"),
                );
                if (!empty($datos)) {
                    //validar datos
                    $validator = Validator::make($request->all(), [
                        'titulo' => 'required|string|unique:cursos|max:255',
                        'descripcion' => 'required|string|unique:cursos|max:255',
                        'instructor' => 'required|string|max:255',
                        'imagen' => 'required|string|unique:cursos|max:255',
                        'precio' => 'required|numeric',
                    ]);

                    if ($validator->fails()) {
                        $errores = $validator->errors();
                        $json = array(
                            "status" => 404,
                            "detalle" => $errores
                        );
                        return json_encode($json, true);
                    } else {
                        $cursos = new Cursos();
                        $cursos->titulo = $datos["titulo"];
                        $cursos->descripcion = $datos["descripcion"];
                        $cursos->instructor = $datos["instructor"];
                        $cursos->imagen = $datos["imagen"];
                        $cursos->precio = $datos["precio"];
                        $cursos->id_creador =  $value["id"];

                        $cursos->save();
                        $response = array(
                            "status" => 200,
                            "detalle" => "Success: Se ha registrado correctamente el curso",
                        );
                    }
                } else {
                    $response = array(
                        "status" => 404,
                        "detalle" => "ERROR: Registro no puede tener campos vacios"
                    );
                }
            } else {
                $response = array(
                    "status" => 404,
                    "detalle" => "ERROR: Token no registrado"
                );
            }
        }
        return json_encode($response, true);
    }

    //seleccionar un registor
    public function show($id, Request $request)
    {
        $token = $request->header('Authorization');
        $response = array();
        $clientes = Clientes::all();

        foreach ($clientes as $key => $value) {
            if ("Basic " . base64_encode($value["id_cliente"] . ":" . $value["llave_secreta"]) == $token) {
                $cursos = Cursos::where("id", $id)->get();

                if (!empty($cursos)) {
                    $response = array(
                        "status" => 200,
                        "total_registros" => count($cursos),
                        "detalles" => $cursos

                    );
                } else {
                    $response = array(
                        "status" => 200,
                        "detalle" => "ERROR: No hay registros de cursos"
                    );
                }
            } else {
                $response = array(
                    "status" => 404,
                    "detalle" => "ERROR: Token no registrado"
                );
            }
        }
        return json_encode($response, true);
    }

    //Editar registro

    public function update($id, Request $request)
    {
        $token = $request->header('Authorization');
        $response = array();
        $clientes = Clientes::all();

        foreach ($clientes as $key => $value) {
            if ("Basic " . base64_encode($value["id_cliente"] . ":" . $value["llave_secreta"]) == $token) {
                $datos = array(
                    "titulo" => $request->input("titulo"),
                    "descripcion" => $request->input("descripcion"),
                    "instructor" => $request->input("instructor"),
                    "imagen" => $request->input("imagen"),
                    "precio" => $request->input("precio"),
                );
                if (!empty($datos)) {
                    //validar datos
                    $validator = Validator::make($request->all(), [
                        'titulo' => 'required|string|max:255',
                        'descripcion' => 'required|string|max:255',
                        'instructor' => 'required|string|max:255',
                        'imagen' => 'required|string|max:255',
                        'precio' => 'required|numeric',
                    ]);

                    if ($validator->fails()) {
                        $errores = $validator->errors();
                        $response = array(
                            "status" => 404,
                            "detalle" => $errores
                        );
                        return json_encode($response, true);
                    } else {

                        $traer_curso = Cursos::where("id", $id)->get();

                        if ($traer_curso[0]["id_creador"] == $value["id"]) {
                            $datos = array(
                                "titulo" => $datos["titulo"],
                                "descripcion" => $datos["descripcion"],
                                "instructor" => $datos["instructor"],
                                "imagen" => $datos["imagen"],
                                "precio" => $datos["precio"],
                            );

                            $cursos = Cursos::where("id", $id)->update($datos);

                            $response = array(
                                "status" => 200,
                                "detalle" => "Success: Se ha actualizado correctamente el curso",
                            );
                        } else {
                            $response = array(
                                "status" => 404,
                                "detalle" => "ERROR: No está autorizado"
                            );
                        }
                    }
                } else {
                    $response = array(
                        "status" => 404,
                        "detalle" => "ERROR: Registro no puede tener campos vacios"
                    );
                }
            } else {
                $response = array(
                    "status" => 404,
                    "detalle" => "ERROR: Token no registrado"
                );
            }
        }
        return json_encode($response, true);
    }


    //Eliminar un registro
    public function destroy($id, Request $request)
    {
        $token = $request->header('Authorization');
        $response = array();
        $clientes = Clientes::all();

        foreach ($clientes as $key => $value) {
            if ("Basic " . base64_encode($value["id_cliente"] . ":" . $value["llave_secreta"]) == $token) {

                $validar = Cursos::where("id", $id)->get();
                // echo "<pre>";
                // print_r(
                //     $validar[0]["id_creador"]
                // );
                // echo "</pre>";

                if (!empty($validar)) {

                    if ($validar[0]["id_creador"] == $value["id"]) {

                        #$validar = Cursos::where("id", $id)->delete();

                        $response = array(
                            "status" => 200,
                            "detalle" => "Success: Se ha borrado correctamente el curso",
                        );
                    } else {
                        $response = array(
                            "status" => 404,
                            "detalle" => "ERROR: No está autorizado"
                        );
                    }
                } else {
                    $response = array(
                        "status" => 404,
                        "detalle" => "ERROR: No existe el id del curso"
                    );
                }
            }
        }
        return json_encode($response, true);
    }
}
