<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\SecureFileUploader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class SecureFileUploaderTest extends TestCase
{
    protected SecureFileUploader $uploader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uploader = new SecureFileUploader();
        Storage::fake('local');
    }

    /** @test */
    public function it_uploads_valid_images_successfully()
    {
        $file = UploadedFile::fake()->image('avatar.jpg');
        
        $path = $this->uploader->upload($file, 'avatars');

        Storage::disk('local')->assertExists($path);
        $this->assertStringStartsWith('avatars/', $path);
        
        // Verify UUID renaming
        $filename = basename($path);
        $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
        $this->assertTrue(Str::isUuid($filenameWithoutExt));
    }

    /** @test */
    public function it_rejects_invalid_mime_types()
    {
        // Create a fake text file masquerading as a jpg
        $file = UploadedFile::fake()->create('malicious.php.jpg', 100, 'text/x-php');

        $this->expectException(ValidationException::class);
        
        $this->uploader->upload($file);
    }

    /** @test */
    public function it_rejects_large_files()
    {
        $file = UploadedFile::fake()->image('large.jpg')->size(6000); // 6MB

        $this->expectException(ValidationException::class);
        
        $this->uploader->upload($file);
    }

    /** @test */
    public function it_uses_guess_extension_for_sanitization()
    {
        $file = UploadedFile::fake()->image('photo.jpeg');
        
        $path = $this->uploader->upload($file);
        
        // Even if we name it .jpeg, guessExtension might return .jpg or .jpeg depending on environment
        // But it should be consistent with valid image extensions
        $this->assertMatchesRegularExpression('/\.(jpg|jpeg|png|webp)$/i', $path);
    }
}
