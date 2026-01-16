import lodashDebounce from 'lodash/debounce'

export function debounce<T extends (...args: any[]) => any>(
    fn: T,
    delay: number,
    options?: { leading?: boolean; trailing?: boolean; maxWait?: number },
) {
    return lodashDebounce(fn, delay, options) as (...args: Parameters<T>) => void
}
