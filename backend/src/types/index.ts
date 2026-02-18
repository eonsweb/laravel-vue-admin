export interface Permission {
    id: number
    name: string
}

export interface Role {
    id: number
    name: string
    permissions: Permission[]
}

export interface User {
    id?: number
    name: string
    username: string
    email: string
    avatar?: string
    bio?: string
    created_at?: string //ISO date string,
    updated_at?: string // ISO date string,

    permissions?: Permission[]
    direct_permissions?: Permission[]
}

export interface LoginResponse {
    message: string
    status: number
    data: {
        user: User
        token: string
    }
}
export interface ApiResponse<T> {
    data: T
}
export interface AuthErrorResponse {
    message: string
    status: number
}

export interface Payment {
    id: string
    amount: number
    status: 'pending' | 'processing' | 'success' | 'failed'
    email: string
}

export interface FetchUserOptions {
    sort?: string
    search?: string
}

//Pagination response meta interface
export interface PaginatedMeta {
    current_page: number
    last_page: number
    per_page: number
    total: number
}

export interface PaginatedResponse<T> {
    data: T[]
    meta: PaginatedMeta
}
