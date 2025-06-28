<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     openapi="3.1.0",
 *     security={{"bearerAuth": {}}}
 * )
 *  @OA\Info(
 *      version="1.0.0",
 *      title="Adaptive education planning system"
 *  )
 * @OA\Server(
 *     url="https://gzprm.asapeducation.online/api",
 *     description="API server"
 * )
  * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     description="Basic Auth"
 * )
* @OA\Tag(
 *      name="api",
 *      description="All API endpoints"
 * )
 * @OA\Tag(
 *      name="auth",
 *      description="All Auth endpoints"
 * )
 * @OA\Tag(
 *      name="priorities",
 *      description="All Priorities endpoints"
 * )
 * 
 * The Controller.
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
