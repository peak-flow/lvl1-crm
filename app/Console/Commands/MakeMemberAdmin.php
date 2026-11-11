<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('members:make-admin {email : The email address the member signed up with}')]
#[Description('Give a member admin powers')]
class MakeMemberAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $member = User::where('email', $this->argument('email'))->first();

        if ($member === null) {
            $this->error("No member has the email address [{$this->argument('email')}].");

            return self::FAILURE;
        }

        $member->is_admin = true;
        $member->save();

        $this->info("{$member->name} is now an admin.");

        return self::SUCCESS;
    }
}
