<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Tests\Controllers;

use App\Models\LegacyMatch\Event;
use App\Models\LegacyMatch\LegacyMatch;
use App\Models\User;
use Tests\TestCase;

class LegacyMatchesControllerTest extends TestCase
{
    private $privateMatch;
    private $privateMatchRoute;
    private $publicMatch;
    private $publicMatchRoute;
    private $user;

    public function testPublicMatchLoggedOut() // OK
    {
        $this
            ->get($this->publicMatchRoute)
            ->assertStatus(200);
    }

    public function testPublicMatchLoggedInNotParticipated() // OK
    {
        $this
            ->actingAs($this->user)
            ->get($this->publicMatchRoute)
            ->assertStatus(200);
    }

    public function testPublicMatchLoggedInParticipated() // OK
    {
        Event::factory()->join()->create([
            'user_id' => $this->user->user_id,
            'match_id' => $this->publicMatch->match_id,
        ]);

        $this
            ->actingAs($this->user)
            ->get($this->publicMatchRoute)
            ->assertStatus(200);
    }

    public function testPrivateMatchLoggedOut() // Login Required
    {
        $this
            ->get($this->privateMatchRoute)
            ->assertSeeText('Please sign in to continue')
            ->assertStatus(401);
    }

    public function testPrivateMatchLoggedInNotParticipated() // Access Denied
    {
        $this
            ->actingAs($this->user)
            ->get($this->privateMatchRoute)
            ->assertStatus(403);
    }

    public function testPrivateMatchLoggedInHost() // OK
    {
        Event::factory()->stateCreate()->create([
            'user_id' => $this->user->user_id,
            'match_id' => $this->privateMatch->match_id,
        ]);

        $this
            ->actingAs($this->user)
            ->get($this->privateMatchRoute)
            ->assertStatus(200);
    }

    public function testPrivateMatchLoggedInParticipated() // OK
    {
        Event::factory()->join()->create([
            'user_id' => $this->user->user_id,
            'match_id' => $this->privateMatch->match_id,
        ]);

        $this
            ->actingAs($this->user)
            ->get($this->privateMatchRoute)
            ->assertStatus(200);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->publicMatch = LegacyMatch::factory()->create();
        Event::factory()->stateCreate()->create([
            'match_id' => $this->publicMatch->match_id,
        ]);
        $this->publicMatchRoute = route('matches.show', $this->publicMatch->match_id);

        $this->privateMatch = LegacyMatch::factory()->private()->create();
        Event::factory()->stateCreate()->create([
            'match_id' => $this->privateMatch->match_id,
        ]);
        $this->privateMatchRoute = route('matches.show', $this->privateMatch->match_id);
    }
}
