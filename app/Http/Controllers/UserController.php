<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Request as ModelsRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Image;

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

        $response = $changes +
            ['token' => $request->bearerToken()] +
            ['id' => $user->id] +
            ['image' => 'http://localhost:8000/storage/users/' . $user->image,];
        return response($response, 200);
    }

    public function get_img()
    {
        $user = Auth::user();
        return $user->image;
    }

    public function change_password(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        if (Hash::check($request->old_password, $user->password)) {
            $new_password = Hash::make($request['new_password']);
            $user->update(['password' => $new_password]);
            return response(
                'تم تغيير كلمة السر بنجاح',
                200
            );
        }
    }

    public function change_img(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($request->hasFile('img')) {

            // First check if the user has an image and it exists in the storage
            // If it exists, delete it
            if ($user->image && Storage::disk('local')->exists('public/users/' . $user->image)) {
                unlink(storage_path('app/public/users/' . $user->image));
            }

            $image       = $request->file('img');
            $filename    = uniqid() . $image->getClientOriginalName();

            $image_resize = Image::make($image->getRealPath());
            $image_resize->resize(150, 150);

            $image_resize->stream(); // <-- Key point (Very Very Important)

            Storage::disk('local')->put('public/users' . '/' . $filename, $image_resize, 'public');
            $user->update(['image' => $filename]);
        }
        $changes = [
            'fullname' => $user->fullname,
            'job' => $user->job,
            'image' => 'http://localhost:8000/storage/users/' . $filename,
            'email' => $user->email,
            'phonenumber' => $user->phonenumber,
            'city' => $user->city,
            'token' => $request->bearerToken(),
        ];
        return response($changes, 200);
    }

    public function create_offer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:40',
            'description' => 'required',
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
            'description' => 'required',
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