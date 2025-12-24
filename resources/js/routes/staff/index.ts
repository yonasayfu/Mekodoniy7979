import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\StaffController::index
* @see app/Http/Controllers/StaffController.php:23
* @route '/staff'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/staff',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StaffController::index
* @see app/Http/Controllers/StaffController.php:23
* @route '/staff'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StaffController::index
* @see app/Http/Controllers/StaffController.php:23
* @route '/staff'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StaffController::index
* @see app/Http/Controllers/StaffController.php:23
* @route '/staff'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StaffController::index
* @see app/Http/Controllers/StaffController.php:23
* @route '/staff'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StaffController::index
* @see app/Http/Controllers/StaffController.php:23
* @route '/staff'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StaffController::index
* @see app/Http/Controllers/StaffController.php:23
* @route '/staff'
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
* @see \App\Http\Controllers\StaffController::exportMethod
* @see app/Http/Controllers/StaffController.php:104
* @route '/staff/export'
*/
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/staff/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StaffController::exportMethod
* @see app/Http/Controllers/StaffController.php:104
* @route '/staff/export'
*/
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StaffController::exportMethod
* @see app/Http/Controllers/StaffController.php:104
* @route '/staff/export'
*/
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StaffController::exportMethod
* @see app/Http/Controllers/StaffController.php:104
* @route '/staff/export'
*/
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StaffController::exportMethod
* @see app/Http/Controllers/StaffController.php:104
* @route '/staff/export'
*/
const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StaffController::exportMethod
* @see app/Http/Controllers/StaffController.php:104
* @route '/staff/export'
*/
exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StaffController::exportMethod
* @see app/Http/Controllers/StaffController.php:104
* @route '/staff/export'
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
* @see \App\Http\Controllers\StaffController::create
* @see app/Http/Controllers/StaffController.php:112
* @route '/staff/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/staff/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StaffController::create
* @see app/Http/Controllers/StaffController.php:112
* @route '/staff/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StaffController::create
* @see app/Http/Controllers/StaffController.php:112
* @route '/staff/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StaffController::create
* @see app/Http/Controllers/StaffController.php:112
* @route '/staff/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StaffController::create
* @see app/Http/Controllers/StaffController.php:112
* @route '/staff/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StaffController::create
* @see app/Http/Controllers/StaffController.php:112
* @route '/staff/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StaffController::create
* @see app/Http/Controllers/StaffController.php:112
* @route '/staff/create'
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
* @see \App\Http\Controllers\StaffController::store
* @see app/Http/Controllers/StaffController.php:164
* @route '/staff'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/staff',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StaffController::store
* @see app/Http/Controllers/StaffController.php:164
* @route '/staff'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StaffController::store
* @see app/Http/Controllers/StaffController.php:164
* @route '/staff'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StaffController::store
* @see app/Http/Controllers/StaffController.php:164
* @route '/staff'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StaffController::store
* @see app/Http/Controllers/StaffController.php:164
* @route '/staff'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\StaffController::show
* @see app/Http/Controllers/StaffController.php:124
* @route '/staff/{staff}'
*/
export const show = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/staff/{staff}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StaffController::show
* @see app/Http/Controllers/StaffController.php:124
* @route '/staff/{staff}'
*/
show.url = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { staff: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { staff: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            staff: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        staff: typeof args.staff === 'object'
        ? args.staff.id
        : args.staff,
    }

    return show.definition.url
            .replace('{staff}', parsedArgs.staff.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StaffController::show
* @see app/Http/Controllers/StaffController.php:124
* @route '/staff/{staff}'
*/
show.get = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StaffController::show
* @see app/Http/Controllers/StaffController.php:124
* @route '/staff/{staff}'
*/
show.head = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StaffController::show
* @see app/Http/Controllers/StaffController.php:124
* @route '/staff/{staff}'
*/
const showForm = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StaffController::show
* @see app/Http/Controllers/StaffController.php:124
* @route '/staff/{staff}'
*/
showForm.get = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StaffController::show
* @see app/Http/Controllers/StaffController.php:124
* @route '/staff/{staff}'
*/
showForm.head = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\StaffController::edit
* @see app/Http/Controllers/StaffController.php:185
* @route '/staff/{staff}/edit'
*/
export const edit = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/staff/{staff}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StaffController::edit
* @see app/Http/Controllers/StaffController.php:185
* @route '/staff/{staff}/edit'
*/
edit.url = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { staff: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { staff: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            staff: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        staff: typeof args.staff === 'object'
        ? args.staff.id
        : args.staff,
    }

    return edit.definition.url
            .replace('{staff}', parsedArgs.staff.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StaffController::edit
* @see app/Http/Controllers/StaffController.php:185
* @route '/staff/{staff}/edit'
*/
edit.get = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StaffController::edit
* @see app/Http/Controllers/StaffController.php:185
* @route '/staff/{staff}/edit'
*/
edit.head = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StaffController::edit
* @see app/Http/Controllers/StaffController.php:185
* @route '/staff/{staff}/edit'
*/
const editForm = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StaffController::edit
* @see app/Http/Controllers/StaffController.php:185
* @route '/staff/{staff}/edit'
*/
editForm.get = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StaffController::edit
* @see app/Http/Controllers/StaffController.php:185
* @route '/staff/{staff}/edit'
*/
editForm.head = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\StaffController::update
* @see app/Http/Controllers/StaffController.php:219
* @route '/staff/{staff}'
*/
export const update = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/staff/{staff}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\StaffController::update
* @see app/Http/Controllers/StaffController.php:219
* @route '/staff/{staff}'
*/
update.url = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { staff: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { staff: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            staff: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        staff: typeof args.staff === 'object'
        ? args.staff.id
        : args.staff,
    }

    return update.definition.url
            .replace('{staff}', parsedArgs.staff.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StaffController::update
* @see app/Http/Controllers/StaffController.php:219
* @route '/staff/{staff}'
*/
update.put = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\StaffController::update
* @see app/Http/Controllers/StaffController.php:219
* @route '/staff/{staff}'
*/
const updateForm = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StaffController::update
* @see app/Http/Controllers/StaffController.php:219
* @route '/staff/{staff}'
*/
updateForm.put = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\StaffController::destroy
* @see app/Http/Controllers/StaffController.php:250
* @route '/staff/{staff}'
*/
export const destroy = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/staff/{staff}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\StaffController::destroy
* @see app/Http/Controllers/StaffController.php:250
* @route '/staff/{staff}'
*/
destroy.url = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { staff: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { staff: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            staff: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        staff: typeof args.staff === 'object'
        ? args.staff.id
        : args.staff,
    }

    return destroy.definition.url
            .replace('{staff}', parsedArgs.staff.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StaffController::destroy
* @see app/Http/Controllers/StaffController.php:250
* @route '/staff/{staff}'
*/
destroy.delete = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\StaffController::destroy
* @see app/Http/Controllers/StaffController.php:250
* @route '/staff/{staff}'
*/
const destroyForm = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StaffController::destroy
* @see app/Http/Controllers/StaffController.php:250
* @route '/staff/{staff}'
*/
destroyForm.delete = (args: { staff: number | { id: number } } | [staff: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const staff = {
    index: Object.assign(index, index),
    export: Object.assign(exportMethod, exportMethod),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default staff