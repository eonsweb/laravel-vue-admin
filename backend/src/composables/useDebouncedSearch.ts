import { ref, watch } from 'vue'

interface DebounceOptions {
  delay?: number
  leading?: boolean
}

export function useDebouncedSearch(
  initialValue = '',
  onChange: (value: string) => void,
  options: DebounceOptions = {},
) {
  const { delay = 500 } = options

  const value = ref(initialValue)
  let timeout: number | undefined

  watch(value, (newValue) => {
    if (timeout) {
      clearTimeout(timeout)
    }

    timeout = window.setTimeout(() => {
      onChange(newValue)
    }, delay)
  })

  function reset() {
    value.value = ''
  }

  return {
    value,
    reset,
  }
}
