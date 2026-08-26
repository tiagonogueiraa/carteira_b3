<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
    CardFooter,
} from '@/components/ui/card';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Entrar" />

        <Card class="border-zinc-800 bg-zinc-900">
            <CardHeader>
                <CardTitle class="text-zinc-50">Entrar</CardTitle>
                <CardDescription>
                    Acesse sua carteira de ações e FIIs
                </CardDescription>
            </CardHeader>

            <CardContent>
                <div
                    v-if="status"
                    class="mb-4 text-sm font-medium text-emerald-400"
                >
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <p
                            v-if="form.errors.email"
                            class="text-sm text-red-400"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="password">Senha</Label>
                        <Input
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />
                        <p
                            v-if="form.errors.password"
                            class="text-sm text-red-400"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between">
                        <label
                            class="flex items-center gap-2 text-sm text-zinc-400"
                        >
                            <Checkbox
                                name="remember"
                                v-model:checked="form.remember"
                            />
                            Lembrar de mim
                        </label>

                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-zinc-400 underline hover:text-zinc-200"
                        >
                            Esqueceu a senha?
                        </Link>
                    </div>

                    <Button
                        type="submit"
                        class="w-full bg-emerald-500 text-zinc-950 hover:bg-emerald-400"
                        :disabled="form.processing"
                    >
                        Entrar
                    </Button>
                </form>
            </CardContent>

            <CardFooter class="justify-center text-sm text-zinc-400">
                Não tem conta?
                <Link
                    :href="route('register')"
                    class="ml-1 text-emerald-400 hover:underline"
                >
                    Criar conta
                </Link>
            </CardFooter>
        </Card>
    </GuestLayout>
</template>
