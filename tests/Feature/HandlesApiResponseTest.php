<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Concerns\HandlesApiResponse;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class HandlesApiResponseTest extends TestCase
{
    public function test_success_response_structure(): void
    {
        $controller = new class
        {
            use HandlesApiResponse;

            public function callSuccess()
            {
                return $this->respondSuccess(['foo' => 'bar'], 'Success!', 201);
            }
        };
        $response = $controller->callSuccess();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $data = $response->getData(true);
        $this->assertTrue($data['success']);
        $this->assertEquals('Success!', $data['message']);
        $this->assertEquals(['foo' => 'bar'], $data['data']);
        $this->assertNull($data['errors']);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_error_response_structure(): void
    {
        $controller = new class
        {
            use HandlesApiResponse;

            public function callError()
            {
                return $this->respondError('Error!', 400, ['foo' => 'bar']);
            }
        };
        $response = $controller->callError();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $data = $response->getData(true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Error!', $data['message']);
        $this->assertNull($data['data']);
        $this->assertIsArray($data['errors']);
        $this->assertEquals(400, $response->getStatusCode());
    }
}
