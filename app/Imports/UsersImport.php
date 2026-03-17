<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserInviteMail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class UsersImport implements ToModel, WithHeadingRow
{
    public $skipped = 0;  // Track skipped rows

    public function model(array $row)
    {
        // Skip if email is empty
        if (!isset($row['email']) || empty(trim($row['email']))) {
            $this->skipped++;
            return null;
        }

        // Validate email format
        if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            $this->skipped++;
            return null;
        }

        // Skip duplicate emails
        if (User::where('email', $row['email'])->exists()) {
            $this->skipped++;
            return null;
        }

        // Generate temporary password
        $tempPassword = Str::random(8);

        // Create user
        $user = new User([
            'name'     => $row['name'] ?? null,
            'email'    => $row['email'],
            'password' => Hash::make($tempPassword),
        ]);

        // After save send email
        $user->save();

        // Send Invite Email
        Mail::to($user->email)->send(new UserInviteMail($user, $tempPassword));

        return $user;
    }
}
