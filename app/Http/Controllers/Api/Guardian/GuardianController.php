<?php

namespace App\Http\Controllers\Api\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Http\Requests\Api\PasswordChangeRequest;

class GuardianController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Get logged guardian profile
     *
     * @return \Illuminate\Http\Response
     */
    function profile()
    {
        $data = auth()->user()->loggedProfile();

        return $this->sendResponse($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'name'      => 'required',
                'mobile'    => 'required',
                'email'     => 'nullable',
            ]);

            auth()->user()->update($request->validated());

            return $this->sendResponse([], 200, 'Successfully updated!');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Change password
     *
     * @param  \App\Http\Requests\PasswordChangeRequest $request
     * @return \Illuminate\Http\Response
     */
    public function passwordChange(PasswordChangeRequest $request)
    {
        try {
            $this->authService->passwordChanged(Guardian::class, $request->validated());

            return $this->sendResponse([], 200, "Password change successfully!!");
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
