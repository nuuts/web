<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class RegisterController extends Controller
{

    /**
     * Return user register form
     *
     * @param string $referrerId
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function getRegisterForm(string $referrerId)
    {
        if (!User::find($referrerId) instanceof User) {
            return response()->error(Response::HTTP_NOT_FOUND);
        }
        return Auth::check() ?
            redirect()->route('profile', Auth::id()) :
            response()->render('auth.register', [
                'referrer_id' => $referrerId,
                'login' => null,
                'password' => null,
                'password_confirm' => null
            ]);
    }

    /**
     * User registration
     *
     * @param \App\Http\Requests\Auth\RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function register(\App\Http\Requests\Auth\RegisterRequest $request)
    {
        $user = new User();
        $user->setName($request->name)
            ->setEmail($request->email)
            ->setPassword(Hash::make($request->password));
        $user->referrer()->associate($request->referrer_id);
        $user->save();

        if ($request->wantsJson()) {
            return response()
                ->render('', $user->fresh(), Response::HTTP_CREATED)
                ->header('Location', sprintf('/users/%s', $user->id));
        }

        return redirect()->route('loginForm');

    }

}