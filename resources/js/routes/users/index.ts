import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
import warnings from './warnings'
/**
* @see \App\Http\Controllers\UserManagementController::exportMethod
* @see app/Http/Controllers/UserManagementController.php:125
* @route '/users/export'
*/
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/users/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserManagementController::exportMethod
* @see app/Http/Controllers/UserManagementController.php:125
* @route '/users/export'
*/
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserManagementController::exportMethod
* @see app/Http/Controllers/UserManagementController.php:125
* @route '/users/export'
*/
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserManagementController::exportMethod
* @see app/Http/Controllers/UserManagementController.php:125
* @route '/users/export'
*/
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserManagementController::exportMethod
* @see app/Http/Controllers/UserManagementController.php:125
* @route '/users/export'
*/
const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserManagementController::exportMethod
* @see app/Http/Controllers/UserManagementController.php:125
* @route '/users/export'
*/
exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserManagementController::exportMethod
* @see app/Http/Controllers/UserManagementController.php:125
* @route '/users/export'
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
* @see \App\Http\Controllers\UserManagementController::index
* @see app/Http/Controllers/UserManagementController.php:30
* @route '/users'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/users',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserManagementController::index
* @see app/Http/Controllers/UserManagementController.php:30
* @route '/users'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserManagementController::index
* @see app/Http/Controllers/UserManagementController.php:30
* @route '/users'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserManagementController::index
* @see app/Http/Controllers/UserManagementController.php:30
* @route '/users'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserManagementController::index
* @see app/Http/Controllers/UserManagementController.php:30
* @route '/users'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserManagementController::index
* @see app/Http/Controllers/UserManagementController.php:30
* @route '/users'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserManagementController::index
* @see app/Http/Controllers/UserManagementController.php:30
* @route '/users'
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
* @see \App\Http\Controllers\UserManagementController::create
* @see app/Http/Controllers/UserManagementController.php:135
* @route '/users/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/users/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserManagementController::create
* @see app/Http/Controllers/UserManagementController.php:135
* @route '/users/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserManagementController::create
* @see app/Http/Controllers/UserManagementController.php:135
* @route '/users/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserManagementController::create
* @see app/Http/Controllers/UserManagementController.php:135
* @route '/users/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserManagementController::create
* @see app/Http/Controllers/UserManagementController.php:135
* @route '/users/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserManagementController::create
* @see app/Http/Controllers/UserManagementController.php:135
* @route '/users/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserManagementController::create
* @see app/Http/Controllers/UserManagementController.php:135
* @route '/users/create'
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
* @see \App\Http\Controllers\UserManagementController::store
* @see app/Http/Controllers/UserManagementController.php:211
* @route '/users'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/users',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserManagementController::store
* @see app/Http/Controllers/UserManagementController.php:211
* @route '/users'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserManagementController::store
* @see app/Http/Controllers/UserManagementController.php:211
* @route '/users'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::store
* @see app/Http/Controllers/UserManagementController.php:211
* @route '/users'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::store
* @see app/Http/Controllers/UserManagementController.php:211
* @route '/users'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\UserManagementController::show
* @see app/Http/Controllers/UserManagementController.php:153
* @route '/users/{user}'
*/
export const show = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/users/{user}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserManagementController::show
* @see app/Http/Controllers/UserManagementController.php:153
* @route '/users/{user}'
*/
show.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { user: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: typeof args.user === 'object'
        ? args.user.id
        : args.user,
    }

    return show.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserManagementController::show
* @see app/Http/Controllers/UserManagementController.php:153
* @route '/users/{user}'
*/
show.get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserManagementController::show
* @see app/Http/Controllers/UserManagementController.php:153
* @route '/users/{user}'
*/
show.head = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserManagementController::show
* @see app/Http/Controllers/UserManagementController.php:153
* @route '/users/{user}'
*/
const showForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserManagementController::show
* @see app/Http/Controllers/UserManagementController.php:153
* @route '/users/{user}'
*/
showForm.get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserManagementController::show
* @see app/Http/Controllers/UserManagementController.php:153
* @route '/users/{user}'
*/
showForm.head = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\UserManagementController::edit
* @see app/Http/Controllers/UserManagementController.php:245
* @route '/users/{user}/edit'
*/
export const edit = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/users/{user}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserManagementController::edit
* @see app/Http/Controllers/UserManagementController.php:245
* @route '/users/{user}/edit'
*/
edit.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { user: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: typeof args.user === 'object'
        ? args.user.id
        : args.user,
    }

    return edit.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserManagementController::edit
