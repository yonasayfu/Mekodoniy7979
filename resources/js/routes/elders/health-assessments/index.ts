import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\ElderHealthAssessmentController::store
* @see app/Http/Controllers/ElderHealthAssessmentController.php:11
* @route '/elders/{elder}/health-assessments'
*/
export const store = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/elders/{elder}/health-assessments',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ElderHealthAssessmentController::store
* @see app/Http/Controllers/ElderHealthAssessmentController.php:11
* @route '/elders/{elder}/health-assessments'
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
* @see \App\Http\Controllers\ElderHealthAssessmentController::store
* @see app/Http/Controllers/ElderHealthAssessmentController.php:11
* @route '/elders/{elder}/health-assessments'
*/
store.post = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderHealthAssessmentController::store
* @see app/Http/Controllers/ElderHealthAssessmentController.php:11
* @route '/elders/{elder}/health-assessments'
*/
const storeForm = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderHealthAssessmentController::store
* @see app/Http/Controllers/ElderHealthAssessmentController.php:11
* @route '/elders/{elder}/health-assessments'
*/
storeForm.post = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\ElderHealthAssessmentController::update
* @see app/Http/Controllers/ElderHealthAssessmentController.php:28
* @route '/elders/{elder}/health-assessments/{assessment}'
*/
export const update = (args: { elder: number | { id: number }, assessment: number | { id: number } } | [elder: number | { id: number }, assessment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/elders/{elder}/health-assessments/{assessment}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\ElderHealthAssessmentController::update
* @see app/Http/Controllers/ElderHealthAssessmentController.php:28
* @route '/elders/{elder}/health-assessments/{assessment}'
*/
update.url = (args: { elder: number | { id: number }, assessment: number | { id: number } } | [elder: number | { id: number }, assessment: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            elder: args[0],
            assessment: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        elder: typeof args.elder === 'object'
        ? args.elder.id
        : args.elder,
        assessment: typeof args.assessment === 'object'
        ? args.assessment.id
        : args.assessment,
    }

    return update.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace('{assessment}', parsedArgs.assessment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ElderHealthAssessmentController::update
* @see app/Http/Controllers/ElderHealthAssessmentController.php:28
* @route '/elders/{elder}/health-assessments/{assessment}'
*/
update.put = (args: { elder: number | { id: number }, assessment: number | { id: number } } | [elder: number | { id: number }, assessment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\ElderHealthAssessmentController::update
* @see app/Http/Controllers/ElderHealthAssessmentController.php:28
* @route '/elders/{elder}/health-assessments/{assessment}'
*/
const updateForm = (args: { elder: number | { id: number }, assessment: number | { id: number } } | [elder: number | { id: number }, assessment: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderHealthAssessmentController::update
* @see app/Http/Controllers/ElderHealthAssessmentController.php:28
* @route '/elders/{elder}/health-assessments/{assessment}'
*/
updateForm.put = (args: { elder: number | { id: number }, assessment: number | { id: number } } | [elder: number | { id: number }, assessment: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\ElderHealthAssessmentController::destroy
* @see app/Http/Controllers/ElderHealthAssessmentController.php:46
* @route '/elders/{elder}/health-assessments/{assessment}'
*/
export const destroy = (args: { elder: number | { id: number }, assessment: number | { id: number } } | [elder: number | { id: number }, assessment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/elders/{elder}/health-assessments/{assessment}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ElderHealthAssessmentController::destroy
* @see app/Http/Controllers/ElderHealthAssessmentController.php:46
* @route '/elders/{elder}/health-assessments/{assessment}'
*/
destroy.url = (args: { elder: number | { id: number }, assessment: number | { id: number } } | [elder: number | { id: number }, assessment: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            elder: args[0],
            assessment: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        elder: typeof args.elder === 'object'
        ? args.elder.id
        : args.elder,
        assessment: typeof args.assessment === 'object'
        ? args.assessment.id
        : args.assessment,
    }

    return destroy.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace('{assessment}', parsedArgs.assessment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ElderHealthAssessmentController::destroy
* @see app/Http/Controllers/ElderHealthAssessmentController.php:46
* @route '/elders/{elder}/health-assessments/{assessment}'
*/
destroy.delete = (args: { elder: number | { id: number }, assessment: number | { id: number } } | [elder: number | { id: number }, assessment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ElderHealthAssessmentController::destroy
* @see app/Http/Controllers/ElderHealthAssessmentController.php:46
* @route '/elders/{elder}/health-assessments/{assessment}'
*/
const destroyForm = (args: { elder: number | { id: number }, assessment: number | { id: number } } | [elder: number | { id: number }, assessment: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderHealthAssessmentController::destroy
* @see app/Http/Controllers/ElderHealthAssessmentController.php:46
* @route '/elders/{elder}/health-assessments/{assessment}'
*/
destroyForm.delete = (args: { elder: number | { id: number }, assessment: number | { id: number } } | [elder: number | { id: number }, assessment: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const healthAssessments = {
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default healthAssessments