<?php

namespace App\Livewire\Components\SGA;

use App\Models\Position;
use App\Models\Principal;
use App\Models\Recipient;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Session;

class GenerateLetterComponent extends Component
{
    #[Validate([
        'month' => 'required',
        'principal' => 'required',
        'recipient' => 'required',
        'signature' => 'required',
    ])]
    public $month;
    public $hash;
    public $principal;
    public $recipient;
    public $recipientData;
    public $signature;

    #[Layout('layouts.app')]
    public function render()
    {
        $principalData = Principal::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
        $userData = User::where('is_active', true)
            ->orderBy('f_name', 'asc')
            ->get();
        return view('livewire.components.s-g-a.generate-letter-component', compact('principalData', 'userData'));
    }

    public function updatedPrincipal($value)
    {
        $this->recipientData = Recipient::where('principal_id', $value)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function generate()
    {
        $this->validate();
        Session::put('month', $this->month);
        Session::put('principalId', $this->principal);
        Session::put('recipientId', $this->recipient);
        Session::put('userlId', $this->signature);

        return $this->redirectRoute('generate.letter');
    }
}