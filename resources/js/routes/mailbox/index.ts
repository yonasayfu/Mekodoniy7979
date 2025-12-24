import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\MailboxController::index
* @see app/Http/Controllers/MailboxController.php:12
* @route '/mailbox'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/mailbox',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MailboxController::index
* @see app/Http/Controllers/MailboxController.php:12
* @route '/mailbox'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MailboxController::index
* @see app/Http/Controllers/MailboxController.php:12
* @route '/mailbox'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\MailboxController::index
* @see app/Http/Controllers/MailboxController.php:12
* @route '/mailbox'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\MailboxController::index
* @see app/Http/Controllers/MailboxController.php:12
* @route '/mailbox'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\MailboxController::index
* @see app/Http/Controllers/MailboxController.php:12
* @route '/mailbox'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\MailboxController::index
* @see app/Http/Controllers/MailboxController.php:12
* @route '/mailbox'
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
* @see \App\Http\Controllers\MailboxController::show
* @see app/Http/Controllers/MailboxController.php:44
* @route '/mailbox/{message}'
*/
export const show = (args: { message: string | { id: string } } | [message: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/mailbox/{message}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MailboxController::show
* @see app/Http/Controllers/MailboxController.php:44
* @route '/mailbox/{message}'
*/
show.url = (args: { message: string | { id: string } } | [message: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { message: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { message: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            message: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        message: typeof args.message === 'object'
        ? args.message.id
        : args.message,
    }

    return show.definition.url
            .replace('{message}', parsedArgs.message.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MailboxController::show
* @see app/Http/Controllers/MailboxController.php:44
* @route '/mailbox/{message}'
*/
show.get = (args: { message: string | { id: string } } | [message: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\MailboxController::show
* @see app/Http/Controllers/MailboxController.php:44
* @route '/mailbox/{message}'
*/
show.head = (args: { message: string | { id: string } } | [message: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\MailboxController::show
* @see app/Http/Controllers/MailboxController.php:44
* @route '/mailbox/{message}'
*/
const showForm = (args: { message: string | { id: string } } | [message: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\MailboxController::show
* @see app/Http/Controllers/MailboxController.php:44
* @route '/mailbox/{message}'
*/
showForm.get = (args: { message: string | { id: string } } | [message: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\MailboxController::show
* @see app/Http/Controllers/MailboxController.php:44
* @route '/mailbox/{message}'
*/
showForm.head = (args: { message: string | { id: string } } | [message: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\MailboxController::process
* @see app/Http/Controllers/MailboxController.php:68
* @route '/mailbox/{message}/process'
*/
export const process = (args: { message: string | { id: string } } | [message: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: process.url(args, options),
    method: 'post',
})

process.definition = {
    methods: ["post"],
    url: '/mailbox/{message}/process',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MailboxController::process
* @see app/Http/Controllers/MailboxController.php:68
* @route '/mailbox/{message}/process'
*/
process.url = (args: { message: string | { id: string } } | [message: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { message: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { message: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            message: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        message: typeof args.message === 'object'
        ? args.message.id
        : args.message,
    }

    return process.definition.url
            .replace('{message}', parsedArgs.message.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MailboxController::process
* @see app/Http/Controllers/MailboxController.php:68
* @route '/mailbox/{message}/process'
*/
process.post = (args: { message: string | { id: string } } | [message: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: process.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MailboxController::process
* @see app/Http/Controllers/MailboxController.php:68
* @route '/mailbox/{message}/process'
*/
const processForm = (args: { message: string | { id: string } } | [message: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: process.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MailboxController::process
* @see app/Http/Controllers/MailboxController.php:68
* @route '/mailbox/{message}/process'
*/
processForm.post = (args: { message: string | { id: string } } | [message: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: process.url(args, options),
    method: 'post',
})

process.form = processForm

const mailbox = {
    index: Object.assign(index, index),
    show: Object.assign(show, show),
    process: Object.assign(process, process),
}

export default mailbox