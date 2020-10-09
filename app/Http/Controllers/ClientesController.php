<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Clientes;


class ClientesController extends Controller
{
    public function index()
    {
        $json = array(
            "detalle" => "No encontrado"
        );

        return json_encode($json, true);
    }

    public function store(Request $request)
    {

        //recoger datos
        $datos = array(
            "nombre" => $request->input("nombre"),
            "apellido" => $request->input("apellido"),
            "email" => $request->input("email")
        );
        if (!empty($datos)) {
            //validar datos
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'email' => 'required|string|email|unique:clientes|max:255',
            ]);

            //Si falla la validación
            if ($validator->fails()) {
                $errores = $validator->errors();

                $json = array(
                    "status" => 404,
                    "detalle" => $errores
                );
                return json_encode($json, true);
            } else {
                $idClienteCod = Hash::make($datos["nombre"] . $datos["apellido"] . $datos["email"]);
                $idCliente = str_replace('$', 'a', $idClienteCod);

                $llave_secretaCod = Hash::make($datos["email"] . $datos["apellido"] . $datos["nombre"], ['roinds' => 12]);
                $llave_secreta = str_replace('$', 'o', $llave_secretaCod);

                $cliente = new Clientes();
                $cliente->nombre = $datos["nombre"];
                $cliente->apellido = $datos["apellido"];
                $cliente->email = $datos["email"];
                $cliente->id_cliente = $idCliente;
                $cliente->llave_secreta = $llave_secreta;

                $cliente->save();

                $response = array(
                    "status" => 200,
                    "detalle" => "Success: Registrado Correctamente, Por favor asegurar las credenciales",
                    "Credenciales" => array(
                        "id_cliente" => $idCliente,
                        "llave_secreta" => $llave_secreta
                    )
                );

                return json_encode($response, true);
            }
        } else {
            $response = array(
                "status" => 404,
                "detalle" => "ERROR: Registro no valido"
            );

            return json_encode($response, true);
        }
    }
}
