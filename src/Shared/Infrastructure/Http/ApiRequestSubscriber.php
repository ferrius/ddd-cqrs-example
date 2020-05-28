<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ApiRequestSubscriber implements EventSubscriberInterface
{
    private const DEFAULT_JSON_DEPTH = 512;

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => 'onRequest'];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (\is_resource($request->getContent())
            || $request->getContent() === ''
            || strpos($request->getPathInfo(), '/api/doc') === 0
            || strpos($request->getPathInfo(), '/api/') !== 0) {
            return;
        }

        if ($request->getContentType() !== 'json') {
            $event->setResponse(new JsonResponse('Invalid content type', Response::HTTP_BAD_REQUEST));

            return;
        }

        try {
            $requestContent = json_decode($request->getContent(), true, self::DEFAULT_JSON_DEPTH, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $event->setResponse(new JsonResponse('Invalid json string', Response::HTTP_BAD_REQUEST));

            return;
        }

        if (\is_array($requestContent)) {
            $request->request->replace($requestContent);
        }
    }
}
