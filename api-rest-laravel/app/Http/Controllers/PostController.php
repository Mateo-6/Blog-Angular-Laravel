<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller
{
    public function __construct()
    {

        $this->middleware('api.auth', ['except' => [
            'index',
            'show',
            'getImage',
            'getPostsByCategory',
            'getPostsByUser'
            ]]);

    }

    public function index()
    {

        $posts = Post::all()->load('category');

        return response()->json([
            'code'      => 200,
            'status'    => 'Success',
            'posts'     => $posts
        ], 200);

    }

    public function show($id)
    {

        $post = Post::find($id)->load('category')
                               ->load('users');

        if(is_object($post)) {

            $data = array(
                'code'      => 200,
                'status'    => 'Success',
                'posts'     => $post
            );

        } else {

            $data = array(
                'code'      => 400,
                'status'    => 'Error',
                'message'   => 'Post does not exists'
            );

        }

        return response()->json($data, $data['code']);

    }


    public function store(Request $request)
    {

        // Get data by post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);


        if(!empty($params_array)) {

            // Get identified user
            $jwtAuth = new JwtAuth();
            $token = $request->header('Authorization', null);
            $user = $jwtAuth->checkToken($token, true);

            // Validate data
            $validate = \Validator::make($params_array, [
                'title'         => 'required',
                'content'       => 'required',
                'category_id'   => 'required',
                'image'         => 'required'
            ]);

            if ($validate->fails()) {

                $data = array(
                    'code'      => 400,
                    'status'    => 'Error',
                    'message'   => 'Post was not saved',
                    'error'     => $validate->errors()
                );

            } else {

                // Save post
                $post = Post::Create([
                    'user_id'       => $user->sub,
                    'category_id'   => $params->category_id,
                    'title'         => $params->title,
                    'content'       => $params->content,
                    'image'         => $params->image
                ]);

                $data = array(
                    'code'      => 200,
                    'status'    => 'Success',
                    'post'      =>  $post
                );

            }

        } else {

            $data = array(
                'code'      => 400,
                'status'    => 'Error',
                'message'   => 'Error sending data'
            );

        }

        // Return response
        return response()->json($data, $data['code']);

    }

    public function update($id, Request $request)
    {

        $user = $this->getIdentity($request);

        //Get data by post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        // Data to return
        $data = array(
            'code'      => 400,
            'status'    => 'Error',
            'message'   => 'Wron data'
        );

        if(!empty($params_array)) {

            $user = $this->getIdentity($request);
            // Validate data
            $validate = \Validator::make($params_array, [
                'title'         => 'required',
                'content'       => 'required',
                'category_id'   => 'required'
            ]);

            if($validate->fails()) {

                $data['errors'] = $validate->errors();
                return response()->json($data, $data['code']);

            }

            // Remove data i do not want
            unset($params_array['id']);
            //unset($params_array['user_id']);
            unset($params_array['created_at']);
            //unset($params_array['user']);

            // Update
            $post = Post::updateOrCreate([
                'id'            => $id,
                'user_id'       => $user->sub,
            ], [
                'title'         => $params->title,
                'content'       => $params->content,
                'category_id'   => $params->category_id,
                'image'         => $params->image
            ]);

            // Return response
            $data = array(
                'code'      => 200,
                'status'    => 'Success',
                'post'      => $post,
                'changes'   => $params_array
            );

        }

        return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $request)
    {

        $user = $this->getIdentity($request);

        // Get registry
        $post = Post::where('id', $id)
                    ->where('user_id', $user->sub)
                    ->first();

        if(!empty($post)) {

            // Remove
            $post->delete();

            // Return response
            $data = [
                'code'      => 200,
                'status'    => 'Succes',
                'post'      => $post
            ];

        } else {

            $data = [
                'code'      => 404,
                'status'    => 'Error',
                'message'   => 'Post does not exists'
            ];

        }

        return response()->json($data, $data['code']);

    }

    private function getIdentity($request)
    {
        // Get identified user
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);

        return $user;
    }

    public function upload(Request $request)
    {

        // Get image of the request
        $image = $request->file('file0');

        // Validate image
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        // Save image
        if(!$image || $validate->fails()) {

            $data = array(
                'code'      => 400,
                'status'    => 'Error',
                'message'   => 'Error uploading image'
            );

        } else {

            $image_name = time().$image->getClientOriginalName();

            \Storage::disk('images')->put($image_name, \File::get($image));

            $data = array(
                'code'      => 200,
                'status'    => 'Success',
                'image'     => $image_name
            );

        }

        // Return response
        return response()->json($data, $data['code']);
    }

    public function getImage($fileName)
    {

        // Check if file exists
        $isset = \Storage::disk('images')->exists($fileName);

        if($isset) {

            // Get image
            $file = \Storage::disk('images')->get($fileName);

            // Return image
            return new Response($file, 200);

        } else {

            $data = [
                'code'      => 404,
                'status'    => 'Error',
                'message'   => 'Image does not exists'
            ];

        }

        return response()->json($data, $data['code']);

    }

    public function getPostsByCategory($id)
    {

        $posts = Post::where('category_id', $id)->get();

        return response()->json([
            'status'        => 'Success',
            'posts'         => $posts
        ], 200);

    }

    public function getPostsByUser($id)
    {

        $posts = Post::where('user_id', $id)->get();

        return response()->json([
            'status'        => 'Success',
            'posts'         => $posts
        ], 200);


    }

}
