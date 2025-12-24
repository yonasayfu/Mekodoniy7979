import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\TwoFactorEmailRecoveryController::codes
* @see app/Http/Controllers/TwoFactorEmailRecoveryController.php:12
* @route '/two-factor-email-recovery/codes'
*/
export const codes = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: codes.url(options),
    method: 'get',
})

codes.definition = {
    methods: ["get","head"],
    url: '/two-factor-email-recovery/codes',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TwoFactorEmailRecoveryController::codes
* @see app/Http/Controllers/TwoFactorEmailRecoveryController.php:12
* @route '/two-factor-email-recovery/codes'
*/
codes.url = (options?: RouteQueryOptions) => {
    return codes.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TwoFactorEmailRecoveryController::codes
* @see app/Http/Controllers/TwoFactorEmailRecoveryController.php:12
* @route '/two-factor-email-recovery/codes'
*/
codes.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: codes.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TwoFactorEmailRecoveryController::codes
* @see app/Http/Controllers/TwoFactorEmailRecoveryController.php:12
* @route '/two-factor-email-recovery/codes'
*/
codes.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: codes.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TwoFactorEmailRecoveryController::codes
* @see app/Http/Controllers/TwoFactorEmailRecoveryController.php:12
* @route '/two-factor-email-recovery/codes'
*/
const codesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: codes.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TwoFactorEmailRecoveryController::codes
* @see app/Http/Controllers/TwoFactorEmailRecoveryController.php:12
* @route '/two-factor-email-recovery/codes'
*/
codesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: codes.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TwoFactorEmailRecoveryController::codes
* @see app/Http/Controllers/TwoFactorEmailRecoveryController.php:12
* @route '/two-factor-email-recovery/codes'
*/
codesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: codes.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

codes.form = codesForm

/**
* @see \App\Http\Controllers\TwoFactorEmailRecoveryController::send
* @see app/Http/Controllers/TwoFactorEmailRecoveryController.php:23
* @route '/two-factor-email-recovery/send'
*/
export const send = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

send.definition = {
    methods: ["post"],
    url: '/two-factor-email-recovery/send',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TwoFactorEmailRecoveryController::send
* @see app/Http/Controllers/TwoFactorEmailRecoveryController.php:23
* @route '/two-factor-email-recovery/send'
*/
send.url = (options?: RouteQueryOptions) => {
    return send.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TwoFactorEmailRecoveryController::send
* @see app/Http/Controllers/TwoFactorEmailRecoveryController.php:23
* @route '/two-factor-email-recovery/send'
*/
send.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TwoFactorEmailRecoveryController::send
* @see app/Http/Controllers/TwoFactorEmailRecoveryController.php:23
* @route '/two-factor-email-recovery/send'
*/
const sendForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TwoFactorEmailRecoveryController::send
* @see app/Http/Controllers/TwoFactorEmailRecoveryController.php:23
* @route '/two-factor-email-recovery/send'
*/
sendForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send.url(options),
    method: 'post',
})

send.form = sendForm

/**
* @see \App\Http\Controllers\TwoFactorEmailRecoveryController::verify
* @see app/Http/Controllers/TwoFactorEmailRecoveryController.php:38
* @route '/two-factor-email-recovery/verify'
*/
export const verify = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verify.url(options),
    method: 'post',
})

verify.definition = {
    methods: ["post"],
    url: '/two-factor-email-recovery/verify',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TwoFactorEmailRecoveryController::verify
* @see app/Http/Controllers/TwoFactorEmailRecoveryController.php:38
* @route '/two-factor-email-recovery/verify'
*/
verify.url = (options?: RouteQueryOptions) => {
    return verify.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TwoFactorEmailRecoveryController::verify
* @see app/Http/Controllers/TwoFactorEmailRecoveryController.php:38
* @route '/two-factor-email-recovery/verify'
*/
verify.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verify.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TwoFactorEmailRecoveryController::verify
* @see app/Http/Controllers/TwoFactorEmailRecoveryController.php:38
* @route '/two-factor-email-recovery/verify'
*/
const verifyForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: verify.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TwoFactorEmailRecoveryController::verify
* @see app/Http/Controllers/TwoFactorEmailRecoveryController.php:38
* @route '/two-factor-email-recovery/verify'
*/
verifyForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: verify.url(options),
    method: 'post',
})

verify.form = verifyForm

const twoFactorEmailRecovery = {
    codes: Object.assign(codes, codes),
    send: Object.assign(send, send),
    verify: Object.assign(verify, verify),
}

export default twoFactorEmailRecovery