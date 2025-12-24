import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\PendingApprovalController::__invoke
* @see app/Http/Controllers/PendingApprovalController.php:11
* @route '/onboarding/pending-approval'
*/
export const pending = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pending.url(options),
    method: 'get',
})

pending.definition = {
    methods: ["get","head"],
    url: '/onboarding/pending-approval',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PendingApprovalController::__invoke
* @see app/Http/Controllers/PendingApprovalController.php:11
* @route '/onboarding/pending-approval'
*/
pending.url = (options?: RouteQueryOptions) => {
    return pending.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PendingApprovalController::__invoke
* @see app/Http/Controllers/PendingApprovalController.php:11
* @route '/onboarding/pending-approval'
*/
pending.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pending.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PendingApprovalController::__invoke
* @see app/Http/Controllers/PendingApprovalController.php:11
* @route '/onboarding/pending-approval'
*/
pending.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: pending.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PendingApprovalController::__invoke
* @see app/Http/Controllers/PendingApprovalController.php:11
* @route '/onboarding/pending-approval'
*/
const pendingForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pending.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PendingApprovalController::__invoke
* @see app/Http/Controllers/PendingApprovalController.php:11
* @route '/onboarding/pending-approval'
*/
pendingForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pending.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PendingApprovalController::__invoke
* @see app/Http/Controllers/PendingApprovalController.php:11
* @route '/onboarding/pending-approval'
*/
pendingForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pending.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

pending.form = pendingForm

const onboarding = {
    pending: Object.assign(pending, pending),
}

export default onboarding