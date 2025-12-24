import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see routes/web.php:55
* @route '/guest-donation'
*/
export const donation = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: donation.url(options),
    method: 'get',
})

donation.definition = {
    methods: ["get","head"],
    url: '/guest-donation',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:55
* @route '/guest-donation'
*/
donation.url = (options?: RouteQueryOptions) => {
    return donation.definition.url + queryParams(options)
}

/**
* @see routes/web.php:55
* @route '/guest-donation'
*/
donation.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: donation.url(options),
    method: 'get',
})

/**
* @see routes/web.php:55
* @route '/guest-donation'
*/
donation.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: donation.url(options),
    method: 'head',
})

/**
* @see routes/web.php:55
* @route '/guest-donation'
*/
const donationForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: donation.url(options),
    method: 'get',
})

/**
* @see routes/web.php:55
* @route '/guest-donation'
*/
donationForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: donation.url(options),
    method: 'get',
})

/**
* @see routes/web.php:55
* @route '/guest-donation'
*/
donationForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: donation.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

donation.form = donationForm

const guest = {
    donation: Object.assign(donation, donation),
}

export default guest