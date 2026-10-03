<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSingleServiceRequest;
use App\Http\Requests\UpdateSingleServiceRequest;
use App\Interfaces\Services\SingleServiceRepositoryInterface;
use Illuminate\Http\Request;

class SingleServiceController extends Controller
{

    private $SingleService;

    public function __construct(SingleServiceRepositoryInterface $SingleService)
    {
        $this->SingleService = $SingleService;
    }

    public function index()
    {
        return $this->SingleService->index();
    }


    public function store(StoreSingleServiceRequest $request)
    {
        return $this->SingleService->store($request);
    }


    public function update(UpdateSingleServiceRequest $request)
    {
        return $this->SingleService->update($request);

    }


    public function destroy(Request $request)
    {
        $request->validate(['id' => ['required', 'integer', 'exists:Services,id']]);
        return $this->SingleService->destroy($request);
    }
}
