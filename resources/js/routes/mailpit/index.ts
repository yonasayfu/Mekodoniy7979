import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Mailbox\MailpitWebhookController::__invoke
* @see app/Http/Controllers/Mailbox/MailpitWebhookController.php:12
* @route '/mailpit/webhook'
*/
export const webhook = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: webhook.url(options),
    method: 'post',
})

webhook.definition = {
    methods: ["post"],
    url: '/mailpit/webhook',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Mailbox\MailpitWebhookController::__invoke
* @see app/Http/Controllers/Mailbox/MailpitWebhookController.php:12
* @route '/mailpit/webhook'
*/
webhook.url = (options?: RouteQueryOptions) => {
    return webhook.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Mailbox\MailpitWebhookController::__invoke
* @see app/Http/Controllers/Mailbox/MailpitWebhookController.php:12
* @route '/mailpit/webhook'
*/
webhook.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: webhook.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Mailbox\MailpitWebhookController::__invoke
* @see app/Http/Controllers/Mailbox/MailpitWebhookController.php:12
* @route '/mailpit/webhook'
*/
const webhookForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: webhook.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Mailbox\MailpitWebhookController::__invoke
* @see app/Http/Controllers/Mailbox/MailpitWebhookController.php:12
* @route '/mailpit/webhook'
*/
webhookForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: webhook.url(options),
    method: 'post',
})

webhook.form = webhookForm

const mailpit = {
    webhook: Object.assign(webhook, webhook),
}

export default mailpit