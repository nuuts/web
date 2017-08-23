<?php

namespace App\Http\Controllers\Advert;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Symfony\Component\HttpFoundation\Response;
use App\Models\NauModels\Offer;
use App\Http\Requests\Advert;

class OfferController extends Controller
{

    /**
     * Obtain a list of the offers that this user created
     * @return Response
     */
    public function index(): Response
    {
        return \response()->render('advert.offer.index', auth()->user()->getAccountFor(Currency::NAU)->offers()->paginate());
    }

    /**
     * Get the form/json data for creating a new offer.
     * @return Response
     */
    public function create(): Response
    {
        return \response()->render('advert.offer.create', (new Offer())->toArray());
    }

    /**
     * Send new offer data to core to store
     * @param  Advert\OfferRequest $request
     * @return Response
     */
    public function store(Advert\OfferRequest $request): Response
    {
        $newOffer = new Offer();
        $newOffer->account()->associate(auth()->user()->getAccountFor(Currency::NAU));
        $newOffer->create($request->toArray());

        return \response()->render('advert.offer.store',
            $newOffer->toArray(),
            Response::HTTP_ACCEPTED,
            route('advert.offers.index'));
    }

    /**
     * Get offer full info(for Advert) by it uuid
     * @param string $offerUuid
     * @return Response
     */
    public function show(string $offerUuid): Response
    {
        $offer = Offer::firstOrFail($offerUuid);

        if ($offer->isOwner(auth()->user())) {
            return \response()->render('advert.offer.show', $offer->toArray());
        }
        return \response()->error(Response::HTTP_NOT_FOUND, trans('errors.offer_not_found'));
    }
}