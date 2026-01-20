import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http/Controllers/CaseNoteController::index
* @see app/Http/Controllers/CaseNoteController.php:19
* @route '/elders/{elder}/case-notes'
*/
export const index = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/elders/{elder}/case-notes',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App/Http/Controllers/CaseNoteController::index
* @see app/Http/Controllers/CaseNoteController.php:19
* @route '/elders/{elder}/case-notes'
*/
index.url = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { elder: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { elder: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            elder: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        elder: typeof args.elder === 'object'
        ? args.elder.id
        : args.elder,
    }

    return index.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App/Http/Controllers\CaseNoteController::index
* @see app/Http/Controllers/CaseNoteController.php:19
* @route '/elders/{elder}/case-notes'
*/
index.get = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App/Http/Controllers\CaseNoteController::index
* @see app/Http/Controllers/CaseNoteController.php:19
* @route '/elders/{elder}/case-notes'
*/
index.head = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

/**
* @see \App/Http/Controllers\CaseNoteController::index
* @see app/Http/Controllers/CaseNoteController.php:19
* @route '/elders/{elder}/case-notes'
*/
const indexForm = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App/Http/Controllers\CaseNoteController::index
* @see app/Http/Controllers/CaseNoteController.php:19
* @route '/elders/{elder}/case-notes'
*/
indexForm.get = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App/Http/Controllers\CaseNoteController::index
* @see app/Http/Controllers/CaseNoteController.php:19
* @route '/elders/{elder}/case-notes'
*/
indexForm.head = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App/Http/Controllers\CaseNoteController::store
* @see app/Http/Controllers/CaseNoteController.php:42
* @route '/elders/{elder}/case-notes'
*/
export const store = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/elders/{elder}/case-notes',
} satisfies RouteDefinition<["post"]>

