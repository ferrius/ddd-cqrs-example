<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest\AuthToken;

use App\Core\Application\Command\AuthToken\CreateAuthToken\CreateAuthTokenCommand;
use App\Shared\Infrastructure\Http\HttpSpec;
use App\Shared\Infrastructure\Http\ParamFetcher;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class CreateAuthTokenAction
{
    use HandleTrait;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    /**
     * @Route("/api/auth-token", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     *
     * @OA\Parameter(
     *          name="body",
     *          in="body",
     *          description="JSON Payload",
     *          required=true,
     *          content="application/json",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(property="username", type="string"),
     *              @OA\Property(property="password", type="string"),
     *          )
     * )
     *
     * @OA\Response(
     *     response=Response::HTTP_CREATED,
     *     description=HttpSpec::STR_HTTP_CREATED,
     *     @OA\Schema(@OA\Property(property="token", type="string"))
     * )
     * @OA\Response(response=Response::HTTP_BAD_REQUEST, description=HttpSpec::STR_HTTP_BAD_REQUEST)
     * @OA\Response(response=Response::HTTP_UNAUTHORIZED, description=HttpSpec::STR_HTTP_UNAUTHORIZED)
     *
     * @OA\Tag(name="Auth token")
     */
    public function __invoke(Request $request): Response
    {
        $paramFetcher = ParamFetcher::fromRequestBody($request);

        $token = $this->handle(new CreateAuthTokenCommand(
            $paramFetcher->getRequiredString('username'),
            $paramFetcher->getRequiredString('password')
        ));

        return new JsonResponse(['token' => $token], Response::HTTP_CREATED);
    }
}
