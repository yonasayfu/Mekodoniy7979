import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\NotificationPreferenceController::index
* @see app/Http/Controllers/NotificationPreferenceController.php:19
* @route '/profile/notification-preferences'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/profile/notification-preferences',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\NotificationPreferenceController::index
* @see app/Http/Controllers/NotificationPreferenceController.php:19
* @route '/profile/notification-preferences'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\NotificationPreferenceController::index
* @see app/Http/Controllers/NotificationPreferenceController.php:19
* @route '/profile/notification-preferences'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\NotificationPreferenceController::index
* @see app/Http/Controllers/NotificationPreferenceController.php:19
* @route '/profile/notification-preferences'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\NotificationPreferenceController::index
* @see app/Http/Controllers/NotificationPreferenceController.php:19
* @route '/profile/notification-preferences'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\NotificationPreferenceController::index
* @see app/Http/Controllers/NotificationPreferenceController.php:19
* @route '/profile/notification-preferences'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\NotificationPreferenceController::index
* @see app/Http/Controllers/NotificationPreferenceController.php:19
* @route '/profile/notification-preferences'
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
* @see \App\Http\Controllers\NotificationPreferenceController::update
* @see app/Http/Controllers/NotificationPreferenceController.php:31
* @route '/profile/notification-preferences'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/profile/notification-preferences',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\NotificationPreferenceController::update
* @see app/Http/Controllers/NotificationPreferenceController.php:31
* @route '/profile/notification-preferences'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\NotificationPreferenceController::update
* @see app/Http/Controllers/NotificationPreferenceController.php:31
* @route '/profile/notification-preferences'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NotificationPreferenceController::update
* @see app/Http/Controllers/NotificationPreferenceController.php:31
* @route '/profile/notification-preferences'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NotificationPreferenceController::update
* @see app/Http/Controllers/NotificationPreferenceController.php:31
* @route '/profile/notification-preferences'
*/
updateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

update.form = updateForm

const notificationPreferences = {
    index: Object.assign(index, index),
    update: Object.assign(update, update),
}

export default notificationPreferences