* @see app/Http/Controllers/UserManagementController.php:245
* @route '/users/{user}/edit'
*/
edit.get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserManagementController::edit
* @see app/Http/Controllers/UserManagementController.php:245
* @route '/users/{user}/edit'
*/
edit.head = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserManagementController::edit
* @see app/Http/Controllers/UserManagementController.php:245
* @route '/users/{user}/edit'
*/
const editForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserManagementController::edit
* @see app/Http/Controllers/UserManagementController.php:245
* @route '/users/{user}/edit'
*/
editForm.get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserManagementController::edit
* @see app/Http/Controllers/UserManagementController.php:245
* @route '/users/{user}/edit'
*/
editForm.head = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\UserManagementController::update
* @see app/Http/Controllers/UserManagementController.php:297
* @route '/users/{user}'
*/
export const update = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/users/{user}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\UserManagementController::update
* @see app/Http/Controllers/UserManagementController.php:297
* @route '/users/{user}'
*/
update.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { user: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: typeof args.user === 'object'
        ? args.user.id
        : args.user,
    }

    return update.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserManagementController::update
* @see app/Http/Controllers/UserManagementController.php:297
* @route '/users/{user}'
*/
update.put = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\UserManagementController::update
* @see app/Http/Controllers/UserManagementController.php:297
* @route '/users/{user}'
*/
const updateForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::update
* @see app/Http/Controllers/UserManagementController.php:297
* @route '/users/{user}'
*/
updateForm.put = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\UserManagementController::destroy
* @see app/Http/Controllers/UserManagementController.php:407
* @route '/users/{user}'
*/
export const destroy = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/users/{user}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\UserManagementController::destroy
* @see app/Http/Controllers/UserManagementController.php:407
* @route '/users/{user}'
*/
destroy.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { user: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: typeof args.user === 'object'
        ? args.user.id
        : args.user,
    }

    return destroy.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserManagementController::destroy
* @see app/Http/Controllers/UserManagementController.php:407
* @route '/users/{user}'
*/
destroy.delete = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\UserManagementController::destroy
* @see app/Http/Controllers/UserManagementController.php:407
* @route '/users/{user}'
*/
const destroyForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::destroy
* @see app/Http/Controllers/UserManagementController.php:407
* @route '/users/{user}'
*/
destroyForm.delete = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see \App\Http\Controllers\UserManagementController::kick
* @see app/Http/Controllers/UserManagementController.php:431
* @route '/users/{user}/kick'
*/
export const kick = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: kick.url(args, options),
    method: 'post',
})

kick.definition = {
    methods: ["post"],
    url: '/users/{user}/kick',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserManagementController::kick
* @see app/Http/Controllers/UserManagementController.php:431
* @route '/users/{user}/kick'
*/
kick.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { user: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: typeof args.user === 'object'
        ? args.user.id
        : args.user,
    }

    return kick.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserManagementController::kick
* @see app/Http/Controllers/UserManagementController.php:431
* @route '/users/{user}/kick'
*/
kick.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: kick.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::kick
* @see app/Http/Controllers/UserManagementController.php:431
* @route '/users/{user}/kick'
*/
const kickForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: kick.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::kick
* @see app/Http/Controllers/UserManagementController.php:431
* @route '/users/{user}/kick'
*/
kickForm.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: kick.url(args, options),
    method: 'post',
})

kick.form = kickForm

/**
* @see \App\Http\Controllers\UserManagementController::unkick
* @see app/Http/Controllers/UserManagementController.php:450
* @route '/users/{user}/unkick'
*/
export const unkick = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: unkick.url(args, options),
    method: 'post',
})

unkick.definition = {
    methods: ["post"],
    url: '/users/{user}/unkick',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserManagementController::unkick
* @see app/Http/Controllers/UserManagementController.php:450
* @route '/users/{user}/unkick'
*/
unkick.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { user: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: typeof args.user === 'object'
        ? args.user.id
        : args.user,
    }

    return unkick.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserManagementController::unkick
* @see app/Http/Controllers/UserManagementController.php:450
* @route '/users/{user}/unkick'
*/
unkick.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: unkick.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::unkick
* @see app/Http/Controllers/UserManagementController.php:450
* @route '/users/{user}/unkick'
*/
const unkickForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: unkick.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::unkick
* @see app/Http/Controllers/UserManagementController.php:450
* @route '/users/{user}/unkick'
*/
unkickForm.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: unkick.url(args, options),
    method: 'post',
})

unkick.form = unkickForm

/**
* @see \App\Http\Controllers\UserManagementController::ban
* @see app/Http/Controllers/UserManagementController.php:464
* @route '/users/{user}/ban'
*/
export const ban = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ban.url(args, options),
    method: 'post',
})

ban.definition = {
    methods: ["post"],
    url: '/users/{user}/ban',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserManagementController::ban
* @see app/Http/Controllers/UserManagementController.php:464
* @route '/users/{user}/ban'
*/
ban.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { user: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: typeof args.user === 'object'
        ? args.user.id
        : args.user,
    }

    return ban.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserManagementController::ban
* @see app/Http/Controllers/UserManagementController.php:464
* @route '/users/{user}/ban'
*/
ban.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ban.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::ban
* @see app/Http/Controllers/UserManagementController.php:464
* @route '/users/{user}/ban'
*/
const banForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: ban.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::ban
* @see app/Http/Controllers/UserManagementController.php:464
* @route '/users/{user}/ban'
*/
banForm.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: ban.url(args, options),
    method: 'post',
})

