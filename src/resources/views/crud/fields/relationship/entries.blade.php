{{--
    This field is a switchboard for the "real" field that is a repeatable
    Based on developer preferences and the relation type we "guess" the best solution
    we can provide for the user and setup some defaults for them.
    One of the things that we take care, is adding the "pivot selector field", that is the link with
    the current crud and pivot entries, in this scenario is used with other pivot fields in a repeatable container.
--}}

@php
    $field['type'] = 'repeatable';
    $field['fields'] = $field['pivotFields'];
    $field['reorder'] = $field['reorder'] ?? false;
    $pivotSelectorField = $field['pivotSelect'] ?? [];
    $inline_create = !isset($inlineCreate) && isset($pivotSelectorField['inline_create']) ? $pivotSelectorField['inline_create'] : false;
    $pivotSelectorField['name'] = $field['name'];
    $pivotSelectorField['is_pivot_select'] = true;
    $pivotSelectorField['multiple'] = false;
    $pivotSelectorField['allows_null'] = false;
    $pivotSelectorField['entity'] = $field['entity'];
    $pivotSelectorField['model'] = $field['model'];
    
    $pivotSelectorField['ajax'] = $pivotSelectorField['ajax'] ?? false;
    $pivotSelectorField['data_source'] = $pivotSelectorField['data_source'] ?? isset($pivotSelectorField['ajax']) && $pivotSelectorField['ajax'] ? url($crud->route.'/fetch/'.$field['entity']) : 'false';
    $pivotSelectorField['minimum_input_length'] = $pivotSelectorField['minimum_input_length'] ?? 2;
    $pivotSelectorField['delay'] = $pivotSelectorField['delay'] ?? 500;
    $pivotSelectorField['placeholder'] = $pivotSelectorField['placeholder'] ?? trans('backpack::crud.select_entry');
    $pivotSelectorField['type'] = $inline_create !== false ? 'fetch_or_create' : ($pivotSelectorField['ajax'] ? 'fetch' : 'select');
    $pivotSelectorField['view_namespace'] = 'crud::fields.relationship';
    $pivotSelectorField['attribute'] = $pivotSelectorField['attribute'] ?? (new $field['model'])->identifiableAttribute();
    
    
    if($inline_create) {
        $field['inline_create'] = $inline_create;
    }
    switch ($field['relation_type']) {
        case 'MorphToMany':
        case 'BelongsToMany':
            $field['fields'] = Arr::prepend($field['fields'], $pivotSelectorField);
            break;
        case 'MorphMany':
        case 'HasMany':
            if(isset($entry)) {
                $field['fields'] = Arr::prepend($field['fields'], [
                    'name' => $entry->{$field['name']}()->getLocalKeyName(),
                    'type' => 'hidden',
                ]);
            }
            break;
    }
@endphp

@include('crud::fields.repeatable')