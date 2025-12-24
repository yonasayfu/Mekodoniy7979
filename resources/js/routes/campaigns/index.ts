import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'

/**
* @see \App\Http\Controllers\CampaignController::index
* @see app/Http/Controllers/CampaignController.php:12
* @route '/campaigns'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
url: index.url(options),
method: 'get',
})

index.definition = {
methods: ["get","head"],
url: '/campaigns',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CampaignController::index
* @see app/Http/Controllers/CampaignController.php:12
* @route '/campaigns'
*/
index.url = (options?: RouteQueryOptions) => {
return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CampaignController::index
* @see app/Http/Controllers/CampaignController.php:12
* @route '/campaigns'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
url: index.url(options),
method: 'get',
})

/**
* @see \App\Http\Controllers\CampaignController::index
* @see app/Http/Controllers/CampaignController.php:12
* @route '/campaigns'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
url: index.url(options),
method: 'head',
})

/**
* @see \App\Http\Controllers\CampaignController::index
* @see app/Http/Controllers/CampaignController.php:12
* @route '/campaigns'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
action: index.url(options),
method: 'get',
})

/**
* @see \App\Http\Controllers\CampaignController::index
* @see app/Http/Controllers/CampaignController.php:12
* @route '/campaigns'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
action: index.url(options),
method: 'get',
})

/**
* @see \App\Http\Controllers\CampaignController::index
* @see app/Http/Controllers/CampaignController.php:12
* @route '/campaigns'
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
* @see \App\Http\Controllers\CampaignController::create
* @see app/Http/Controllers/CampaignController.php:49
* @route '/campaigns/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
url: create.url(options),
method: 'get',
})

create.definition = {
methods: ["get","head"],
url: '/campaigns/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CampaignController::create
* @see app/Http/Controllers/CampaignController.php:49
* @route '/campaigns/create'
*/
create.url = (options?: RouteQueryOptions) => {
return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CampaignController::create
* @see app/Http/Controllers/CampaignController.php:49
* @route '/campaigns/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
url: create.url(options),
method: 'get',
})

/**
* @see \App\Http\Controllers\CampaignController::create
* @see app/Http/Controllers/CampaignController.php:49
* @route '/campaigns/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
url: create.url(options),
method: 'head',
})

/**
* @see \App\Http\Controllers\CampaignController::create
* @see app/Http/Controllers/CampaignController.php:49
* @route '/campaigns/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
action: create.url(options),
method: 'get',
})

/**
* @see \App\Http\Controllers\CampaignController::create
* @see app/Http/Controllers/CampaignController.php:49
* @route '/campaigns/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
action: create.url(options),
method: 'get',
})

/**
* @see \App\Http\Controllers\CampaignController::create
* @see app/Http/Controllers/CampaignController.php:49
* @route '/campaigns/create'
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
* @see \App\Http\Controllers\CampaignController::store
* @see app/Http/Controllers/CampaignController.php:54
* @route '/campaigns'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
url: store.url(options),
method: 'post',
})

store.definition = {
methods: ["post"],
url: '/campaigns',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CampaignController::store
* @see app/Http/Controllers/CampaignController.php:54
* @route '/campaigns'
*/
store.url = (options?: RouteQueryOptions) => {
return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CampaignController::store
* @see app/Http/Controllers/CampaignController.php:54
* @route '/campaigns'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
url: store.url(options),
method: 'post',
})

/**
* @see \App\Http\Controllers\CampaignController::store
* @see app/Http/Controllers/CampaignController.php:54
* @route '/campaigns'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
action: store.url(options),
method: 'post',
})

/**
* @see \App\Http\Controllers\CampaignController::store
* @see app/Http/Controllers/CampaignController.php:54
* @route '/campaigns'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
action: store.url(options),
method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\CampaignController::show
* @see app/Http/Controllers/CampaignController.php:76
* @route '/campaigns/{campaign}'
*/
export const show = (id: string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
url: show.url(id, options),
method: 'get',
})

show.definition = {
methods: ["get","head"],
url: '/campaigns/{campaign}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CampaignController::show
* @see app/Http/Controllers/CampaignController.php:76
* @route '/campaigns/{campaign}'
*/
show.url = (id: string | number, options?: RouteQueryOptions) => {
return show.definition.url.replace('{campaign}', String(id)) + queryParams(options)
}

/**
* @see \App\Http\Controllers\CampaignController::show
* @see app/Http/Controllers/CampaignController.php:76
* @route '/campaigns/{campaign}'
*/
show.get = (id: string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
url: show.url(id, options),
method: 'get',
})

/**
* @see \App\Http\Controllers\CampaignController::show
* @see app/Http/Controllers/CampaignController.php:76
* @route '/campaigns/{campaign}'
*/
show.head = (id: string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
url: show.url(id, options),
method: 'head',
})

/**
* @see \App\Http\Controllers\CampaignController::show
* @see app/Http/Controllers/CampaignController.php:76
* @route '/campaigns/{campaign}'
*/
const showForm = (id: string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
action: show.url(id, options),
method: 'get',
})

/**
* @see \App\Http\Controllers\CampaignController::show
* @see app/Http/Controllers/CampaignController.php:76
* @route '/campaigns/{campaign}'
*/
showForm.get = (id: string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
action: show.url(id, options),
method: 'get',
})

/**
* @see \App\Http\Controllers\CampaignController::show
* @see app/Http/Controllers/CampaignController.php:76
* @route '/campaigns/{campaign}'
*/
showForm.head = (id: string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(id, options),
    method: 'get',
    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
        _method: 'HEAD',
        ...(options?.query ?? options?.mergeQuery ?? {}),
    },
})

show.form = showForm

