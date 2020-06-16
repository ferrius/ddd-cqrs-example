<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest\Task;

use App\Core\Application\Command\Task\MakeTaskDone\MakeTaskDoneCommand;
use App\Shared\Infrastructure\Http\HttpSpec;
use App\Shared\Infrastructure\Http\ParamFetcher;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class MakeTaskDoneAction
{
    use HandleTrait;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    /**
     * @Route("/api/tasks/{id}/status/done", methods={"PATCH"})
     *
     * @SWG\Patch(description="Make task done")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @SWG\Response(response=Response::HTTP_NO_CONTENT, description=HttpSpec::STR_HTTP_NO_CONTENT)
     * @SWG\Response(response=Response::HTTP_NOT_FOUND, description=HttpSpec::STR_HTTP_NOT_FOUND)
     * @SWG\Response(response=Response::HTTP_UNAUTHORIZED, description=HttpSpec::STR_HTTP_UNAUTHORIZED)
     *
     * @SWG\Tag(name="Task")
     */
    public function __invoke(Request $request): Response
    {
        $route = ParamFetcher::fromRequestAttributes($request);

        $this->handle(new MakeTaskDoneCommand($route->getRequiredInt('id')));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
