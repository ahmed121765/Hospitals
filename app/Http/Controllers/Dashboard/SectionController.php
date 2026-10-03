<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSectionRequest;
use App\Interfaces\Sections\SectionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{

    private $Sections;

    public function __construct(SectionRepositoryInterface $Sections)
    {
        $this->Sections = $Sections;
    }

    public function index()
    {
        return $this->Sections->index();

    }

    public function show($id)
    {
        return $this->Sections->show($id);
    }


    public function store(StoreSectionRequest $request)
    {
        return $this->Sections->store($request);
    }


    public function update(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'exists:sections,id'],
            'name' => ['required', 'string', 'max:255', Rule::unique('section_translations', 'name')->where('locale', app()->getLocale())->ignore($request->id, 'section_id')],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
        return $this->Sections->update($request);
    }


    public function destroy(Request $request)
    {
        $request->validate(['id' => ['required', 'integer', 'exists:sections,id']]);
        return $this->Sections->destroy($request);
    }
}
