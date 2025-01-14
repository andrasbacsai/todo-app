<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Settings\InstanceSettings;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{
    #[Title('Login')]

    #[Validate(['required', 'email'], ['email.required' => 'Email is required', 'email.email' => 'Invalid email'])]
    public string $email;

    #[Validate(['required'], ['password.required' => 'Password is required'])]
    public string $password;

    public bool $isRegistrationEnabled = true;

    public function mount(InstanceSettings $settings)
    {
        if (User::count() === 0) {
            return redirect()->route('register');
        }
        $this->isRegistrationEnabled = $settings->is_registration_enabled;

        if (auth()->check()) {
            return redirect()->intended(route('dashboard'));
        }
        if (config('app.env') !== 'production') {
            $this->email = 'test@example.com';
            $this->password = 'password';
        }
    }

    public function login()
    {
        try {
            $this->validate();
            $response = auth()->attempt([
                'email' => $this->email,
                'password' => $this->password,
            ]);
            if ($response) {
                return redirect()->intended(route('dashboard'));
            }

            $this->addError('password', 'Invalid credentials');
        } catch (\Exception $e) {
            $this->addError('password', "Something went wrong: {$e->getMessage()}");
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
