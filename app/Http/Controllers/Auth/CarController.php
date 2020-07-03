<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Car;

class CarController extends Controller
{
    public function index(){
        
         $cars = Car::all()->load('user');
         return response()->json(array(
             'cars' => $cars,
             'status' =>'success'

         ), 200);

        
    }
    public function show($id){
      $car = Car::find($id)->load('user');
      return response()->json(array('car' => $car, 'status' =>'success'), 200);


    }
    public function store(Request $request){
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);
        if($checkToken){
            //recoger datos por post
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);


           //conseguir el usuario identificado
             $user = $jwtAuth->checkToken($hash, true);

             //validation
            // $request->merge($params_array);

            // try{
                $validate = \Validator::make($params_array,[
                    'title' => 'required|min:5',
                    'description' => 'required',
                    'price' => 'required',
                    'status' => 'required'
   
                ]);
                if($validate->fails()){
                    return response()->json($validate->errors(),400);

                }
                //var_dump($validate); die();

            // }catch(\Illuminate\Validation\ValidationException $e){
              //   return $e->getResponse();
               //  die();

            // }
             // guardar el coche 
            // if(isset($params->title) && isset($params->description) && isset($params->price) ){
             $car = new Car();
             $car->user_id = $user->sub;
             $car->title = $params->title;
             $car->description = $params->description;
             $car->price = $params->price;
             $car->status = $params ->status;
              $car->save();

            $data = array(
                'car' => $car,
                'status' =>'success',
                'code' => 200,

            );
        //}

        } else{
            //devolver el error
            $data = array(
                'message' => 'login Incorrecto',
                'status' =>'error',
                'code' => 300,

            );
        }
        return response()->json($data, 200);

    }
    public function update($id, Request $request){
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);
        if($checkToken){
            //recoger parametros post
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);



            //validar datos
            $validate = \Validator::make($params_array,[
                'title' => 'required|min:5',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required'

            ]);
            if($validate->fails()){
                return response()->json($validate->errors(),400);

            }




            //Actualizar registro
            $car = Car::where('id', $id)->update($params_array);
            $data = array(
                'car' => $params,
                'status' =>'success',
                'code' => 200

            );


        } else{
            //devolver el error
            $data = array(
                'message' => 'login Incorrecto',
                'status' =>'error',
                'code' => 300,

            );
        }
        return response()->json($data, 200);

        

    }

    public function destroy($id, Request $request){
        $hash = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){

            //comprobar si existe el registro
            $car = Car::find($id);


            //borrarlo

            $car->delete();

            //devolverlo
            $data = array(
                'car' => $car,
                'status' => 'success',
                'code' => 200
            );

        }else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Login Incorrecto !!'
            );
        }
        return response()->json($data, 200);


    }



} // fin de la clase controler :)