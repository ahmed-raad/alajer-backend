<?php

namespace App\Http\Controllers;

use App\Http\Resources\RequestResource;
use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function get_request()
    {
        $requests = ModelsRequest::latest();
        return RequestResource::collection($requests);
    }
}