/**
* @see \App\Http\Controllers\CampaignController::edit
* @see app/Http/Controllers/CampaignController.php:87
* @route '/campaigns/{campaign}/edit'
*/
export const edit = (id: string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
url: edit.url(id, options),
method: 'get',
})

edit.definition = {
methods: ["get","head"],
url: '/campaigns/{campaign}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CampaignController::edit
* @see app/Http/Controllers/CampaignController.php:87
* @route '/campaigns/{campaign}/edit'
*/
edit.url = (id: string | number, options?: RouteQueryOptions) => {
return edit.definition.url.replace('{campaign}', String(id)) + queryParams(options)
}

/**
* @see \App\Http\Controllers\CampaignController::edit
* @see app/Http/Controllers/CampaignController.php:87
* @route '/campaigns/{campaign}/edit'
*/
edit.get = (id: string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
url: edit.url(id, options),
method: 'get',
})

/**
* @see \App\Http\Controllers\CampaignController::edit
* @see app/Http/Controllers/CampaignController.php:87
* @route '/campaigns/{campaign}/edit'
*/
edit.head = (id: string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
url: edit.url(id, options),
method: 'head',
})

/**
* @see \App\Http\Controllers\CampaignController::edit
* @see app/Http/Controllers/CampaignController.php:87
* @route '/campaigns/{campaign}/edit'
*/
const editForm = (id: string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
action: edit.url(id, options),
method: 'get',
})

/**
* @see \App\Http\Controllers\CampaignController::edit
* @see app/Http/Controllers/CampaignController.php:87
* @route '/campaigns/{campaign}/edit'
*/
editForm.get = (id: string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
action: edit.url(id, options),
method: 'get',
})

/**
* @see \App\Http\Controllers\CampaignController::edit
* @see app/Http/Controllers/CampaignController.php:87
* @route '/campaigns/{campaign}/edit'
*/
editForm.head = (id: string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(id, options),
    method: 'get',
    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
        _method: 'HEAD',
        ...(options?.query ?? options?.mergeQuery ?? {}),
    },
})

edit.form = editForm

/**
* @see \App\Http\Controllers\CampaignController::update
* @see app/Http/Controllers/CampaignController.php:99
* @route '/campaigns/{campaign}'
*/
export const update = (id: string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
url: update.url(id, options),
method: 'put',
})

update.definition = {
methods: ["put","patch"],
url: '/campaigns/{campaign}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\CampaignController::update
* @see app/Http/Controllers/CampaignController.php:99
* @route '/campaigns/{campaign}'
*/
update.url = (id: string | number, options?: RouteQueryOptions) => {
return update.definition.url.replace('{campaign}', String(id)) + queryParams(options)
}

/**
* @see \App\Http\Controllers\CampaignController::update
* @see app/Http/Controllers/CampaignController.php:99
* @route '/campaigns/{campaign}'
*/
update.put = (id: string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
url: update.url(id, options),
method: 'put',
})

/**
* @see \App\Http\Controllers\CampaignController::update
* @see app/Http/Controllers/CampaignController.php:99
* @route '/campaigns/{campaign}'
*/
update.patch = (id: string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
url: update.url(id, options),
method: 'patch',
})

/**
* @see \App\Http\Controllers\CampaignController::update
* @see app/Http/Controllers/CampaignController.php:99
* @route '/campaigns/{campaign}'
*/
const updateForm = (id: string | number, options?: RouteQueryOptions): RouteFormDefinition<'put'> => ({
action: update.url(id, options),
method: 'put',
})

/**
* @see \App\Http\Controllers\CampaignController::update
* @see app/Http/Controllers/CampaignController.php:99
* @route '/campaigns/{campaign}'
*/
updateForm.put = (id: string | number, options?: RouteQueryOptions): RouteFormDefinition<'put'> => ({
action: update.url(id, options),
method: 'put',
})

/**
* @see \App\Http\Controllers\CampaignController::update
* @see app/Http/Controllers/CampaignController.php:99
* @route '/campaigns/{campaign}'
*/
updateForm.patch = (id: string | number, options?: RouteQueryOptions): RouteFormDefinition<'patch'> => ({
action: update.url(id, options),
method: 'patch',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\CampaignController::destroy
* @see app/Http/Controllers/CampaignController.php:125
* @route '/campaigns/{campaign}'
*/
export const destroy = (id: string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
url: destroy.url(id, options),
method: 'delete',
})

destroy.definition = {
methods: ["delete"],
url: '/campaigns/{campaign}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\CampaignController::destroy
* @see app/Http/Controllers/CampaignController.php:125
* @route '/campaigns/{campaign}'
*/
destroy.url = (id: string | number, options?: RouteQueryOptions) => {
return destroy.definition.url.replace('{campaign}', String(id)) + queryParams(options)
}

/**
* @see \App\Http\Controllers\CampaignController::destroy
* @see app/Http/Controllers/CampaignController.php:125
* @route '/campaigns/{campaign}'
*/
destroy.delete = (id: string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
url: destroy.url(id, options),
method: 'delete',
})

/**
* @see \App\Http\Controllers\CampaignController::destroy
* @see app/Http/Controllers/CampaignController.php:125
* @route '/campaigns/{campaign}'
*/
const destroyForm = (id: string | number, options?: RouteQueryOptions): RouteFormDefinition<'delete'> => ({
action: destroy.url(id, options),
method: 'delete',
})

/**
* @see \App\Http\Controllers\CampaignController::destroy
* @see app/Http/Controllers/CampaignController.php:125
* @route '/campaigns/{campaign}'
*/
destroyForm.delete = (id: string | number, options?: RouteQueryOptions): RouteFormDefinition<'delete'> => ({
action: destroy.url(id, options),
method: 'delete',
})

destroy.form = destroyForm