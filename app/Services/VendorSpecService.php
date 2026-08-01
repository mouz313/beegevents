<?php

namespace App\Services;

use App\Models\VendorProfile;
use App\Models\VendorSpec;
use Illuminate\Http\Request;

class VendorSpecService
{
    /**
     * Build validation rules for a vendor type's fields (including common
     * contact/legal fields), keyed by the form input names.
     */
    public static function rulesFor(string $type): array
    {
        $rules = [];
        $def = config("vendor-specs.types.{$type}");
        if (!$def) {
            return $rules;
        }

        foreach ($def['fields'] as $field) {
            $key = $field['key'];
            $base = !empty($field['flat']) ? $key : "specs.{$key}";

            if (($field['type'] ?? 'text') === 'file') {
                $rules[$base] = ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'];
                continue;
            }

            $fieldRules = [!empty($field['required']) ? 'required' : 'nullable'];

            switch ($field['type'] ?? 'text') {
                case 'number':
                    $fieldRules[] = 'numeric';
                    if (isset($field['min'])) {
                        $fieldRules[] = 'min:' . $field['min'];
                    }
                    break;
                case 'multi_select':
                    $fieldRules[] = 'array';
                    $fieldRules[] = 'max:20';
                    break;
                case 'checkbox':
                    $fieldRules[] = 'boolean';
                    break;
                default:
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:1000';
            }

            $rules[$base] = $fieldRules;
        }

        foreach (config('vendor-specs.common_fields', []) as $field) {
            $key = $field['key'];

            if (($field['type'] ?? 'text') === 'file') {
                $rules[$key] = ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'];
                continue;
            }

            $rules[$key] = [
                !empty($field['required']) ? 'required' : 'nullable',
                'string',
                'max:255',
            ];
        }

        return $rules;
    }

    /**
     * Persist the submitted type-specific data across all three layers:
     * flat columns + per-type spec row + type_specs JSON mirror.
     */
    public static function save(VendorProfile $profile, Request $request): void
    {
        $type = $profile->vendor_type;
        $def = config("vendor-specs.types.{$type}");
        if (!$def) {
            return;
        }

        $input = $request->input();
        $specData = [];
        $flatData = [];
        $mirror = [];

        if ($request->hasFile('legal_doc')) {
            $flatData['legal_doc_path'] = $request->file('legal_doc')->store('legal-docs', 'public');
        }

        foreach ($def['fields'] as $field) {
            $key = $field['key'];
            $raw = $input['specs'][$key] ?? $input[$key] ?? null;
            $fieldType = $field['type'] ?? 'text';

            if ($fieldType === 'checkbox') {
                $val = (bool) $raw;
            } elseif ($fieldType === 'multi_select') {
                $val = $raw ? array_values(array_filter((array) $raw)) : [];
            } elseif ($fieldType === 'number') {
                $val = ($raw === null || $raw === '') ? null : (float) $raw;
                if (in_array($key, [
                    'floors_count', 'min_capacity', 'max_capacity',
                    'min_person_booking', 'max_person_booking', 'years_experience',
                    'team_size', 'max_capacity_per_event', 'fleet_size',
                ], true)) {
                    $val = ($raw === null || $raw === '') ? null : (int) $raw;
                }
            } else {
                $val = ($raw === null || $raw === '') ? null : $raw;
            }

            if (!empty($field['flat'])) {
                if ($key !== 'legal_doc') {
                    $flatData[$key] = $val;
                }
            } else {
                $specData[$key] = $val;
            }
            $mirror[$key] = $val;
        }

        foreach (config('vendor-specs.common_fields', []) as $field) {
            $key = $field['key'];
            if ($key === 'legal_doc') {
                continue;
            }
            $raw = $input[$key] ?? null;
            $flatData[$key] = ($raw === null || $raw === '') ? null : $raw;
            $mirror[$key] = $flatData[$key];
        }

        $startingFrom = $def['starting_price_from'] ?? null;
        if ($startingFrom && array_key_exists($startingFrom, $mirror) && $mirror[$startingFrom] !== null) {
            $flatData['starting_price'] = $mirror[$startingFrom];
        }

        $expFrom = $def['years_experience_from'] ?? null;
        if ($expFrom && array_key_exists($expFrom, $mirror) && $mirror[$expFrom] !== null) {
            $flatData['years_experience'] = $mirror[$expFrom];
        }

        $profile->fill($flatData);
        $profile->type_specs = array_merge($profile->type_specs ?? [], $mirror);
        $profile->save();

        if (!empty($specData)) {
            $spec = new VendorSpec;
            $spec->setTable($type . '_specs');
            $spec->updateOrCreate(['vendor_profile_id' => $profile->id], $specData);
        }
    }
}
