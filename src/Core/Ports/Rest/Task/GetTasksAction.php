<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest\Task;

use App\Core\Application\Query\Task\DTO\TaskDTO;
use App\Core\Application\Query\Task\GetTasks\GetTasksQuery;
use App\Shared\Infrastructure\Http\HttpSpec;
use App\Shared\Infrastructure\Http\ParamFetcher;
use App\Shared\Infrastructure\ValueObject\PaginatedData;
use App\Shared\Infrastructure\ValueObject\Pagination;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class GetTasksAction
{
    use HandleTrait;

    private NormalizerInterface $normalizer;

    public function __construct(MessageBusInterface $queryBus, NormalizerInterface $normalizer)
    {
        $this->messageBus = $queryBus;
        $this->normalizer = $normalizer;
    }

    /**
     * @Route("/api/tasks", methods={"GET"})
     *
     * @param Request $request
     *
     * @return Response
     *
     * @OA\Parameter(
     *     name="execution_day",
     *     in="query",
     *     description="Search phrase text",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="search",
     *     in="query",
     *     description="Search phrase text",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Number of result items",
     *     @OA\Schema(type="integer", default=Pagination::DEFAULT_LIMIT)
     * )
     * @OA\Parameter(
     *     name="offset",
     *     in="query",
     *     description="First result offset",
     *     @OA\Schema(type="integer", default=Pagination::DEFAULT_OFFSET)
     * )
     * @OA\Response(
     *     response=Response::HTTP_OK,
     *     description=HttpSpec::STR_HTTP_OK,
     *     @OA\Schema(type="array", @OA\Items(ref=@Model(type=TaskDTO::class, groups={"task_view"})))
     * )
     * @OA\Response(response=Response::HTTP_BAD_REQUEST, description=HttpSpec::STR_HTTP_BAD_REQUEST)
     * @OA\Response(response=Response::HTTP_UNAUTHORIZED, description=HttpSpec::STR_HTTP_UNAUTHORIZED)
     *
     * @OA\Tag(name="Task")
     */
    public function __invoke(Request $request): Response
    {
        $query = ParamFetcher::fromRequestQuery($request);

        $query = new GetTasksQuery(
            Pagination::fromRequest($request),
            $query->getNullableDate('execution_day'),
            $query->getNullableString('search')
        );

        /** @var PaginatedData $paginatedData */
        $paginatedData = $this->handle($query);

        return new JsonResponse(
            $this->normalizer->normalize($paginatedData->getData(), '', ['groups' => 'task_view']),
            Response::HTTP_OK,
            [HttpSpec::HEADER_X_ITEMS_COUNT => $paginatedData->getCount()]
        );
    }
}
