import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import guest from './guest'
/**
* @see \App\Http\Controllers\DonationController::store
* @see app/Http/Controllers/DonationController.php:56
* @route '/donations'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/donations',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DonationController::store
* @see app/Http/Controllers/DonationController.php:56
* @route '/donations'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DonationController::store
* @see app/Http/Controllers/DonationController.php:56
* @route '/donations'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DonationController::store
* @see app/Http/Controllers/DonationController.php:56
* @route '/donations'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DonationController::store
* @see app/Http/Controllers/DonationController.php:56
* @route '/donations'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const donations = {
    guest: Object.assign(guest, guest),
    store: Object.assign(store, store),
}

export default donations