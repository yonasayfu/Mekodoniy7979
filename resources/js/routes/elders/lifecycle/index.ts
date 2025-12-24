import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\ElderLifecycleController::status
* @see app/Http/Controllers/ElderLifecycleController.php:17
* @route '/elders/{elder}/lifecycle/status'
*/
export const status = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: status.url(args, options),
    method: 'post',
})

status.definition = {
    methods: ["post"],
    url: '/elders/{elder}/lifecycle/status',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ElderLifecycleController::status
* @see app/Http/Controllers/ElderLifecycleController.php:17
* @route '/elders/{elder}/lifecycle/status'
*/
status.url = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return status.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ElderLifecycleController::status
* @see app/Http/Controllers/ElderLifecycleController.php:17
* @route '/elders/{elder}/lifecycle/status'
*/
status.post = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: status.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderLifecycleController::status
* @see app/Http/Controllers/ElderLifecycleController.php:17
* @route '/elders/{elder}/lifecycle/status'
*/
const statusForm = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: status.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ElderLifecycleController::status
* @see app/Http/Controllers/ElderLifecycleController.php:17
* @route '/elders/{elder}/lifecycle/status'
*/
statusForm.post = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: status.url(args, options),
    method: 'post',
})

status.form = statusForm

const lifecycle = {
    status: Object.assign(status, status),
}

export default lifecycle