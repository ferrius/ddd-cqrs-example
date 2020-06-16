<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest\Task;

use App\Core\Application\Command\Task\CreateTask\CreateTaskCommand;
use App\Shared\Infrastructure\Http\HttpSpec;
use App\Shared\Infrastructure\Http\ParamFetcher;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

final class CreateTaskAction
{
    use HandleTrait;

    private RouterInterface $router;

    public function __construct(MessageBusInterface $commandBus, RouterInterface $router)
    {
        $this->messageBus = $commandBus;
        $this->router = $router;
    }

    /**
     * @Route("/api/tasks", methods={"POST"})
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
     * @SWG\Response(response=Response::HTTP_CREATED, description=HttpSpec::STR_HTTP_CREATED)
     * @SWG\Response(response=Response::HTTP_BAD_REQUEST, description=HttpSpec::STR_HTTP_BAD_REQUEST)
     * @SWG\Response(response=Response::HTTP_UNAUTHORIZED, description=HttpSpec::STR_HTTP_UNAUTHORIZED)
     *
     * @SWG\Tag(name="Task")
     */
    public function __invoke(Request $request): Response
    {
        $paramFetcher = ParamFetcher::fromRequestBody($request);

        $command = new CreateTaskCommand(
            $paramFetcher->getRequiredString('title'),
            $paramFetcher->getRequiredDate('execution_day'),
            $paramFetcher->getNullableString('description') ?? '',
        );

        $id = $this->handle($command);
        $resourceUrl = $this->router->generate('api_get_task', ['id' => $id]);

        return new JsonResponse(null, Response::HTTP_CREATED, [HttpSpec::HEADER_LOCATION => $resourceUrl]);
    }
}