/**
* @see \App/Http/Controllers\CaseNoteController::store
* @see app/Http/Controllers/CaseNoteController.php:42
* @route '/elders/{elder}/case-notes'
*/
store.url = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { elder: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { elder: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            elder: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        elder: typeof args.elder === 'object'
        ? args.elder.id
        : args.elder,
    }

    return store.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App/Http/Controllers\CaseNoteController::store
* @see app/Http/Controllers/CaseNoteController.php:42
* @route '/elders/{elder}/case-notes'
*/
store.post = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App/Http/Controllers\CaseNoteController::store
* @see app/Http/Controllers/CaseNoteController.php:42
* @route '/elders/{elder}/case-notes'
*/
const storeForm = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App/Http/Controllers\CaseNoteController::store
* @see app/Http/Controllers/CaseNoteController.php:42
* @route '/elders/{elder}/case-notes'
*/
storeForm.post = (args: { elder: number | { id: number } } | [elder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App/Http/Controllers\CaseNoteController::update
* @see app/Http/Controllers/CaseNoteController.php:59
* @route '/elders/{elder}/case-notes/{caseNote}'
*/
const updateWithCaseNote = (args: { elder: number | { id: number }, caseNote: number | { id: number } } | [elder: number | { id: number }, caseNote: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateWithCaseNote.url(args, options),
    method: 'put',
})

updateWithCaseNote.definition = {
    methods: ["put","patch"],
    url: '/elders/{elder}/case-notes/{caseNote}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App/Http/Controllers\CaseNoteController::update
* @see app/Http/Controllers/CaseNoteController.php:59
* @route '/elders/{elder}/case-notes/{caseNote}'
*/
updateWithCaseNote.url = (args: { elder: number | { id: number }, caseNote: number | { id: number } } | [elder: number | { id: number }, caseNote: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            elder: args[0],
            caseNote: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        elder: typeof args.elder === 'object'
        ? args.elder.id
        : args.elder,
        caseNote: typeof args.caseNote === 'object'
        ? args.caseNote.id
        : args.caseNote,
    }

    return updateWithCaseNote.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace('{caseNote}', parsedArgs.caseNote.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App/Http/Controllers\CaseNoteController::update
* @see app/Http/Controllers/CaseNoteController.php:59
* @route '/elders/{elder}/case-notes/{caseNote}'
*/
updateWithCaseNote.put = (args: { elder: number | { id: number }, caseNote: number | { id: number } } | [elder: number | { id: number }, caseNote: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateWithCaseNote.url(args, options),
    method: 'put',
})

/**
* @see \App/Http/Controllers\CaseNoteController::update
* @see app/Http/Controllers/CaseNoteController.php:59
* @route '/elders/{elder}/case-notes/{caseNote}'
*/
updateWithCaseNote.patch = (args: { elder: number | { id: number }, caseNote: number | { id: number } } | [elder: number | { id: number }, caseNote: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateWithCaseNote.url(args, options),
    method: 'patch',
})

/**
* @see \App/Http/Controllers\CaseNoteController::update
* @see app/Http/Controllers/CaseNoteController.php:59
* @route '/elders/{elder}/case-notes/{caseNote}'
*/
const updateFormWithCaseNote = (args: { elder: number | { id: number }, caseNote: number | { id: number } } | [elder: number | { id: number }, caseNote: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateWithCaseNote.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App/Http/Controllers\CaseNoteController::update
* @see app/Http/Controllers/CaseNoteController.php:59
* @route '/elders/{elder}/case-notes/{caseNote}'
*/
updateFormWithCaseNote.put = (args: { elder: number | { id: number }, caseNote: number | { id: number } } | [elder: number | { id: number }, caseNote: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateWithCaseNote.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App/Http/Controllers\CaseNoteController::update
* @see app/Http/Controllers/CaseNoteController.php:59
* @route '/elders/{elder}/case-notes/{caseNote}'
*/
updateFormWithCaseNote.patch = (args: { elder: number | { id: number }, caseNote: number | { id: number } } | [elder: number | { id: number }, caseNote: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateWithCaseNote.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateWithCaseNote.form = updateFormWithCaseNote

/**
* @see \App/Http/Controllers\CaseNoteController::update
* @see app/Http/Controllers/CaseNoteController.php:59
* @route '/elders/{elder}/case-notes/{case_note}'
*/
export const update = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/elders/{elder}/case-notes/{case_note}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App/Http/Controllers\CaseNoteController::update
* @see app/Http/Controllers/CaseNoteController.php:59
* @route '/elders/{elder}/case-notes/{case_note}'
*/
update.url = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            elder: args[0],
            case_note: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        elder: typeof args.elder === 'object'
        ? args.elder.id
        : args.elder,
        case_note: args.case_note,
    }

    return update.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace('{case_note}', parsedArgs.case_note.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App/Http/Controllers\CaseNoteController::update
* @see app/Http/Controllers/CaseNoteController.php:59
* @route '/elders/{elder}/case-notes/{case_note}'
*/
update.put = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App/Http/Controllers\CaseNoteController::update
* @see app/Http/Controllers/CaseNoteController.php:59
* @route '/elders/{elder}/case-notes/{case_note}'
*/
const updateForm = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App/Http/Controllers\CaseNoteController::update
* @see app/Http/Controllers/CaseNoteController.php:59
* @route '/elders/{elder}/case-notes/{case_note}'
*/
updateForm.put = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App/Http/Controllers\CaseNoteController::destroy
* @see app/Http/Controllers/CaseNoteController.php:71
* @route '/elders/{elder}/case-notes/{caseNote}'
*/
const destroyWithCaseNote = (args: { elder: number | { id: number }, caseNote: number | { id: number } } | [elder: number | { id: number }, caseNote: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyWithCaseNote.url(args, options),
    method: 'delete',
})

destroyWithCaseNote.definition = {
    methods: ["delete"],
    url: '/elders/{elder}/case-notes/{caseNote}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App/Http/Controllers\CaseNoteController::destroy
* @see app/Http/Controllers/CaseNoteController.php:71
* @route '/elders/{elder}/case-notes/{caseNote}'
*/
destroyWithCaseNote.url = (args: { elder: number | { id: number }, caseNote: number | { id: number } } | [elder: number | { id: number }, caseNote: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            elder: args[0],
            caseNote: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        elder: typeof args.elder === 'object'
        ? args.elder.id
        : args.elder,
        caseNote: typeof args.caseNote === 'object'
        ? args.caseNote.id
        : args.caseNote,
    }

    return destroyWithCaseNote.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace('{caseNote}', parsedArgs.caseNote.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App/Http/Controllers\CaseNoteController::destroy
* @see app/Http/Controllers/CaseNoteController.php:71
* @route '/elders/{elder}/case-notes/{caseNote}'
*/
destroyWithCaseNote.delete = (args: { elder: number | { id: number }, caseNote: number | { id: number } } | [elder: number | { id: number }, caseNote: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyWithCaseNote.url(args, options),
    method: 'delete',
})

/**
* @see \App/Http/Controllers\CaseNoteController::destroy
* @see app/Http/Controllers/CaseNoteController.php:71
* @route '/elders/{elder}/case-notes/{caseNote}'
*/
const destroyFormWithCaseNote = (args: { elder: number | { id: number }, caseNote: number | { id: number } } | [elder: number | { id: number }, caseNote: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyWithCaseNote.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App/Http/Controllers\CaseNoteController::destroy
* @see app/Http/Controllers/CaseNoteController.php:71
* @route '/elders/{elder}/case-notes/{caseNote}'
*/
destroyFormWithCaseNote.delete = (args: { elder: number | { id: number }, caseNote: number | { id: number } } | [elder: number | { id: number }, caseNote: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyWithCaseNote.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroyWithCaseNote.form = destroyFormWithCaseNote

/**
* @see \App/Http/Controllers\CaseNoteController::destroy
* @see app/Http/Controllers/CaseNoteController.php:71
* @route '/elders/{elder}/case-notes/{case_note}'
*/
export const destroy = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/elders/{elder}/case-notes/{case_note}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App/Http/Controllers\CaseNoteController::destroy
* @see app/Http/Controllers/CaseNoteController.php:71
* @route '/elders/{elder}/case-notes/{case_note}'
*/
destroy.url = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            elder: args[0],
            case_note: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        elder: typeof args.elder === 'object'
        ? args.elder.id
        : args.elder,
        case_note: args.case_note,
    }

    return destroy.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace('{case_note}', parsedArgs.case_note.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App/Http/Controllers\CaseNoteController::destroy
* @see app/Http/Controllers/CaseNoteController.php:71
* @route '/elders/{elder}/case-notes/{case_note}'
*/
destroy.delete = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App/Http/Controllers\CaseNoteController::destroy
* @see app/Http/Controllers/CaseNoteController.php:71
* @route '/elders/{elder}/case-notes/{case_note}'
*/
const destroyForm = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App/Http/Controllers\CaseNoteController::destroy
* @see app/Http/Controllers/CaseNoteController.php:71
* @route '/elders/{elder}/case-notes/{case_note}'
*/
destroyForm.delete = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see \App/Http/Controllers\CaseNoteController::restore
* @see app/Http/Controllers/CaseNoteController.php:83
* @route '/elders/{elder}/case-notes/{case_note}/restore'
*/
export const restore = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restore.url(args, options),
    method: 'post',
})

restore.definition = {
    methods: ["post"],
    url: '/elders/{elder}/case-notes/{case_note}/restore',
} satisfies RouteDefinition<["post"]>

/**
* @see \App/Http/Controllers\CaseNoteController::restore
* @see app/Http/Controllers/CaseNoteController.php:83
* @route '/elders/{elder}/case-notes/{case_note}/restore'
*/
restore.url = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            elder: args[0],
            case_note: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        elder: typeof args.elder === 'object'
        ? args.elder.id
        : args.elder,
        case_note: args.case_note,
    }

    return restore.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace('{case_note}', parsedArgs.case_note.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App/Http/Controllers\CaseNoteController::restore
* @see app/Http/Controllers/CaseNoteController.php:83
* @route '/elders/{elder}/case-notes/{case_note}/restore'
*/
restore.post = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restore.url(args, options),
    method: 'post',
})

/**
* @see \App/Http/Controllers\CaseNoteController::restore
* @see app/Http/Controllers/CaseNoteController.php:83
* @route '/elders/{elder}/case-notes/{case_note}/restore'
*/
const restoreForm = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restore.url(args, options),
    method: 'post',
})

/**
* @see \App/Http/Controllers\CaseNoteController::restore
* @see app/Http/Controllers/CaseNoteController.php:83
* @route '/elders/{elder}/case-notes/{case_note}/restore'
*/
restoreForm.post = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restore.url(args, options),
    method: 'post',
})

restore.form = restoreForm

/**
* @see \App/Http/Controllers\CaseNoteController::restore
* @see app/Http/Controllers/CaseNoteController.php:83
* @route '/elders/{elder}/case-notes/{case_note}/restore'
*/
export const restorePut = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: restorePut.url(args, options),
    method: 'put',
})

restorePut.definition = {
    methods: ["put"],
    url: '/elders/{elder}/case-notes/{case_note}/restore',
} satisfies RouteDefinition<["put"]>

/**
* @see \App/Http/Controllers\CaseNoteController::restore
* @see app/Http/Controllers/CaseNoteController.php:83
* @route '/elders/{elder}/case-notes/{case_note}/restore'
*/
restorePut.url = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            elder: args[0],
            case_note: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        elder: typeof args.elder === 'object'
        ? args.elder.id
        : args.elder,
        case_note: args.case_note,
    }

    return restorePut.definition.url
            .replace('{elder}', parsedArgs.elder.toString())
            .replace('{case_note}', parsedArgs.case_note.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App/Http/Controllers\CaseNoteController::restore
* @see app/Http/Controllers/CaseNoteController.php:83
* @route '/elders/{elder}/case-notes/{case_note}/restore'
*/
restorePut.put = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: restorePut.url(args, options),
    method: 'put',
})

/**
* @see \App/Http/Controllers\CaseNoteController::restore
* @see app/Http/Controllers/CaseNoteController.php:83
* @route '/elders/{elder}/case-notes/{case_note}/restore'
*/
const restoreFormPut = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restorePut.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App/Http/Controllers\CaseNoteController::restore
* @see app/Http/Controllers/CaseNoteController.php:83
* @route '/elders/{elder}/case-notes/{case_note}/restore'
*/
restoreFormPut.put = (args: { elder: number | { id: number }, case_note: string | number } | [elder: number | { id: number }, case_note: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restorePut.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

restorePut.form = restoreFormPut

const caseNotes = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    restore: Object.assign(restore, restore),
}

export default caseNotes
