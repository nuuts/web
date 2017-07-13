<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;


class RegisterController extends Controller
{

    public function getRegister()
    {
        return response()->render('auth.register');
    }

    /**
     * User registration
     *
     * @param \App\Http\Requests\Auth\RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function postRegister(\App\Http\Requests\Auth\RegisterRequest $request)
    {
        $user = new User();
        $user->setName($request->name)
            ->setEmail($request->email)
            ->setPassword(Hash::make($request->password));
        $user->save();

        if ($request->wantsJson()) {
            return response()
                ->render(null, $user->fresh(), 201)
                ->header('Location', sprintf('/users/%s', $user->id));
        }

        return redirect()->route('login');

    }

}