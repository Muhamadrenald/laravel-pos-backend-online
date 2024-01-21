<?php

namespace App\Http\Controllers;

// use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index(Request $request)
    {

        // $users = \App\Models\User::paginate(10);
        $users = DB::table('users')
            ->when($request->input('name'), function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        return view('pages.users.create');
    }

    // public function store(StoreUserRequest $request)
    // public function store(Request $request)
    // {
    //     // dd($request->all());

    //     $data = $request->all();
    //     $data['password'] = Hash::make($request->password);
    //     \App\Models\User::create($data);
    //     return redirect()->route('user.index')->with('success', 'User successfully created');
    // }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'new_confirm_password' => 'nullable|min:8|same:password', // Validasi konfirmasi password baru
            'phone' => 'nullable|numeric',
            'roles' => 'required|in:admin,staff,user',
        ], [
            'password.min' => 'The password must be at least 8 characters.',
            'password.required' => 'The password field is required.',
            'email.unique' => 'The email address is already in use.',
            'new_confirm_password.same' => 'The new password confirmation must match the entered password.',
            'new_confirm_password.required' => 'The new password confirmation field is required.',
            'new_confirm_password.min' => 'The new password confirmation must be at least 8 characters.'
            // Tambahkan pesan validasi kustom sesuai kebutuhan
        ]);

        // Hash password sebelum menyimpan ke database
        $data['password'] = Hash::make($data['password']);

        // Membuat user baru
        \App\Models\User::create($data);

        return redirect()->route('user.index')->with('success', 'User successfully created');
    }


    public function edit($id)
    {
        $user = \App\Models\User::findOrFail($id);
        return view('pages.users.edit', compact('user'));
    }

    // public function update(UpdateUserRequest $request, User $user)
    // {
    //     $data = $request->validated();
    //     $user->update($data);
    //     return redirect()->route('user.index')->with('success', 'User successfully updated');
    // }
    // public function update(UpdateUserRequest $request, User $user)
    // {
    //     $data = $request->all();

    //     // Periksa apakah ada perubahan pada password
    //     if (!empty($data['password'])) {
    //         $data['password'] = Hash::make($data['password']);
    //     } else {
    //         // Jika tidak ada perubahan pada password, hapus kunci password dari data yang akan diupdate
    //         unset($data['password']);
    //     }

    //     $user->update($data);
    //     return redirect()->route('user.index')->with('success', 'User successfully updated');
    // }
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'nullable|min:8', // Validasi password baru
            'new_confirm_password' => 'nullable|min:8|same:password', // Validasi konfirmasi password baru
            'phone' => 'nullable|numeric',
            'roles' => 'required|in:admin,staff,user',
        ], [
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'new_confirm_password.same' => 'The new password confirmation must match the entered password.',
            'new_confirm_password.required' => 'The new password confirmation field is required.',
            'new_confirm_password.min' => 'The new password confirmation must be at least 8 characters.'
        ]);

        // Jika password baru tidak kosong, hash password baru
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Jika tidak ada perubahan pada password, hapus kunci password dari data yang akan diupdate
            unset($data['password']);
        }

        $user->update($data);
        return redirect()->route('user.index')->with('success', 'User successfully updated');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User successfully deleted');
    }
}
