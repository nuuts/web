<?php

namespace App\Services;

use App\Exceptions\Offer\Redemption\BadActivationCodeException;
use App\Exceptions\Offer\Redemption\CannotRedeemException;
use App\Models\ActivationCode;
use App\Models\NauModels\Offer;
use App\Models\NauModels\Redemption;
use App\Repositories\ActivationCodeRepository;
use App\Repositories\OfferRepository;
use Illuminate\Contracts\Auth\Access\Gate;
use OmniSynapse\CoreService\Exception\RequestException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class NauOffersService
 * NS: App\Services
 */
class NauOffersService implements OffersService
{
    private $activationCodeRepository;
    private $offerRepository;
    /**
     * @var Gate
     */
    private $gate;

    public function __construct(ActivationCodeRepository $activationCodeRepository, OfferRepository $offerRepository, Gate $gate)
    {
        $this->activationCodeRepository = $activationCodeRepository;
        $this->offerRepository          = $offerRepository;
        $this->gate                     = $gate;
    }

    /**
     * @param Offer  $offer
     * @param string $code
     *
     * @return Redemption
     * @throws BadActivationCodeException
     * @throws CannotRedeemException
     */
    public function redeemByOfferAndCode(Offer $offer, string $code): Redemption
    {
        $activationCode = $this->activationCodeRepository
            ->findByCodeAndOfferAndNotRedeemed($code, $offer);

        if (null === $activationCode) {
            throw new BadActivationCodeException($offer, $code);
        }

        return $this->redeem($activationCode);
    }

    /**
     * @param string $code
     *
     * @return ActivationCode
     * @throws BadActivationCodeException
     */
    public function getActivationCodeByCode(string $code): ActivationCode
    {
        $activationCode = $this->activationCodeRepository
            ->findByCodeAndNotRedeemed($code);

        if (null === $activationCode) {
            throw new BadActivationCodeException(null, $code);
        }

        return $activationCode;
    }

    /**
     * @param ActivationCode $activationCode
     *
     * @return Redemption
     * @throws BadActivationCodeException
     * @throws CannotRedeemException
     */
    public function redeemByActivationCode(ActivationCode $activationCode)
    {
        $offer = $activationCode->offer;
        if (null === $offer) {
            throw new BadActivationCodeException($offer, $activationCode->code);
        }

        return $this->redeem($activationCode);
    }

    /**
     * @param ActivationCode $activationCode
     *
     * @return Redemption
     * @throws CannotRedeemException
     */
    private function redeem(?ActivationCode $activationCode): Redemption
    {
        try {
            /** @var Redemption $redemption */
            $redemption = $activationCode->offer->redemptions()->create([
                'user_id' => $activationCode->getUserId()
            ]);
        } catch (RequestException $exception) {
            throw new HttpException($exception->getCode(), $exception->getMessage(), $exception);
        } catch (\Throwable $throwable) {
            throw new HttpException(503, $throwable);
        }

        if (null === $redemption->id) {
            throw new CannotRedeemException($activationCode->offer, $activationCode->getCode());
        }

        $activationCode->redemption()->associate($redemption)->update();

        return $redemption;
    }
}
