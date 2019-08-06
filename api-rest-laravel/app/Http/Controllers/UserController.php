<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use Dotenv\Validator;

class UserController extends Controller
{
    public function test(Request $request)
    {
        return "Acción de pruebas de USER";
    }

    public function register(Request $request)
    {

        // Get data user by post
        $json = $request->input('json', null);
        $params = json_decode($json); // Object
        $params_array = json_decode($json, true); //Array

        if(!empty($params) && !empty($params_array))
        {

            // Clean data
            $params_array = array_map('trim', $params_array);

            // Validate data
            $validate = \Validator::make($params_array, [
                'name'      => 'required|alpha',
                'surname'   => 'required|alpha',
                'email'     => 'required|email|unique:users',
                'password'  => 'required'
            ]);

            if($validate->fails()) {

                $data = array(
                    'status'    => 'error',
                    'code'      => 400,
                    'message'   => 'User has not been created',
                    'errors'    => $validate->errors()
                );

            } else {

                // Encrypt password
                $pwd = hash('sha256', $params->password);

                // Create user
                $user = User::Create([
                    'name'      => $params_array['name'],
                    'surname'   => $params_array['surname'],
                    'email'     => $params_array['email'],
                    'password'  => $pwd,
                    'role'      => 'ROLE_ADMIN'
                ]);

                $data = array(
                    'status'    => 'success',
                    'code'      => 200,
                    'message'   => 'User has been created',
                    'user'      => $user
                );

            }

        } else {

            $data = array(
                'status'    => 'error',
                'code'      => 400,
                'message'   => 'Data sent is wrong',
            );

        }

        return response()->json($data, $data['code']);

    }

    public function login(Request $request)
    {

        $jwtAuth = new \JwtAuth();

        // Get post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        // Validate data
        $validate = \Validator::make($params_array, [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        if($validate->fails()) {

            $signup = array(
                'status'    => 'error',
                'code'      => 404,
                'message'   => 'User could not login',
                'errors'    => $validate->errors()
            );

        } else {

            // Encrypt password
            $pwd = hash('sha256', $params->password);

            // return token
            $signup = $jwtAuth->signup($params->email, $pwd);

            if(!empty($params->getToken))
            {

                $signup = $jwtAuth->signup($params->email, $pwd, true);

            }

        }

        return response()->json($signup, 200);

    }

    public function update(Request $request)
    {
        $jwtAuth = new \JwtAuth();

        $token = $request->header('Authorization');
        $checkToken = $jwtAuth->checkToken($token);

        // Get data by post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (/*$checkToken && */!empty($params_array) && !empty($params)) {

            // Get user identified
            $user = $jwtAuth->checkToken($token, true);

            // Validate data
            $validate = \Validator::make($params_array, [
                'name'      => 'required|alpha',
                'surname'   => 'required|alpha',
                'email'     => 'required|email|unique:users,'.$user->sub
            ]);

            // Remove fields i dont want
            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);

            // Update user
            $user_update = User::where('id', $user->sub)->update($params_array);

            // Resturn response
            $data = array(
                'code'      => 200,
                'status'    => 'success',
                'user'      => $user,
                'changes'   => $params_array,
                'message'   => 'User updated'
            );

        } else {

            $data = array(
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'Data sent is wrong'
            );

        }

        return response()->json($data, $data['code']);

    }

    public function upload(Request $request)
    {

        // Get data
        $image = $request->file('file0');

        // Validate image
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        // Save image
        if(!$image || $validate->fails()) {

            $data = array(
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'Error uploading file'
            );

        } else {

            $image_name = time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));

            $data = array(
                'code'      => 200,
                'status'    => 'success',
                'image'     => $image_name
            );

        }

        return response()->json($data, $data['code']);

    }

    public function getImage($filename)
    {

        $isset = \Storage::disk('users')->exists($filename);

        if($isset) {

            $file = \Storage::disk('users')->get($filename);

            return new response($file, 200);

        } else {

            $data = array(
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'Image does not exists'
            );

            return response()->json($data, $data['code']);

        }

    }

    public function detail($id)
    {
        $user = User::find($id);

        if(is_object($user)) {

            $data = array (
                'code'      => 200,
                'status'    => 'success',
                'user'      => $user
            );

        } else {

            $data = array (
                'code'      => 404,
                'status'    => 'error',
                'message'      => 'User does not exists'
            );

        }

        return response()->json($data, $data['code']);

    }
}
