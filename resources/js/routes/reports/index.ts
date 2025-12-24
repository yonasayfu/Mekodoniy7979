import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\ReportController::index
* @see app/Http/Controllers/ReportController.php:27
* @route '/reports'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/reports',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::index
* @see app/Http/Controllers/ReportController.php:27
* @route '/reports'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::index
* @see app/Http/Controllers/ReportController.php:27
* @route '/reports'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::index
* @see app/Http/Controllers/ReportController.php:27
* @route '/reports'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ReportController::index
* @see app/Http/Controllers/ReportController.php:27
* @route '/reports'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::index
* @see app/Http/Controllers/ReportController.php:27
* @route '/reports'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::index
* @see app/Http/Controllers/ReportController.php:27
* @route '/reports'
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
* @see \App\Http\Controllers\ReportController::donations
* @see app/Http/Controllers/ReportController.php:82
* @route '/reports/donations'
*/
export const donations = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: donations.url(options),
    method: 'get',
})

donations.definition = {
    methods: ["get","head"],
    url: '/reports/donations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::donations
* @see app/Http/Controllers/ReportController.php:82
* @route '/reports/donations'
*/
donations.url = (options?: RouteQueryOptions) => {
    return donations.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::donations
* @see app/Http/Controllers/ReportController.php:82
* @route '/reports/donations'
*/
donations.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: donations.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::donations
* @see app/Http/Controllers/ReportController.php:82
* @route '/reports/donations'
*/
donations.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: donations.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ReportController::donations
* @see app/Http/Controllers/ReportController.php:82
* @route '/reports/donations'
*/
const donationsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: donations.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::donations
* @see app/Http/Controllers/ReportController.php:82
* @route '/reports/donations'
*/
donationsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: donations.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::donations
* @see app/Http/Controllers/ReportController.php:82
* @route '/reports/donations'
*/
donationsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: donations.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

donations.form = donationsForm

/**
* @see \App\Http\Controllers\ReportController::detailed
* @see app/Http/Controllers/ReportController.php:121
* @route '/reports/detailed'
*/
export const detailed = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detailed.url(options),
    method: 'get',
})

detailed.definition = {
    methods: ["get","head"],
    url: '/reports/detailed',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::detailed
* @see app/Http/Controllers/ReportController.php:121
* @route '/reports/detailed'
*/
detailed.url = (options?: RouteQueryOptions) => {
    return detailed.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::detailed
* @see app/Http/Controllers/ReportController.php:121
* @route '/reports/detailed'
*/
detailed.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detailed.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::detailed
* @see app/Http/Controllers/ReportController.php:121
* @route '/reports/detailed'
*/
detailed.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: detailed.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ReportController::detailed
* @see app/Http/Controllers/ReportController.php:121
* @route '/reports/detailed'
*/
const detailedForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: detailed.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::detailed
* @see app/Http/Controllers/ReportController.php:121
* @route '/reports/detailed'
*/
detailedForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: detailed.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::detailed
* @see app/Http/Controllers/ReportController.php:121
* @route '/reports/detailed'
*/
detailedForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: detailed.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

detailed.form = detailedForm

/**
* @see \App\Http\Controllers\ReportController::activity
* @see app/Http/Controllers/ReportController.php:164
* @route '/reports/activity'
*/
export const activity = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activity.url(options),
    method: 'get',
})

activity.definition = {
    methods: ["get","head"],
    url: '/reports/activity',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::activity
* @see app/Http/Controllers/ReportController.php:164
* @route '/reports/activity'
*/
activity.url = (options?: RouteQueryOptions) => {
    return activity.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::activity
* @see app/Http/Controllers/ReportController.php:164
* @route '/reports/activity'
*/
activity.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activity.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::activity
* @see app/Http/Controllers/ReportController.php:164
* @route '/reports/activity'
*/
activity.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: activity.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ReportController::activity
* @see app/Http/Controllers/ReportController.php:164
* @route '/reports/activity'
*/
const activityForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: activity.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::activity
* @see app/Http/Controllers/ReportController.php:164
* @route '/reports/activity'
*/
activityForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: activity.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::activity
* @see app/Http/Controllers/ReportController.php:164
* @route '/reports/activity'
*/
activityForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: activity.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

activity.form = activityForm

/**
* @see \App\Http\Controllers\ReportController::exportMethod
* @see app/Http/Controllers/ReportController.php:188
* @route '/reports/export'
*/
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/reports/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::exportMethod
* @see app/Http/Controllers/ReportController.php:188
* @route '/reports/export'
*/
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::exportMethod
* @see app/Http/Controllers/ReportController.php:188
* @route '/reports/export'
*/
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::exportMethod
* @see app/Http/Controllers/ReportController.php:188
* @route '/reports/export'
*/
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ReportController::exportMethod
* @see app/Http/Controllers/ReportController.php:188
* @route '/reports/export'
*/
const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::exportMethod
* @see app/Http/Controllers/ReportController.php:188
* @route '/reports/export'
*/
exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::exportMethod
* @see app/Http/Controllers/ReportController.php:188
* @route '/reports/export'
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
* @see \App\Http\Controllers\ReportController::impactBook
* @see app/Http/Controllers/ReportController.php:97
* @route '/reports/impact-book'
*/
export const impactBook = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: impactBook.url(options),
    method: 'get',
})

impactBook.definition = {
    methods: ["get","head"],
    url: '/reports/impact-book',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::impactBook
* @see app/Http/Controllers/ReportController.php:97
* @route '/reports/impact-book'
*/
impactBook.url = (options?: RouteQueryOptions) => {
    return impactBook.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::impactBook
* @see app/Http/Controllers/ReportController.php:97
* @route '/reports/impact-book'
*/
impactBook.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: impactBook.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::impactBook
* @see app/Http/Controllers/ReportController.php:97
* @route '/reports/impact-book'
*/
impactBook.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: impactBook.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ReportController::impactBook
* @see app/Http/Controllers/ReportController.php:97
* @route '/reports/impact-book'
*/
const impactBookForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: impactBook.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::impactBook
* @see app/Http/Controllers/ReportController.php:97
* @route '/reports/impact-book'
*/
impactBookForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: impactBook.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::impactBook
* @see app/Http/Controllers/ReportController.php:97
* @route '/reports/impact-book'
*/
impactBookForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: impactBook.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

impactBook.form = impactBookForm

const reports = {
    index: Object.assign(index, index),
    donations: Object.assign(donations, donations),
    detailed: Object.assign(detailed, detailed),
    activity: Object.assign(activity, activity),
    export: Object.assign(exportMethod, exportMethod),
    impactBook: Object.assign(impactBook, impactBook),
}

export default reports