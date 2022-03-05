<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Request as ModelsRequest;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function get_user()
    {
        return Auth::user();
    }

    public function update_user(Request $request)
    {
        // Get current user
        $user = User::find(Auth::user()->id);
        // Validate the data submitted by user
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|max:255',
            'email' => 'required|email|max:225|' . Rule::unique('users')->ignore($user->id),
            'job' => 'required|string|max:50',
            'phonenumber' => 'required|numeric',
            'city' => 'required|string|max:50',
        ]);

        // if fails redirects back with errors
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $changes = [
            'fullname' => $request->fullname,
            'job' => $request->job,
            'email' => $request->email,
            'phonenumber' => $request->phonenumber,
            'city' => $request->city,
        ];

        $user->update($changes);

        $response = $changes + ['token' => $request->token] + [$user->id];
        return response($response, 200);
    }

    public function change_img(Request $request)
    {
        if ($img = $request->img) {
            $imageName  = Str::after($img->store('users', 'public'), '/');
        }
        return $imageName;
    }

    public function create_offer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:40',
            'description' => 'required|max:480|',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }


        $response = [
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => Auth::user()->id
        ];
        Offer::create($response);
        return response($response, 200);
    }


    public function create_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:40',
            'description' => 'required|max:480|',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }


        $response = [
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => Auth::user()->id
        ];
        ModelsRequest::create($response);
        return response($response, 200);
    }
}