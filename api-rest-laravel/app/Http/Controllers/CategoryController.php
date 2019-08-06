<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index()
    {

        $categories = Category::all();

        return response()->json([
            'code'          => 200,
            'status'        => 'Success',
            'categories'    => $categories
        ]);

    }

    public function show($id)
    {

        $category = Category::find($id);

        if(is_object($category)) {

            $data = ([
                'code'          => 200,
                'status'        => 'Success',
                'category'      => $category
            ]);

        } else {

            $data = ([
                'code'          => 400,
                'status'        => 'Error',
                'message'      => 'Category does not exists'
            ]);

        }

        return response()->json($data, $data['code']);

    }

    public function store(Request $request)
    {

        // Get data by post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)) {
            // Validate data
            $validate = \Validator::make($params_array, [
                'name' => 'required'
            ]);

            if($validate->fails()) {

                $data = [
                    'code'      => 400,
                    'status'    => 'Error',
                    'message'   => 'The category was not saved'
                ];

            } else {

                // Save category
                $category = Category::create([
                    'name' => $params_array['name']
                ]);

                $data = [
                    'code'      => 200,
                    'status'    => 'success',
                    'category'  => $category,
                    'message'   => 'The category was saved'
                ];

            }

        } else {

            $data = [
                'code'      => 400,
                'status'    => 'Error',
                'message'   => 'Category not submitted'
            ];

        }
        // Return response
        return response()->json($data, $data['code']);

    }

    public function Update($id, Request $request)
    {

        // Get data by post
        $json =  $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)) {

            // Validate data
            $validate = \Validator::make($params_array, [
                'name' => 'required'
            ]);

            // Remove data i dont want
            unset($params_array['id']);
            unset($params_array['created_at']);

            // Update data
            $category = Category::where('id', $id)->update($params_array);

            $data = [
                'code'      => 200,
                'status'    => 'Success',
                'category'  => $params_array
            ];


        } else {

            $data = [
                'code'      => 400,
                'status'    => 'Error',
                'message'   => 'Category not submitted'
            ];

        }

        // Return response
        return response()->json($data, $data['code']);

    }
}
