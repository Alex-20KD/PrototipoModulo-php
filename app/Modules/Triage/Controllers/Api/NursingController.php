<?php

namespace App\Modules\Triage\Controllers\Api;

use App\Modules\Triage\Models\VitalSign;
use App\Modules\Triage\Requests\StoreVitalSignApiRequest;
use App\Modules\Triage\Resources\VitalSignResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Routing\Controller;

class NursingController extends Controller
{
    use ApiResponseTrait;

    public function store(StoreVitalSignApiRequest $request)
    {
        $vitalSign = VitalSign::create(array_merge(
            $request->validated(),
            ['status' => 'pending']
        ));

        return $this->created(
            'Signos vitales registrados con éxito',
            new VitalSignResource($vitalSign)
        );
    }
}
