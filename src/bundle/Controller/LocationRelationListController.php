<?php

declare(strict_types=1);

namespace Ibexa\LocationRelationListFieldTypeBundle\Controller;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Symfony\Component\HttpFoundation\Response;

class LocationRelationListController extends Controller
{
    /** @var LocationService  */
    private $locationService;

    public function __construct(
        LocationService $locationService

    ) {
        $this->locationService = $locationService;
    }


    public function locationRelationViewAction(int $locationId): Response
    {
        try {
            /** @var Content $content */
            $content = $this->locationService->loadLocation($locationId)->getContent();
        } catch (UnauthorizedException $exception) {
            return $this->render('@ibexadesign/content/relation_unauthorized.html.twig', [
                'contentId' => $content->id,
            ]);
        }

        return $this->render('@ibexadesign/content/location_relation.html.twig', [
            'content' => $content,
            'locationId' => $locationId,
            'contentType' => $content->getContentType(),
        ]);
    }
}
