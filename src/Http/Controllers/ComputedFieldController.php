<?php

namespace Upline\ComputedField\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;
use Upline\ComputedField\ComputedField;


class ComputedFieldController extends Controller
{
    public function calculate(NovaRequest $request)
    {
        $field = $this->getNovaField($request);

        if (empty($field)) {
            abort(404, "Unable to find the field required to calculate this value");
        }

        if(!($field instanceof ComputedField)){
            abort(400, "Found field is not computed");
        }


        $calculatedValue = call_user_func(
            $field->getComputedFunction(),
            $request->input('data'),
            $request
        );

        return response()->json($calculatedValue);
    }

    private function getNovaField(NovaRequest $request)
    {
        $field = $request->newResource()
            ->availableFields($request)
            ->where('showOnCreation', '=', true)
            ->where('attribute', '=', $request->input('fieldName'))
            ->first();

        if ($field) {
            return $field;
        }

        return $request->newResource()
            ->availableFields($request)
            ->where('showOnUpdate', '=', true)
            ->where('attribute', '=', $request->input('fieldName'))
            ->first();
    }
}
