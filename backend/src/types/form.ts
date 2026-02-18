export type FormFieldType =
    | 'text'
    | 'email'
    | 'password'
    | 'number'
    | 'textarea'
    | 'file'
    | 'select'
    | 'checkbox'
    | 'radio'

export type Mode = 'create' | 'edit'

export interface FormField {
    name: string
    label: string
    type: FormFieldType
    placeholder?: string
    options?: { value: string | number; label: string }[] // For select, checkbox, radio
}
