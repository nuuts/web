<?php

namespace App\Http\Controllers;

use App\Repositories\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends Controller
{
    /**
     * @param CategoryRepository $categoryRepository
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        $this->authorize('index', $categoryRepository->model());

        $categories = $categoryRepository
            ->getWithNoParent();

        if ($categories === null) {
            throw new NotFoundHttpException();
        }

        return \response()->render('category.list', $categories->paginate());
    }

    /**
     * Category show
     *
     * @param string             $uuid
     * @param CategoryRepository $categoryRepository
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \LogicException
     */
    public function show(string $uuid, CategoryRepository $categoryRepository)
    {
        $this->authorize('show', $categoryRepository->model());

        $category = $categoryRepository
            ->with(['parent'])->find($uuid);

        if ($category === null) {
            throw new NotFoundHttpException();
        }

        return response()->render('category.show', $category->toArray());
    }
}
