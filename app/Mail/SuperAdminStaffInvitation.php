<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class SuperAdminStaffInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $roleName;
    public $loginUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $password, $roleName)
    {
        $this->user = $user;
        $this->password = $password;
        $this->roleName = $roleName;
        $this->loginUrl = route('login'); // Central login route
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Welcome to the Team - ' . config('app.name'))
                    ->view('emails.superadmin.staff-invitation');
    }
}
