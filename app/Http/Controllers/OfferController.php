<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Http\Resources\OfferResource;

class OfferController extends Controller
{
    public function get_offer()
    {
        $offers = Offer::all();
        return OfferResource::collection($offers);
    }
}