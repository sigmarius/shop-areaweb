<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UploadAvatarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn(false);
    }

    #[Test]
    public function avatar_uploaded_successfully(): void
    {
        $response = $this->postJson(route('api.v1.user.avatar.upload'), [
            'avatar' => UploadedFile::fake()
                ->image('avatar.jpg'),
        ]);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'subscribers',
                'publications',
                'avatar',
                'about',
                'isVerified',
                'registeredAt'
            ],
        ]);

        $this->assertIsString($response->json('data.avatar'));
    }

    #[Test]
    public function validation_type_successfully()
    {
        $response = $this->postJson(route('api.v1.user.avatar.upload'), [
            'avatar' => UploadedFile::fake()
                ->image('avatar.gif')
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['avatar']);
    }

    #[Test]
    public function validation_size_successfully()
    {
        $response = $this->postJson(route('api.v1.user.avatar.upload'), [
            'avatar' => UploadedFile::fake()
                ->image('avatar.png')
                ->size(10000),
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['avatar']);
    }
}
