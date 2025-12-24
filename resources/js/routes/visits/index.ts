import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\VisitController::exportMethod
* @see app/Http/Controllers/VisitController.php:170
* @route '/visits/export'
*/
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/visits/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\VisitController::exportMethod
* @see app/Http/Controllers/VisitController.php:170
* @route '/visits/export'
*/
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\VisitController::exportMethod
* @see app/Http/Controllers/VisitController.php:170
* @route '/visits/export'
*/
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VisitController::exportMethod
* @see app/Http/Controllers/VisitController.php:170
* @route '/visits/export'
*/
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\VisitController::exportMethod
* @see app/Http/Controllers/VisitController.php:170
* @route '/visits/export'
*/
const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VisitController::exportMethod
* @see app/Http/Controllers/VisitController.php:170
* @route '/visits/export'
*/
exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VisitController::exportMethod
* @see app/Http/Controllers/VisitController.php:170
* @route '/visits/export'
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
* @see \App\Http\Controllers\VisitController::index
* @see app/Http/Controllers/VisitController.php:26
* @route '/visits'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/visits',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\VisitController::index
* @see app/Http/Controllers/VisitController.php:26
* @route '/visits'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\VisitController::index
* @see app/Http/Controllers/VisitController.php:26
* @route '/visits'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VisitController::index
* @see app/Http/Controllers/VisitController.php:26
* @route '/visits'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\VisitController::index
* @see app/Http/Controllers/VisitController.php:26
* @route '/visits'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VisitController::index
* @see app/Http/Controllers/VisitController.php:26
* @route '/visits'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VisitController::index
* @see app/Http/Controllers/VisitController.php:26
* @route '/visits'
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
* @see \App\Http\Controllers\VisitController::create
* @see app/Http/Controllers/VisitController.php:55
* @route '/visits/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/visits/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\VisitController::create
* @see app/Http/Controllers/VisitController.php:55
* @route '/visits/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\VisitController::create
* @see app/Http/Controllers/VisitController.php:55
* @route '/visits/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VisitController::create
* @see app/Http/Controllers/VisitController.php:55
* @route '/visits/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\VisitController::create
* @see app/Http/Controllers/VisitController.php:55
* @route '/visits/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VisitController::create
* @see app/Http/Controllers/VisitController.php:55
* @route '/visits/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VisitController::create
* @see app/Http/Controllers/VisitController.php:55
* @route '/visits/create'
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
* @see \App\Http\Controllers\VisitController::store
* @see app/Http/Controllers/VisitController.php:70
* @route '/visits'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/visits',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\VisitController::store
* @see app/Http/Controllers/VisitController.php:70
* @route '/visits'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\VisitController::store
* @see app/Http/Controllers/VisitController.php:70
* @route '/visits'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VisitController::store
* @see app/Http/Controllers/VisitController.php:70
* @route '/visits'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VisitController::store
* @see app/Http/Controllers/VisitController.php:70
* @route '/visits'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\VisitController::show
* @see app/Http/Controllers/VisitController.php:90
* @route '/visits/{visit}'
*/
export const show = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/visits/{visit}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\VisitController::show
* @see app/Http/Controllers/VisitController.php:90
* @route '/visits/{visit}'
*/
show.url = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { visit: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { visit: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            visit: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        visit: typeof args.visit === 'object'
        ? args.visit.id
        : args.visit,
    }

    return show.definition.url
            .replace('{visit}', parsedArgs.visit.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\VisitController::show
* @see app/Http/Controllers/VisitController.php:90
* @route '/visits/{visit}'
*/
show.get = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VisitController::show
* @see app/Http/Controllers/VisitController.php:90
* @route '/visits/{visit}'
*/
show.head = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\VisitController::show
* @see app/Http/Controllers/VisitController.php:90
* @route '/visits/{visit}'
*/
const showForm = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VisitController::show
* @see app/Http/Controllers/VisitController.php:90
* @route '/visits/{visit}'
*/
showForm.get = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VisitController::show
* @see app/Http/Controllers/VisitController.php:90
* @route '/visits/{visit}'
*/
showForm.head = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\VisitController::edit
* @see app/Http/Controllers/VisitController.php:112
* @route '/visits/{visit}/edit'
*/
export const edit = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/visits/{visit}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\VisitController::edit
* @see app/Http/Controllers/VisitController.php:112
* @route '/visits/{visit}/edit'
*/
edit.url = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { visit: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { visit: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            visit: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        visit: typeof args.visit === 'object'
        ? args.visit.id
        : args.visit,
    }

    return edit.definition.url
            .replace('{visit}', parsedArgs.visit.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\VisitController::edit
* @see app/Http/Controllers/VisitController.php:112
* @route '/visits/{visit}/edit'
*/
edit.get = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VisitController::edit
* @see app/Http/Controllers/VisitController.php:112
* @route '/visits/{visit}/edit'
*/
edit.head = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\VisitController::edit
* @see app/Http/Controllers/VisitController.php:112
* @route '/visits/{visit}/edit'
*/
const editForm = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VisitController::edit
* @see app/Http/Controllers/VisitController.php:112
* @route '/visits/{visit}/edit'
*/
editForm.get = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VisitController::edit
* @see app/Http/Controllers/VisitController.php:112
* @route '/visits/{visit}/edit'
*/
editForm.head = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\VisitController::update
* @see app/Http/Controllers/VisitController.php:144
* @route '/visits/{visit}'
*/
export const update = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/visits/{visit}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\VisitController::update
* @see app/Http/Controllers/VisitController.php:144
* @route '/visits/{visit}'
*/
update.url = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { visit: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { visit: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            visit: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        visit: typeof args.visit === 'object'
        ? args.visit.id
        : args.visit,
    }

    return update.definition.url
            .replace('{visit}', parsedArgs.visit.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\VisitController::update
* @see app/Http/Controllers/VisitController.php:144
* @route '/visits/{visit}'
*/
update.put = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\VisitController::update
* @see app/Http/Controllers/VisitController.php:144
* @route '/visits/{visit}'
*/
update.patch = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\VisitController::update
* @see app/Http/Controllers/VisitController.php:144
* @route '/visits/{visit}'
*/
const updateForm = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VisitController::update
* @see app/Http/Controllers/VisitController.php:144
* @route '/visits/{visit}'
*/
updateForm.put = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VisitController::update
* @see app/Http/Controllers/VisitController.php:144
* @route '/visits/{visit}'
*/
updateForm.patch = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\VisitController::destroy
* @see app/Http/Controllers/VisitController.php:164
* @route '/visits/{visit}'
*/
export const destroy = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/visits/{visit}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\VisitController::destroy
* @see app/Http/Controllers/VisitController.php:164
* @route '/visits/{visit}'
*/
destroy.url = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { visit: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { visit: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            visit: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        visit: typeof args.visit === 'object'
        ? args.visit.id
        : args.visit,
    }

    return destroy.definition.url
            .replace('{visit}', parsedArgs.visit.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\VisitController::destroy
* @see app/Http/Controllers/VisitController.php:164
* @route '/visits/{visit}'
*/
destroy.delete = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\VisitController::destroy
* @see app/Http/Controllers/VisitController.php:164
* @route '/visits/{visit}'
*/
const destroyForm = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VisitController::destroy
* @see app/Http/Controllers/VisitController.php:164
* @route '/visits/{visit}'
*/
destroyForm.delete = (args: { visit: number | { id: number } } | [visit: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const visits = {
    export: Object.assign(exportMethod, exportMethod),
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default visits