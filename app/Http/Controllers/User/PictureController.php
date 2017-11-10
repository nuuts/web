<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\AbstractPictureController;
use App\Http\Requests\Profile\PictureRequest;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Filesystem\Filesystem;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PictureController
 * @package App\Http\Controllers\Profile
 */
class PictureController extends AbstractPictureController
{
    const PROFILE_PICTURES_PATH = 'images/profile/pictures';

    public function __construct(
        ImageManager $imageManager,
        Filesystem $filesystem,
        AuthManager $authManager
    ) {
        parent::__construct($imageManager, $filesystem, $authManager);
    }

    /**
     * Saves profile image from request
     *
     * @param PictureRequest $request
     *
     * @return \Illuminate\Http\Response|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \LogicException
     * @throws \RuntimeException
     */
    public function store(PictureRequest $request)
    {
        $this->authorize('pictureStore', $this->auth->user());

        return $this->storeImageFor($request, $this->auth->id(), route('profile.picture.show'));
    }

    /**
     * Retrieves and responds with profile image
     *
     * @param string|null $userUuid
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \LogicException
     * @throws \RuntimeException
     */
    public function show(string $userUuid = null): Response
    {
        $userUuid = $userUuid ?? $this->auth->id();
        if ($userUuid === null) {
            throw new NotFoundHttpException();
        }

        $this->authorize('pictureShow', $this->auth->user());

        return $this->respondWithImageFor($userUuid);
    }

    /**
     * @return string
     */
    protected function getPath(): string
    {
        return self::PROFILE_PICTURES_PATH;
    }
}
