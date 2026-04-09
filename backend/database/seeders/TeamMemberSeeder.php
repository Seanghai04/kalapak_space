<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'name' => 'Khat Vanna',
                'title' => 'Founder & Full-Stack Developer',
                'bio' => 'Passionate about building impactful software solutions. Leading the Kalapak Code Team vision.',
                'email' => 'admin@kalapak.dev',
                'github_url' => 'https://github.com/khatvanna',
                'is_founder' => true,
                'display_order' => 1,
                'is_visible' => true,
            ],
            [
                'name' => 'Rom Chamraeun',
                'title' => 'Co-Founder & Full-Stack Developer',
                'bio' => 'Building robust backend systems and creative frontend interfaces.',
                'email' => 'member@kalapak.dev',
                'github_url' => 'https://github.com/romchamraeun',
                'is_founder' => false,
                'display_order' => 2,
                'is_visible' => true,
            ],
            [
                'name' => 'Phuem Norng',
                'title' => 'Co-Founder & Full-Stack Developer',
                'bio' => 'Focused on clean architecture and scalable solutions.',
                'email' => 'norng@kalapak.dev',
                'github_url' => 'https://github.com/phuemnorng',
                'is_founder' => false,
                'display_order' => 3,
                'is_visible' => true,
            ],
            [
                'name' => 'Pheun Seanghai',
                'title' => 'Co-Founder & Full-Stack Developer',
                'bio' => 'Enthusiastic about modern web technologies and open source.',
                'email' => 'seanghai@kalapak.dev',
                'github_url' => 'https://github.com/pheunseanghai',
                'is_founder' => false,
                'display_order' => 4,
                'is_visible' => true,
            ],
        ];

        foreach ($members as $member) {
            TeamMember::updateOrCreate(['email' => $member['email']], $member);
        }
    }
}