ban.form = banForm

/**
* @see \App\Http\Controllers\UserManagementController::unban
* @see app/Http/Controllers/UserManagementController.php:483
* @route '/users/{user}/unban'
*/
export const unban = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: unban.url(args, options),
    method: 'post',
})

unban.definition = {
    methods: ["post"],
    url: '/users/{user}/unban',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserManagementController::unban
* @see app/Http/Controllers/UserManagementController.php:483
* @route '/users/{user}/unban'
*/
unban.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { user: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: typeof args.user === 'object'
        ? args.user.id
        : args.user,
    }

    return unban.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserManagementController::unban
* @see app/Http/Controllers/UserManagementController.php:483
* @route '/users/{user}/unban'
*/
unban.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: unban.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::unban
* @see app/Http/Controllers/UserManagementController.php:483
* @route '/users/{user}/unban'
*/
const unbanForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: unban.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::unban
* @see app/Http/Controllers/UserManagementController.php:483
* @route '/users/{user}/unban'
*/
unbanForm.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: unban.url(args, options),
    method: 'post',
})

unban.form = unbanForm

/**
* @see \App\Http\Controllers\UserManagementController::mute
* @see app/Http/Controllers/UserManagementController.php:497
* @route '/users/{user}/mute'
*/
export const mute = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: mute.url(args, options),
    method: 'post',
})

mute.definition = {
    methods: ["post"],
    url: '/users/{user}/mute',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserManagementController::mute
* @see app/Http/Controllers/UserManagementController.php:497
* @route '/users/{user}/mute'
*/
mute.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { user: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: typeof args.user === 'object'
        ? args.user.id
        : args.user,
    }

    return mute.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserManagementController::mute
* @see app/Http/Controllers/UserManagementController.php:497
* @route '/users/{user}/mute'
*/
mute.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: mute.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::mute
* @see app/Http/Controllers/UserManagementController.php:497
* @route '/users/{user}/mute'
*/
const muteForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: mute.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::mute
* @see app/Http/Controllers/UserManagementController.php:497
* @route '/users/{user}/mute'
*/
muteForm.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: mute.url(args, options),
    method: 'post',
})

mute.form = muteForm

/**
* @see \App\Http\Controllers\UserManagementController::unmute
* @see app/Http/Controllers/UserManagementController.php:516
* @route '/users/{user}/unmute'
*/
export const unmute = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: unmute.url(args, options),
    method: 'post',
})

unmute.definition = {
    methods: ["post"],
    url: '/users/{user}/unmute',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserManagementController::unmute
* @see app/Http/Controllers/UserManagementController.php:516
* @route '/users/{user}/unmute'
*/
unmute.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { user: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: typeof args.user === 'object'
        ? args.user.id
        : args.user,
    }

    return unmute.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserManagementController::unmute
* @see app/Http/Controllers/UserManagementController.php:516
* @route '/users/{user}/unmute'
*/
unmute.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: unmute.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::unmute
* @see app/Http/Controllers/UserManagementController.php:516
* @route '/users/{user}/unmute'
*/
const unmuteForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: unmute.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::unmute
* @see app/Http/Controllers/UserManagementController.php:516
* @route '/users/{user}/unmute'
*/
unmuteForm.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: unmute.url(args, options),
    method: 'post',
})

unmute.form = unmuteForm

/**
* @see \App\Http\Controllers\UserManagementController::warn
* @see app/Http/Controllers/UserManagementController.php:530
* @route '/users/{user}/warn'
*/
export const warn = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: warn.url(args, options),
    method: 'post',
})

warn.definition = {
    methods: ["post"],
    url: '/users/{user}/warn',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserManagementController::warn
* @see app/Http/Controllers/UserManagementController.php:530
* @route '/users/{user}/warn'
*/
warn.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { user: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: typeof args.user === 'object'
        ? args.user.id
        : args.user,
    }

    return warn.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserManagementController::warn
* @see app/Http/Controllers/UserManagementController.php:530
* @route '/users/{user}/warn'
*/
warn.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: warn.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::warn
* @see app/Http/Controllers/UserManagementController.php:530
* @route '/users/{user}/warn'
*/
const warnForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: warn.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserManagementController::warn
* @see app/Http/Controllers/UserManagementController.php:530
* @route '/users/{user}/warn'
*/
warnForm.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: warn.url(args, options),
    method: 'post',
})

warn.form = warnForm

const users = {
    export: Object.assign(exportMethod, exportMethod),
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    kick: Object.assign(kick, kick),
    unkick: Object.assign(unkick, unkick),
    ban: Object.assign(ban, ban),
    unban: Object.assign(unban, unban),
    mute: Object.assign(mute, mute),
    unmute: Object.assign(unmute, unmute),
    warn: Object.assign(warn, warn),
    warnings: Object.assign(warnings, warnings),
}

export default users