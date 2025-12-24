import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\ElderMedicationController::store
* @see app/Http/Controllers/ElderMedicationController.php:11
* @route '/elders/{elder}/medications'
*/
export const store = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/elders/{elder}/medications',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ElderMedicationController::store
* @see app/Http/Controllers/ElderMedicationController.php:11
* @route '/elders/{elder}/medications'
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
* @see \App\Http\Controllers\ElderMedicationController::store
* @see app/Http/Controllers/ElderMedicationController.php:11
* @route '/elders/{elder}/medications'
*/
store.post = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderMedicationController::store
* @see app/Http/Controllers/ElderMedicationController.php:11
* @route '/elders/{elder}/medications'
*/
const storeForm = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderMedicationController::store
* @see app/Http/Controllers/ElderMedicationController.php:11
* @route '/elders/{elder}/medications'
*/
storeForm.post = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\ElderMedicationController::update
* @see app/Http/Controllers/ElderMedicationController.php:27
* @route '/elders/{elder}/medications/{medication}'
*/
export const update = (args: { elder: number | { id: number }, medication: number | { id: number } } | [elder: number | { id: number }, medication: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/elders/{elder}/medications/{medication}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\ElderMedicationController::update
* @see app/Http/Controllers/ElderMedicationController.php:27
* @route '/elders/{elder}/medications/{medication}'
*/
update.url = (args: { elder: number | { id: number }, medication: number | { id: number } } | [elder: number | { id: number }, medication: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            elder: args[0],
            medication: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        elder: typeof args.elder === 'object'
        ? args.elder.id
        : args.elder,
        medication: typeof args.medication === 'object'
        ? args.medication.id
        : args.medication,
    }

    return update.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace('{medication}', parsedArgs.medication.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ElderMedicationController::update
* @see app/Http/Controllers/ElderMedicationController.php:27
* @route '/elders/{elder}/medications/{medication}'
*/
update.put = (args: { elder: number | { id: number }, medication: number | { id: number } } | [elder: number | { id: number }, medication: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\ElderMedicationController::update
* @see app/Http/Controllers/ElderMedicationController.php:27
* @route '/elders/{elder}/medications/{medication}'
*/
const updateForm = (args: { elder: number | { id: number }, medication: number | { id: number } } | [elder: number | { id: number }, medication: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderMedicationController::update
* @see app/Http/Controllers/ElderMedicationController.php:27
* @route '/elders/{elder}/medications/{medication}'
*/
updateForm.put = (args: { elder: number | { id: number }, medication: number | { id: number } } | [elder: number | { id: number }, medication: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\ElderMedicationController::destroy
* @see app/Http/Controllers/ElderMedicationController.php:47
* @route '/elders/{elder}/medications/{medication}'
*/
export const destroy = (args: { elder: number | { id: number }, medication: number | { id: number } } | [elder: number | { id: number }, medication: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/elders/{elder}/medications/{medication}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ElderMedicationController::destroy
* @see app/Http/Controllers/ElderMedicationController.php:47
* @route '/elders/{elder}/medications/{medication}'
*/
destroy.url = (args: { elder: number | { id: number }, medication: number | { id: number } } | [elder: number | { id: number }, medication: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            elder: args[0],
            medication: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        elder: typeof args.elder === 'object'
        ? args.elder.id
        : args.elder,
        medication: typeof args.medication === 'object'
        ? args.medication.id
        : args.medication,
    }

    return destroy.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace('{medication}', parsedArgs.medication.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ElderMedicationController::destroy
* @see app/Http/Controllers/ElderMedicationController.php:47
* @route '/elders/{elder}/medications/{medication}'
*/
destroy.delete = (args: { elder: number | { id: number }, medication: number | { id: number } } | [elder: number | { id: number }, medication: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ElderMedicationController::destroy
* @see app/Http/Controllers/ElderMedicationController.php:47
* @route '/elders/{elder}/medications/{medication}'
*/
const destroyForm = (args: { elder: number | { id: number }, medication: number | { id: number } } | [elder: number | { id: number }, medication: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderMedicationController::destroy
* @see app/Http/Controllers/ElderMedicationController.php:47
* @route '/elders/{elder}/medications/{medication}'
*/
destroyForm.delete = (args: { elder: number | { id: number }, medication: number | { id: number } } | [elder: number | { id: number }, medication: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const medications = {
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default medications