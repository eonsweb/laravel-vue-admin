export function buildSortParam(sort?:string, direction?:'asc'|'desc'):string|undefined
{
    if(!sort) return undefined
    return direction === 'desc' ? `-${sort}` : sort
}