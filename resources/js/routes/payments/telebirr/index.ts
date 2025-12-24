import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Payments\TelebirrWebhookController::__invoke
* @see app/Http/Controllers/Payments/TelebirrWebhookController.php:20
* @route '/payments/telebirr/webhook'
*/
export const webhook = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: webhook.url(options),
    method: 'post',
})

webhook.definition = {
    methods: ["post"],
    url: '/payments/telebirr/webhook',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Payments\TelebirrWebhookController::__invoke
* @see app/Http/Controllers/Payments/TelebirrWebhookController.php:20
* @route '/payments/telebirr/webhook'
*/
webhook.url = (options?: RouteQueryOptions) => {
    return webhook.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Payments\TelebirrWebhookController::__invoke
* @see app/Http/Controllers/Payments/TelebirrWebhookController.php:20
* @route '/payments/telebirr/webhook'
*/
webhook.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: webhook.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Payments\TelebirrWebhookController::__invoke
* @see app/Http/Controllers/Payments/TelebirrWebhookController.php:20
* @route '/payments/telebirr/webhook'
*/
const webhookForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: webhook.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Payments\TelebirrWebhookController::__invoke
* @see app/Http/Controllers/Payments/TelebirrWebhookController.php:20
* @route '/payments/telebirr/webhook'
*/
webhookForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: webhook.url(options),
    method: 'post',
})

webhook.form = webhookForm

const telebirr = {
    webhook: Object.assign(webhook, webhook),
}

export default telebirr