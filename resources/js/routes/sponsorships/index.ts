import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\SponsorshipController::exportMethod
* @see app/Http/Controllers/SponsorshipController.php:187
* @route '/sponsorships/export'
*/
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/sponsorships/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SponsorshipController::exportMethod
* @see app/Http/Controllers/SponsorshipController.php:187
* @route '/sponsorships/export'
*/
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SponsorshipController::exportMethod
* @see app/Http/Controllers/SponsorshipController.php:187
* @route '/sponsorships/export'
*/
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SponsorshipController::exportMethod
* @see app/Http/Controllers/SponsorshipController.php:187
* @route '/sponsorships/export'
*/
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SponsorshipController::exportMethod
* @see app/Http/Controllers/SponsorshipController.php:187
* @route '/sponsorships/export'
*/
const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SponsorshipController::exportMethod
* @see app/Http/Controllers/SponsorshipController.php:187
* @route '/sponsorships/export'
*/
exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SponsorshipController::exportMethod
* @see app/Http/Controllers/SponsorshipController.php:187
* @route '/sponsorships/export'
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
* @see \App\Http\Controllers\SponsorshipController::index
* @see app/Http/Controllers/SponsorshipController.php:25
* @route '/sponsorships'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/sponsorships',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SponsorshipController::index
* @see app/Http/Controllers/SponsorshipController.php:25
* @route '/sponsorships'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SponsorshipController::index
* @see app/Http/Controllers/SponsorshipController.php:25
* @route '/sponsorships'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SponsorshipController::index
* @see app/Http/Controllers/SponsorshipController.php:25
* @route '/sponsorships'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SponsorshipController::index
* @see app/Http/Controllers/SponsorshipController.php:25
* @route '/sponsorships'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SponsorshipController::index
* @see app/Http/Controllers/SponsorshipController.php:25
* @route '/sponsorships'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SponsorshipController::index
* @see app/Http/Controllers/SponsorshipController.php:25
* @route '/sponsorships'
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
* @see \App\Http\Controllers\SponsorshipController::create
* @see app/Http/Controllers/SponsorshipController.php:54
* @route '/sponsorships/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/sponsorships/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SponsorshipController::create
* @see app/Http/Controllers/SponsorshipController.php:54
* @route '/sponsorships/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SponsorshipController::create
* @see app/Http/Controllers/SponsorshipController.php:54
* @route '/sponsorships/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SponsorshipController::create
* @see app/Http/Controllers/SponsorshipController.php:54
* @route '/sponsorships/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SponsorshipController::create
* @see app/Http/Controllers/SponsorshipController.php:54
* @route '/sponsorships/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SponsorshipController::create
* @see app/Http/Controllers/SponsorshipController.php:54
* @route '/sponsorships/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SponsorshipController::create
* @see app/Http/Controllers/SponsorshipController.php:54
* @route '/sponsorships/create'
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
* @see \App\Http\Controllers\SponsorshipController::store
* @see app/Http/Controllers/SponsorshipController.php:67
* @route '/sponsorships'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/sponsorships',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SponsorshipController::store
* @see app/Http/Controllers/SponsorshipController.php:67
* @route '/sponsorships'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SponsorshipController::store
* @see app/Http/Controllers/SponsorshipController.php:67
* @route '/sponsorships'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SponsorshipController::store
* @see app/Http/Controllers/SponsorshipController.php:67
* @route '/sponsorships'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SponsorshipController::store
* @see app/Http/Controllers/SponsorshipController.php:67
* @route '/sponsorships'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\SponsorshipController::show
* @see app/Http/Controllers/SponsorshipController.php:91
* @route '/sponsorships/{sponsorship}'
*/
export const show = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/sponsorships/{sponsorship}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SponsorshipController::show
* @see app/Http/Controllers/SponsorshipController.php:91
* @route '/sponsorships/{sponsorship}'
*/
show.url = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { sponsorship: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { sponsorship: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            sponsorship: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        sponsorship: typeof args.sponsorship === 'object'
        ? args.sponsorship.id
        : args.sponsorship,
    }

    return show.definition.url
            .replace('{sponsorship}', parsedArgs.sponsorship.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SponsorshipController::show
* @see app/Http/Controllers/SponsorshipController.php:91
* @route '/sponsorships/{sponsorship}'
*/
show.get = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SponsorshipController::show
* @see app/Http/Controllers/SponsorshipController.php:91
* @route '/sponsorships/{sponsorship}'
*/
show.head = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SponsorshipController::show
* @see app/Http/Controllers/SponsorshipController.php:91
* @route '/sponsorships/{sponsorship}'
*/
const showForm = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SponsorshipController::show
* @see app/Http/Controllers/SponsorshipController.php:91
* @route '/sponsorships/{sponsorship}'
*/
showForm.get = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SponsorshipController::show
* @see app/Http/Controllers/SponsorshipController.php:91
* @route '/sponsorships/{sponsorship}'
*/
showForm.head = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\SponsorshipController::edit
* @see app/Http/Controllers/SponsorshipController.php:132
* @route '/sponsorships/{sponsorship}/edit'
*/
export const edit = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/sponsorships/{sponsorship}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SponsorshipController::edit
* @see app/Http/Controllers/SponsorshipController.php:132
* @route '/sponsorships/{sponsorship}/edit'
*/
edit.url = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { sponsorship: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { sponsorship: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            sponsorship: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        sponsorship: typeof args.sponsorship === 'object'
        ? args.sponsorship.id
        : args.sponsorship,
    }

    return edit.definition.url
            .replace('{sponsorship}', parsedArgs.sponsorship.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SponsorshipController::edit
* @see app/Http/Controllers/SponsorshipController.php:132
* @route '/sponsorships/{sponsorship}/edit'
*/
edit.get = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SponsorshipController::edit
* @see app/Http/Controllers/SponsorshipController.php:132
* @route '/sponsorships/{sponsorship}/edit'
*/
edit.head = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SponsorshipController::edit
* @see app/Http/Controllers/SponsorshipController.php:132
* @route '/sponsorships/{sponsorship}/edit'
*/
const editForm = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SponsorshipController::edit
* @see app/Http/Controllers/SponsorshipController.php:132
* @route '/sponsorships/{sponsorship}/edit'
*/
editForm.get = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SponsorshipController::edit
* @see app/Http/Controllers/SponsorshipController.php:132
* @route '/sponsorships/{sponsorship}/edit'
*/
editForm.head = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\SponsorshipController::update
* @see app/Http/Controllers/SponsorshipController.php:162
* @route '/sponsorships/{sponsorship}'
*/
export const update = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/sponsorships/{sponsorship}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\SponsorshipController::update
* @see app/Http/Controllers/SponsorshipController.php:162
* @route '/sponsorships/{sponsorship}'
*/
update.url = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { sponsorship: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { sponsorship: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            sponsorship: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        sponsorship: typeof args.sponsorship === 'object'
        ? args.sponsorship.id
        : args.sponsorship,
    }

    return update.definition.url
            .replace('{sponsorship}', parsedArgs.sponsorship.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SponsorshipController::update
* @see app/Http/Controllers/SponsorshipController.php:162
* @route '/sponsorships/{sponsorship}'
*/
update.put = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\SponsorshipController::update
* @see app/Http/Controllers/SponsorshipController.php:162
* @route '/sponsorships/{sponsorship}'
*/
update.patch = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\SponsorshipController::update
* @see app/Http/Controllers/SponsorshipController.php:162
* @route '/sponsorships/{sponsorship}'
*/
const updateForm = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SponsorshipController::update
* @see app/Http/Controllers/SponsorshipController.php:162
* @route '/sponsorships/{sponsorship}'
*/
updateForm.put = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SponsorshipController::update
* @see app/Http/Controllers/SponsorshipController.php:162
* @route '/sponsorships/{sponsorship}'
*/
updateForm.patch = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\SponsorshipController::destroy
* @see app/Http/Controllers/SponsorshipController.php:181
* @route '/sponsorships/{sponsorship}'
*/
export const destroy = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/sponsorships/{sponsorship}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\SponsorshipController::destroy
* @see app/Http/Controllers/SponsorshipController.php:181
* @route '/sponsorships/{sponsorship}'
*/
destroy.url = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { sponsorship: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { sponsorship: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            sponsorship: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        sponsorship: typeof args.sponsorship === 'object'
        ? args.sponsorship.id
        : args.sponsorship,
    }

    return destroy.definition.url
            .replace('{sponsorship}', parsedArgs.sponsorship.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SponsorshipController::destroy
* @see app/Http/Controllers/SponsorshipController.php:181
* @route '/sponsorships/{sponsorship}'
*/
destroy.delete = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\SponsorshipController::destroy
* @see app/Http/Controllers/SponsorshipController.php:181
* @route '/sponsorships/{sponsorship}'
*/
const destroyForm = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SponsorshipController::destroy
* @see app/Http/Controllers/SponsorshipController.php:181
* @route '/sponsorships/{sponsorship}'
*/
destroyForm.delete = (args: { sponsorship: number | { id: number } } | [sponsorship: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const sponsorships = {
    export: Object.assign(exportMethod, exportMethod),
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default sponsorships