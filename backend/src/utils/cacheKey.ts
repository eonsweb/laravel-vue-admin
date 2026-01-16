export function buildUsersCacheKey(
    page: number,
    perPage: number,
    sort: string | undefined,
    search: string | undefined,
) {
    const normalize = (v?: string | null) => (!v || v === '' ? 'none' : v)

    return `${page}-${perPage}-${normalize(sort)}-${normalize(search)}`
}
