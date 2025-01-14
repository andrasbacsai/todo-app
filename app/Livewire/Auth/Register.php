<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Settings\InstanceSettings;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Register extends Component
{
    #[Validate(['required', 'email'], ['email.required' => 'Email is required', 'email.email' => 'Invalid email'])]
    public string $email;

    #[Validate(['required'], ['password.required' => 'Password is required'])]
    public string $password;

    #[Validate(['required'], ['password_confirmation.required' => 'Password confirmation is required'])]
    public string $password_confirmation;

    #[Locked]
    public bool $isRootUser = false;

    public function mount(InstanceSettings $instanceSettings)
    {
        if (User::count() === 0) {
            $this->isRootUser = true;
        }
        if ($instanceSettings->is_registration_enabled === false) {
            return redirect()->route('dashboard');
        }
        if (config('app.env') !== 'production') {
            $this->email = 'test@example.com';
            $this->password = 'password';
            $this->password_confirmation = 'password';
        }
    }

    public function register(InstanceSettings $instanceSettings)
    {
        try {
            $this->validate();
            $emailExists = User::where('email', $this->email)->exists();
            if ($emailExists) {
                throw ValidationException::withMessages([
                    'email' => 'Email already exists',
                ]);
            }
            if ($this->password !== $this->password_confirmation) {
                throw ValidationException::withMessages([
                    'password_confirmation' => 'Password confirmation does not match',
                ]);
            }

            $user = User::create([
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'is_root_user' => $this->isRootUser,
            ]);
            if ($this->isRootUser) {
                $instanceSettings->is_registration_enabled = false;
                $instanceSettings->save();
            }

            auth()->login($user);

            return redirect()->intended(route('dashboard'));
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                $this->addError('email', $e->getMessage());
            } else {
                $this->addError('password_confirmation', "Something went wrong: {$e->getMessage()}");
            }
        }
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
