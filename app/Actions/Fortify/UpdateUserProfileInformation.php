<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Rules\PhilippinePhoneNumber;
use App\Support\PhilippinePhone;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20', new PhilippinePhoneNumber],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        $phone = PhilippinePhone::normalize($input['phone']);

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input, $phone);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'phone' => $phone,
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input, string $phone): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $phone,
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
