<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest\Task;

use App\Core\Application\Command\Task\UpdateTask\UpdateTaskCommand;
use App\Shared\Infrastructure\Http\HttpSpec;
use App\Shared\Infrastructure\Http\ParamFetcher;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class UpdateTaskAction
{
    use HandleTrait;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    /**
     * @Route("/api/tasks/{id}", methods={"PUT"}, requirements={"id": "\d+"})
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
     *              @SWG\Property(property="title", type="string"),
     *              @SWG\Property(property="execution_day", type="string"),
     *              @SWG\Property(property="description", type="string"),
     *          )
     * )
     *
     * @SWG\Response(response=Response::HTTP_NO_CONTENT, description=HttpSpec::STR_HTTP_NO_CONTENT)
     * @SWG\Response(response=Response::HTTP_NOT_FOUND, description=HttpSpec::STR_HTTP_NOT_FOUND)
     * @SWG\Response(response=Response::HTTP_BAD_REQUEST, description=HttpSpec::STR_HTTP_BAD_REQUEST)
     * @SWG\Response(response=Response::HTTP_UNAUTHORIZED, description=HttpSpec::STR_HTTP_UNAUTHORIZED)
     *
     * @SWG\Tag(name="Task")
     */
    public function __invoke(Request $request): Response
    {
        $route = ParamFetcher::fromRequestAttributes($request);
        $body = ParamFetcher::fromRequestBody($request);

        $command = new UpdateTaskCommand(
            $route->getRequiredInt('id'),
            $body->getRequiredString('title'),
            $body->getRequiredDate('execution_day'),
            $body->getNullableString('description') ?? '',
        );

        $this->handle($command);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
