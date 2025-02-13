<?php

namespace App\Http\Controllers;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function Index()
    {
        return view('frontend.index');
    }

    public function userProfile()
    {
        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('frontend.dashboard.edit_profile', compact('profileData'));

    }


    public function userProfileUpdate(Request $request)
    {

        $id = Auth::user()->id;
        $data = User::find($id);
        $data->name = $request->name;
        $data->username = $request->username;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        if ($request->file('photo')) {
            $file = $request->file('photo');

            if (!empty($data->photo) && file_exists(public_path('upload/user_images/' . $data->photo))) {
                @unlink(public_path('upload/user_images/' . $data->photo));
            }

            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('upload/user_images/'), $filename);
            $data['photo'] = $filename;
        }

        $data->save();

        $notification = array(
            'message' => "User profile updated successfully!!",
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);

    }

    public function UserLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function UserChangePassword()
    {

        return view('frontend.dashboard.change_password');

    }

    public function userPasswordUpdate(Request $request)
    {
        /// Validation
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        if (!Hash::check($request->old_password, auth::user()->password)) {
            $notification = array(
                'message' => "Old password does not match!!",
                'alert-type' => 'error'
            );
            return back()->with($notification);
        }

        // Update User
        User::whereId(auth::user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        $notification = array(
            'message' => "Password updated successfully!!",
            'alert-type' => 'success'
        );
        return back()->with($notification);

    }

}
