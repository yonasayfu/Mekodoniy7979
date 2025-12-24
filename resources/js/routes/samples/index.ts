import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see routes/web.php:308
* @route '/samples'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/samples',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:308
* @route '/samples'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see routes/web.php:308
* @route '/samples'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:308
* @route '/samples'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see routes/web.php:308
* @route '/samples'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:308
* @route '/samples'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:308
* @route '/samples'
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
* @see routes/web.php:312
* @route '/samples/admin'
*/
export const admin = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: admin.url(options),
    method: 'get',
})

admin.definition = {
    methods: ["get","head"],
    url: '/samples/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:312
* @route '/samples/admin'
*/
admin.url = (options?: RouteQueryOptions) => {
    return admin.definition.url + queryParams(options)
}

/**
* @see routes/web.php:312
* @route '/samples/admin'
*/
admin.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: admin.url(options),
    method: 'get',
})

/**
* @see routes/web.php:312
* @route '/samples/admin'
*/
admin.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: admin.url(options),
    method: 'head',
})

/**
* @see routes/web.php:312
* @route '/samples/admin'
*/
const adminForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: admin.url(options),
    method: 'get',
})

/**
* @see routes/web.php:312
* @route '/samples/admin'
*/
adminForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: admin.url(options),
    method: 'get',
})

/**
* @see routes/web.php:312
* @route '/samples/admin'
*/
adminForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: admin.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

admin.form = adminForm

/**
* @see routes/web.php:316
* @route '/samples/manager'
*/
export const manager = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: manager.url(options),
    method: 'get',
})

manager.definition = {
    methods: ["get","head"],
    url: '/samples/manager',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:316
* @route '/samples/manager'
*/
manager.url = (options?: RouteQueryOptions) => {
    return manager.definition.url + queryParams(options)
}

/**
* @see routes/web.php:316
* @route '/samples/manager'
*/
manager.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: manager.url(options),
    method: 'get',
})

/**
* @see routes/web.php:316
* @route '/samples/manager'
*/
manager.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: manager.url(options),
    method: 'head',
})

/**
* @see routes/web.php:316
* @route '/samples/manager'
*/
const managerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: manager.url(options),
    method: 'get',
})

/**
* @see routes/web.php:316
* @route '/samples/manager'
*/
managerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: manager.url(options),
    method: 'get',
})

/**
* @see routes/web.php:316
* @route '/samples/manager'
*/
managerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: manager.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

manager.form = managerForm

/**
* @see routes/web.php:320
* @route '/samples/technician'
*/
export const technician = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: technician.url(options),
    method: 'get',
})

technician.definition = {
    methods: ["get","head"],
    url: '/samples/technician',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:320
* @route '/samples/technician'
*/
technician.url = (options?: RouteQueryOptions) => {
    return technician.definition.url + queryParams(options)
}

/**
* @see routes/web.php:320
* @route '/samples/technician'
*/
technician.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: technician.url(options),
    method: 'get',
})

/**
* @see routes/web.php:320
* @route '/samples/technician'
*/
technician.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: technician.url(options),
    method: 'head',
})

/**
* @see routes/web.php:320
* @route '/samples/technician'
*/
const technicianForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: technician.url(options),
    method: 'get',
})

/**
* @see routes/web.php:320
* @route '/samples/technician'
*/
technicianForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: technician.url(options),
    method: 'get',
})

/**
* @see routes/web.php:320
* @route '/samples/technician'
*/
technicianForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: technician.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

technician.form = technicianForm

/**
* @see routes/web.php:324
* @route '/samples/external'
*/
export const external = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: external.url(options),
    method: 'get',
})

external.definition = {
    methods: ["get","head"],
    url: '/samples/external',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:324
* @route '/samples/external'
*/
external.url = (options?: RouteQueryOptions) => {
    return external.definition.url + queryParams(options)
}

/**
* @see routes/web.php:324
* @route '/samples/external'
*/
external.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: external.url(options),
    method: 'get',
})

/**
* @see routes/web.php:324
* @route '/samples/external'
*/
external.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: external.url(options),
    method: 'head',
})

/**
* @see routes/web.php:324
* @route '/samples/external'
*/
const externalForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: external.url(options),
    method: 'get',
})

/**
* @see routes/web.php:324
* @route '/samples/external'
*/
externalForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: external.url(options),
    method: 'get',
})

/**
* @see routes/web.php:324
* @route '/samples/external'
*/
externalForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: external.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

external.form = externalForm

const samples = {
    index: Object.assign(index, index),
    admin: Object.assign(admin, admin),
    manager: Object.assign(manager, manager),
    technician: Object.assign(technician, technician),
    external: Object.assign(external, external),
}

export default samples