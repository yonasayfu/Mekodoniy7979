import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\DataExportController::index
* @see app/Http/Controllers/DataExportController.php:14
* @route '/exports'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/exports',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DataExportController::index
* @see app/Http/Controllers/DataExportController.php:14
* @route '/exports'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DataExportController::index
* @see app/Http/Controllers/DataExportController.php:14
* @route '/exports'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DataExportController::index
* @see app/Http/Controllers/DataExportController.php:14
* @route '/exports'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DataExportController::index
* @see app/Http/Controllers/DataExportController.php:14
* @route '/exports'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DataExportController::index
* @see app/Http/Controllers/DataExportController.php:14
* @route '/exports'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DataExportController::index
* @see app/Http/Controllers/DataExportController.php:14
* @route '/exports'
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
* @see \App\Http\Controllers\DataExportController::download
* @see app/Http/Controllers/DataExportController.php:58
* @route '/exports/{export}'
*/
export const download = (args: { export: string | { uuid: string } } | [exportParam: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

download.definition = {
    methods: ["get","head"],
    url: '/exports/{export}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DataExportController::download
* @see app/Http/Controllers/DataExportController.php:58
* @route '/exports/{export}'
*/
download.url = (args: { export: string | { uuid: string } } | [exportParam: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { export: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'uuid' in args) {
        args = { export: args.uuid }
    }

    if (Array.isArray(args)) {
        args = {
            export: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        export: typeof args.export === 'object'
        ? args.export.uuid
        : args.export,
    }

    return download.definition.url
            .replace('{export}', parsedArgs.export.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DataExportController::download
* @see app/Http/Controllers/DataExportController.php:58
* @route '/exports/{export}'
*/
download.get = (args: { export: string | { uuid: string } } | [exportParam: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DataExportController::download
* @see app/Http/Controllers/DataExportController.php:58
* @route '/exports/{export}'
*/
download.head = (args: { export: string | { uuid: string } } | [exportParam: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: download.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DataExportController::download
* @see app/Http/Controllers/DataExportController.php:58
* @route '/exports/{export}'
*/
const downloadForm = (args: { export: string | { uuid: string } } | [exportParam: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DataExportController::download
* @see app/Http/Controllers/DataExportController.php:58
* @route '/exports/{export}'
*/
downloadForm.get = (args: { export: string | { uuid: string } } | [exportParam: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DataExportController::download
* @see app/Http/Controllers/DataExportController.php:58
* @route '/exports/{export}'
*/
downloadForm.head = (args: { export: string | { uuid: string } } | [exportParam: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

download.form = downloadForm

/**
* @see \App\Http\Controllers\DataExportController::destroy
* @see app/Http/Controllers/DataExportController.php:74
* @route '/exports/{export}'
*/
export const destroy = (args: { export: string | { uuid: string } } | [exportParam: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/exports/{export}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\DataExportController::destroy
* @see app/Http/Controllers/DataExportController.php:74
* @route '/exports/{export}'
*/
destroy.url = (args: { export: string | { uuid: string } } | [exportParam: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { export: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'uuid' in args) {
        args = { export: args.uuid }
    }

    if (Array.isArray(args)) {
        args = {
            export: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        export: typeof args.export === 'object'
        ? args.export.uuid
        : args.export,
    }

    return destroy.definition.url
            .replace('{export}', parsedArgs.export.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DataExportController::destroy
* @see app/Http/Controllers/DataExportController.php:74
* @route '/exports/{export}'
*/
destroy.delete = (args: { export: string | { uuid: string } } | [exportParam: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\DataExportController::destroy
* @see app/Http/Controllers/DataExportController.php:74
* @route '/exports/{export}'
*/
const destroyForm = (args: { export: string | { uuid: string } } | [exportParam: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DataExportController::destroy
* @see app/Http/Controllers/DataExportController.php:74
* @route '/exports/{export}'
*/
destroyForm.delete = (args: { export: string | { uuid: string } } | [exportParam: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const exports = {
    index: Object.assign(index, index),
    download: Object.assign(download, download),
    destroy: Object.assign(destroy, destroy),
}

export default exports