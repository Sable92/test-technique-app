<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use App\Exceptions\CommentAlreadyExistsException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CommentAlreadyExistsExceptionTest extends TestCase
{
    #[Test]
    public function it_has_default_message(): void
    {
        $exception = new CommentAlreadyExistsException();

        $this->assertEquals('Vous avez déjà commenté ce profil', $exception->getMessage());
    }

    #[Test]
    public function it_accepts_custom_message(): void
    {
        $customMessage = 'Message personnalisé';
        $exception = new CommentAlreadyExistsException($customMessage);

        $this->assertEquals($customMessage, $exception->getMessage());
    }

    #[Test]
    public function it_renders_json_response_with_422_status(): void
    {
        $exception = new CommentAlreadyExistsException();

        $response = $exception->render();

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals([
            'message' => 'Vous avez déjà commenté ce profil'
        ], $response->getData(true));
    }

    #[Test]
    public function it_renders_json_response_with_custom_message(): void
    {
        $customMessage = 'Message de test';
        $exception = new CommentAlreadyExistsException($customMessage);

        $response = $exception->render();

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals([
            'message' => $customMessage
        ], $response->getData(true));
    }
}
