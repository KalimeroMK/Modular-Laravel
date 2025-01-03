<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use App\Modules\User\Http\Requests\CreateUserRequest;
use App\Modules\User\Http\Requests\UpdateUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;


class UserController extends Controller
{
     /**
         * @return view
         */
        public function index():view
    {
        $items = User::all(); // Fetch all records
        return view('user::index', compact('items'));
    }

  /**
      * Show the form for creating a new resource.
      * @return view
      */
     public function create():view
    {
        return view('User::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateUserRequest  $request
     * @return View
     */
    public function store(CreateUserRequest $request): view
    {
        $user = User::create($request->validated());
        return view('user::show', compact('user'));
    }

    /**
     * Display the specified resource.
     *
     * @param  User $user
     * @return view
     */
    public function show(User $user) : view
    {
        return view('user::show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  User $user
     * @return view
     */
    public function edit(User $user): view
    {
        return view('user::edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateUserRequest  $request
     * @param  User $user
     * @return RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());
        return redirect()->route('$user.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('$user.index');
    }
}