import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\ElderMedicalConditionController::store
* @see app/Http/Controllers/ElderMedicalConditionController.php:11
* @route '/elders/{elder}/medical-conditions'
*/
export const store = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/elders/{elder}/medical-conditions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ElderMedicalConditionController::store
* @see app/Http/Controllers/ElderMedicalConditionController.php:11
* @route '/elders/{elder}/medical-conditions'
*/
store.url = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { elder: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { elder: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            elder: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        elder: typeof args.elder === 'object'
        ? args.elder.id
        : args.elder,
    }

    return store.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ElderMedicalConditionController::store
* @see app/Http/Controllers/ElderMedicalConditionController.php:11
* @route '/elders/{elder}/medical-conditions'
*/
store.post = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderMedicalConditionController::store
* @see app/Http/Controllers/ElderMedicalConditionController.php:11
* @route '/elders/{elder}/medical-conditions'
*/
const storeForm = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderMedicalConditionController::store
* @see app/Http/Controllers/ElderMedicalConditionController.php:11
* @route '/elders/{elder}/medical-conditions'
*/
storeForm.post = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\ElderMedicalConditionController::update
* @see app/Http/Controllers/ElderMedicalConditionController.php:25
* @route '/elders/{elder}/medical-conditions/{condition}'
*/
export const update = (args: { elder: number | { id: number }, condition: number | { id: number } } | [elder: number | { id: number }, condition: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/elders/{elder}/medical-conditions/{condition}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\ElderMedicalConditionController::update
* @see app/Http/Controllers/ElderMedicalConditionController.php:25
* @route '/elders/{elder}/medical-conditions/{condition}'
*/
update.url = (args: { elder: number | { id: number }, condition: number | { id: number } } | [elder: number | { id: number }, condition: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            elder: args[0],
            condition: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        elder: typeof args.elder === 'object'
        ? args.elder.id
        : args.elder,
        condition: typeof args.condition === 'object'
        ? args.condition.id
        : args.condition,
    }

    return update.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace('{condition}', parsedArgs.condition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ElderMedicalConditionController::update
* @see app/Http/Controllers/ElderMedicalConditionController.php:25
* @route '/elders/{elder}/medical-conditions/{condition}'
*/
update.put = (args: { elder: number | { id: number }, condition: number | { id: number } } | [elder: number | { id: number }, condition: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\ElderMedicalConditionController::update
* @see app/Http/Controllers/ElderMedicalConditionController.php:25
* @route '/elders/{elder}/medical-conditions/{condition}'
*/
const updateForm = (args: { elder: number | { id: number }, condition: number | { id: number } } | [elder: number | { id: number }, condition: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderMedicalConditionController::update
* @see app/Http/Controllers/ElderMedicalConditionController.php:25
* @route '/elders/{elder}/medical-conditions/{condition}'
*/
updateForm.put = (args: { elder: number | { id: number }, condition: number | { id: number } } | [elder: number | { id: number }, condition: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\ElderMedicalConditionController::destroy
* @see app/Http/Controllers/ElderMedicalConditionController.php:43
* @route '/elders/{elder}/medical-conditions/{condition}'
*/
export const destroy = (args: { elder: number | { id: number }, condition: number | { id: number } } | [elder: number | { id: number }, condition: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/elders/{elder}/medical-conditions/{condition}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ElderMedicalConditionController::destroy
* @see app/Http/Controllers/ElderMedicalConditionController.php:43
* @route '/elders/{elder}/medical-conditions/{condition}'
*/
destroy.url = (args: { elder: number | { id: number }, condition: number | { id: number } } | [elder: number | { id: number }, condition: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            elder: args[0],
            condition: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        elder: typeof args.elder === 'object'
        ? args.elder.id
        : args.elder,
        condition: typeof args.condition === 'object'
        ? args.condition.id
        : args.condition,
    }

    return destroy.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace('{condition}', parsedArgs.condition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ElderMedicalConditionController::destroy
* @see app/Http/Controllers/ElderMedicalConditionController.php:43
* @route '/elders/{elder}/medical-conditions/{condition}'
*/
destroy.delete = (args: { elder: number | { id: number }, condition: number | { id: number } } | [elder: number | { id: number }, condition: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ElderMedicalConditionController::destroy
* @see app/Http/Controllers/ElderMedicalConditionController.php:43
* @route '/elders/{elder}/medical-conditions/{condition}'
*/
const destroyForm = (args: { elder: number | { id: number }, condition: number | { id: number } } | [elder: number | { id: number }, condition: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderMedicalConditionController::destroy
* @see app/Http/Controllers/ElderMedicalConditionController.php:43
* @route '/elders/{elder}/medical-conditions/{condition}'
*/
destroyForm.delete = (args: { elder: number | { id: number }, condition: number | { id: number } } | [elder: number | { id: number }, condition: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const medicalConditions = {
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default medicalConditions