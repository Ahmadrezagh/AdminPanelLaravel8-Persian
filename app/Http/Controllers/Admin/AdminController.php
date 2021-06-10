<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ((Auth::user()->isAdmin() && Auth::user()->can('Admin')) || Auth::user()->isSuperAdmin())
            {
                return $next($request);
            }else{
                abort(404);
            }
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admins = User::Admins()->latest()->get();
        $roles = Role::all();
        return view('admin.admins.index',compact('admins','roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.admins.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8' ],
            're_password' => ['required', 'string', 'min:8'],
        ]);
        if($validatedData->fails()){
            alert()->warning("در وارد کردن اطلاعات دقت نمایید");
            return back()->withErrors($validatedData)->withInput();
        }
        if($request->password != $request->re_password)
        {
            alert()->warning('رمز عبور و تکرار آن یکسان نیست');
            return back()->withErrors($validatedData)->withInput();
        }
         $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'type_id' => '2',
        'password' => Hash::make($request->password),
        ]);

        if($request->role)
        {
           $user->refreshRoles($request->role);
        }
        alert()->success('مدیر با موفقیت ایجاد شد');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return 'show';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8' ],
            're_password' => ['nullable', 'string', 'min:8'],
            'img' => 'image|mimes:jpeg,jpg,png',
        ]);
        if($validatedData->fails()){
            alert()->warning("در وارد کردن اطلاعات دقت نمایید");
            return back()->withErrors($validatedData)->withInput();
        }
        if($request->img)
        {
            $profile_img = upload_file($request->img , '/profiles/'.$id,$request->name);

        }
        if($request->password != null && $request->re_password != null)
        {
            if($request->password != $request->re_password)
        {
            alert()->warning('رمز عبور و تکرار آن یکسان نیست');
            return back()->withErrors($validatedData)->withInput();
        }
        }
         User::where('id','=',$id)->update([
        'name' => $request->name,
        'email' => $request->email,
        'password' => ($request->password != null)? Hash::make($request->password) : User::where('id','=',$id)->pluck('password')->first(),
        'profile' => ($request->img) ? $profile_img : User::where('id','=',$id)->pluck('profile')->first()
        ]);
        $user = User::where('id','=',$id)->first();
        if($request->role)
        {
           $user->refreshRoles($request->role);
        }
        alert()->success('مدیر با موفقیت ویرایش شد');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::where('id',$id)->delete();
        alert()->success('مدیر با موفقیت حذف شد');
        return back();
    }
}
