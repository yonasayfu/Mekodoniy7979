import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
import caseNotes from './case-notes'
import lifecycle from './lifecycle'
import healthAssessments from './health-assessments'
import medicalConditions from './medical-conditions'
import medications from './medications'
/**
* @see \App\Http\Controllers\ElderController::exportMethod
* @see app/Http/Controllers/ElderController.php:228
* @route '/elders/export'
*/
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/elders/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ElderController::exportMethod
* @see app/Http/Controllers/ElderController.php:228
* @route '/elders/export'
*/
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ElderController::exportMethod
* @see app/Http/Controllers/ElderController.php:228
* @route '/elders/export'
*/
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ElderController::exportMethod
* @see app/Http/Controllers/ElderController.php:228
* @route '/elders/export'
*/
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ElderController::exportMethod
* @see app/Http/Controllers/ElderController.php:228
* @route '/elders/export'
*/
const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ElderController::exportMethod
* @see app/Http/Controllers/ElderController.php:228
* @route '/elders/export'
*/
exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ElderController::exportMethod
* @see app/Http/Controllers/ElderController.php:228
* @route '/elders/export'
*/
exportMethodForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

exportMethod.form = exportMethodForm

/**
* @see \App\Http\Controllers\ElderController::index
* @see app/Http/Controllers/ElderController.php:22
* @route '/elders'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/elders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ElderController::index
* @see app/Http/Controllers/ElderController.php:22
* @route '/elders'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ElderController::index
* @see app/Http/Controllers/ElderController.php:22
* @route '/elders'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ElderController::index
* @see app/Http/Controllers/ElderController.php:22
* @route '/elders'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ElderController::index
* @see app/Http/Controllers/ElderController.php:22
* @route '/elders'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ElderController::index
* @see app/Http/Controllers/ElderController.php:22
* @route '/elders'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ElderController::index
* @see app/Http/Controllers/ElderController.php:22
* @route '/elders'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\ElderController::create
* @see app/Http/Controllers/ElderController.php:49
* @route '/elders/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/elders/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ElderController::create
* @see app/Http/Controllers/ElderController.php:49
* @route '/elders/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ElderController::create
* @see app/Http/Controllers/ElderController.php:49
* @route '/elders/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ElderController::create
* @see app/Http/Controllers/ElderController.php:49
* @route '/elders/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ElderController::create
* @see app/Http/Controllers/ElderController.php:49
* @route '/elders/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ElderController::create
* @see app/Http/Controllers/ElderController.php:49
* @route '/elders/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ElderController::create
* @see app/Http/Controllers/ElderController.php:49
* @route '/elders/create'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Http\Controllers\ElderController::store
* @see app/Http/Controllers/ElderController.php:60
* @route '/elders'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/elders',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ElderController::store
* @see app/Http/Controllers/ElderController.php:60
* @route '/elders'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ElderController::store
* @see app/Http/Controllers/ElderController.php:60
* @route '/elders'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderController::store
* @see app/Http/Controllers/ElderController.php:60
* @route '/elders'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderController::store
* @see app/Http/Controllers/ElderController.php:60
* @route '/elders'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\ElderController::show
* @see app/Http/Controllers/ElderController.php:79
* @route '/elders/{elder}'
*/
export const show = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/elders/{elder}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ElderController::show
* @see app/Http/Controllers/ElderController.php:79
* @route '/elders/{elder}'
*/
show.url = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ElderController::show
* @see app/Http/Controllers/ElderController.php:79
* @route '/elders/{elder}'
*/
show.get = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ElderController::show
* @see app/Http/Controllers/ElderController.php:79
* @route '/elders/{elder}'
*/
show.head = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ElderController::show
* @see app/Http/Controllers/ElderController.php:79
* @route '/elders/{elder}'
*/
const showForm = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ElderController::show
* @see app/Http/Controllers/ElderController.php:79
* @route '/elders/{elder}'
*/
showForm.get = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ElderController::show
* @see app/Http/Controllers/ElderController.php:79
* @route '/elders/{elder}'
*/
showForm.head = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\ElderController::edit
* @see app/Http/Controllers/ElderController.php:146
* @route '/elders/{elder}/edit'
*/
export const edit = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/elders/{elder}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ElderController::edit
* @see app/Http/Controllers/ElderController.php:146
* @route '/elders/{elder}/edit'
*/
edit.url = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return edit.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ElderController::edit
* @see app/Http/Controllers/ElderController.php:146
* @route '/elders/{elder}/edit'
*/
edit.get = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ElderController::edit
* @see app/Http/Controllers/ElderController.php:146
* @route '/elders/{elder}/edit'
*/
edit.head = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ElderController::edit
* @see app/Http/Controllers/ElderController.php:146
* @route '/elders/{elder}/edit'
*/
const editForm = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ElderController::edit
* @see app/Http/Controllers/ElderController.php:146
* @route '/elders/{elder}/edit'
*/
editForm.get = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ElderController::edit
* @see app/Http/Controllers/ElderController.php:146
* @route '/elders/{elder}/edit'
*/
editForm.head = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Http\Controllers\ElderController::update
* @see app/Http/Controllers/ElderController.php:173
* @route '/elders/{elder}'
*/
export const update = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/elders/{elder}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\ElderController::update
* @see app/Http/Controllers/ElderController.php:173
* @route '/elders/{elder}'
*/
update.url = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ElderController::update
* @see app/Http/Controllers/ElderController.php:173
* @route '/elders/{elder}'
*/
update.put = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\ElderController::update
* @see app/Http/Controllers/ElderController.php:173
* @route '/elders/{elder}'
*/
update.patch = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\ElderController::update
* @see app/Http/Controllers/ElderController.php:173
* @route '/elders/{elder}'
*/
const updateForm = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderController::update
* @see app/Http/Controllers/ElderController.php:173
* @route '/elders/{elder}'
*/
updateForm.put = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderController::update
* @see app/Http/Controllers/ElderController.php:173
* @route '/elders/{elder}'
*/
updateForm.patch = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\ElderController::destroy
* @see app/Http/Controllers/ElderController.php:216
* @route '/elders/{elder}'
*/
export const destroy = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/elders/{elder}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ElderController::destroy
* @see app/Http/Controllers/ElderController.php:216
* @route '/elders/{elder}'
*/
destroy.url = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ElderController::destroy
* @see app/Http/Controllers/ElderController.php:216
* @route '/elders/{elder}'
*/
destroy.delete = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ElderController::destroy
* @see app/Http/Controllers/ElderController.php:216
* @route '/elders/{elder}'
*/
const destroyForm = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderController::destroy
* @see app/Http/Controllers/ElderController.php:216
* @route '/elders/{elder}'
*/
destroyForm.delete = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const elders = {
    caseNotes: Object.assign(caseNotes, caseNotes),
    export: Object.assign(exportMethod, exportMethod),
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    lifecycle: Object.assign(lifecycle, lifecycle),
    healthAssessments: Object.assign(healthAssessments, healthAssessments),
    medicalConditions: Object.assign(medicalConditions, medicalConditions),
    medications: Object.assign(medications, medications),
}

export default elders