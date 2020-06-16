<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest\AuthToken;

use App\Core\Application\Command\AuthToken\CreateAuthToken\CreateAuthTokenCommand;
use App\Shared\Infrastructure\Http\HttpSpec;
use App\Shared\Infrastructure\Http\ParamFetcher;
use Swagger\Annotations as SWG;
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
     * @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JSON Payload",
     *          required=true,
     *          format="application/json",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="username", type="string"),
     *              @SWG\Property(property="password", type="string"),
     *          )
     * )
     *
     * @SWG\Response(
     *     response=Response::HTTP_CREATED,
     *     description=HttpSpec::STR_HTTP_CREATED,
     *     @SWG\Schema(@SWG\Property(property="token", type="string"))
     * )
     * @SWG\Response(response=Response::HTTP_BAD_REQUEST, description=HttpSpec::STR_HTTP_BAD_REQUEST)
     * @SWG\Response(response=Response::HTTP_UNAUTHORIZED, description=HttpSpec::STR_HTTP_UNAUTHORIZED)
     *
     * @SWG\Tag(name="Auth token")
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
