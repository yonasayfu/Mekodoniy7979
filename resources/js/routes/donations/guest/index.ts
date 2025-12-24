import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\DonationController::store
* @see app/Http/Controllers/DonationController.php:21
* @route '/donations/guest'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/donations/guest',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DonationController::store
* @see app/Http/Controllers/DonationController.php:21
* @route '/donations/guest'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DonationController::store
* @see app/Http/Controllers/DonationController.php:21
* @route '/donations/guest'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DonationController::store
* @see app/Http/Controllers/DonationController.php:21
* @route '/donations/guest'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DonationController::store
* @see app/Http/Controllers/DonationController.php:21
* @route '/donations/guest'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const guest = {
    store: Object.assign(store, store),
}

export default guest