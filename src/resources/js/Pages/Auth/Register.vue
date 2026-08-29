<script setup>
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

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Criar conta" />

        <Card class="">
            <CardHeader>
                <CardTitle class="">Criar conta</CardTitle>
                <CardDescription>
                    Comece a acompanhar sua carteira de ações e FIIs
                </CardDescription>
            </CardHeader>

            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="name">Nome</Label>
                        <Input
                            id="name"
                            type="text"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                        />
                        <p
                            v-if="form.errors.name"
                            class="text-sm text-red-400"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
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
                            autocomplete="new-password"
                        />
                        <p
                            v-if="form.errors.password"
                            class="text-sm text-red-400"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="password_confirmation">
                            Confirmar senha
                        </Label>
                        <Input
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                        />
                        <p
                            v-if="form.errors.password_confirmation"
                            class="text-sm text-red-400"
                        >
                            {{ form.errors.password_confirmation }}
                        </p>
                    </div>

                    <Button
                        type="submit"
                        class="w-full bg-emerald-500 text-zinc-950 hover:bg-emerald-400"
                        :disabled="form.processing"
                    >
                        Criar conta
                    </Button>
                </form>
            </CardContent>

            <CardFooter class="justify-center text-sm text-zinc-400">
                Já tem conta?
                <Link
                    :href="route('login')"
                    class="ml-1 text-emerald-400 hover:underline"
                >
                    Entrar
                </Link>
            </CardFooter>
        </Card>
    </GuestLayout>
</template>
