import { z } from 'zod'

export const loginSchema = z.object({
    email: z.string().nonempty('Email is required *').email({ message: 'Invalid email address *' }),
    password: z
        .string()
        .nonempty('Password is required *')
        .min(6, { message: 'Password must be at least 6 characters *' }),
})

export type LoginForm = z.infer<typeof loginSchema>
