<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PsgcService;
use Illuminate\View\View;

class PsgcController extends Controller
{
    public function __construct(
        private readonly PsgcService $psgcService,
    ) {}

    public function index(): View
    {
        $regions = $this->psgcService->getRegions();

        return view('admin.psgc.index', compact('regions'));
    }
}
